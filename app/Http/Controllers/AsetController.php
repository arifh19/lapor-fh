<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AsetRequest;
use App\Transformers\AsetTransformer;
use Illuminate\Support\Facades\File;
use App\Aset;

class AsetController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Aset $aset,AsetTransformer $asetTransformer){
        $this->aset = $aset;
        $this->asetTransformer = $asetTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $asets = $this->asetTransformer->transformCollection($this->aset->all());
        return response()->json($asets);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    
    /**
     * Fungsi untuk menyimpan data aset
     */
    public function store(AsetRequest $request)
    {
        $user = $this->getUser();
        $this->aset->lokasi_id = $request->lokasi_id;
        $this->aset->nama = $request->nama;
        $this->aset->kuantitas = $request->kuantitas;
        $this->aset->kondisi = $request->kondisi;
        $this->aset->status = $request->status;
        $this->aset->last_updated_by = $user->id;
        if ($request->hasFile('foto')) {
            // Mengambil file yang diupload
            $uploaded_avatar = $request->file('foto');
            // Mengambil extension file
            $extension = $uploaded_avatar->getClientOriginalExtension();
            // Membuat nama file random berikut extension
            $filename = md5(time()) . "." . $extension;
            // Menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/aset';
            $uploaded_avatar->move($destinationPath, $filename);
            // Mengisi field cover di book dengan filename yang baru dibuat
            $this->aset->foto = $filename;
            $this->aset->save();
        }

        try{            
            $this->aset->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Aset berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aset = $this->aset->find($id);  
        if(empty($aset)){
            return $this->respondNotFound();
        }
        return response()->json($this->asetTransformer->transform($aset));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AsetRequest $request, $id)
    {
        $user = $this->getUser();
        $aset = $this->aset->find($id);  
        if(empty($aset)){
            return $this->respondNotFound();
        }
        $aset->lokasi_id = $request->lokasi_id;
        $aset->nama = $request->nama;
        $aset->kuantitas = $request->kuantitas;
        $aset->kondisi = $request->kondisi;
        $aset->status = $request->status;
        $aset->last_updated_by = $user->id;

        if ($request->hasFile('foto')) {
            // Mengambil proposal yang diupload berikut ektensinya
            $filename = null;
            $uploaded_upload = $request->file('foto');
            $extension = $uploaded_upload->getClientOriginalExtension();
            // Membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            // Menyimpan proposal ke folder public/proposal
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/aset';
            $uploaded_upload->move($destinationPath, $filename);
            // Hapus proposal lama, jika ada
            if ($aset->foto) {
                $old_upload = $aset->foto;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/aset' . DIRECTORY_SEPARATOR . $aset->foto;
                try {
                    File::delete($filepath);
                } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }
            // Ganti field proposal dengan proposal yang baru
            $aset->foto = $filename;
            $aset->save();
        }
        try{            
            $aset->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Aset telah diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $aset = $this->aset->find($id);
        if(empty($aset)){
            return $this->respondNotFound();
        }
        try{            
            $aset->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Reference this data']);
            }
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }
        if ($aset->foto) {
            $old_upload = $aset->foto;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/aset' . DIRECTORY_SEPARATOR . $aset->foto;
            try {
                File::delete($filepath);
            } catch (FileNotFoundException $e) {
                // File sudah dihapus/tidak ada
            }
        }

        return response()->json(['error' => false, 'message' => 'Aset telah dihapus']);
    }
}
