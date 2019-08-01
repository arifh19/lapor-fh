<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SarprasRequest;
use App\Transformers\SarprasTransformer;
use Illuminate\Support\Facades\File;
use App\Sarpras;
use App\Status;
use App\RiwayatSarpras;
use App\Kondisi;

class SarprasController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Sarpras $sarpras,SarprasTransformer $sarprasTransformer){
        $this->sarpras = $sarpras;
        $this->sarprasTransformer = $sarprasTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sarprass = $this->sarprasTransformer->transformCollection($this->sarpras->orderBy('kode','asc')->get());
        return response()->json($sarprass);
    }

    public function getKondisi()
    {
        $kondisi = Kondisi::select('id','keterangan')->get();
        return response()->json($kondisi);
    }

    public function getStatus()
    {
        $status = Status::select('id','keterangan')->get();
        return response()->json($status);
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

    public function searchByLokasi(Request $request,$name){
        $sarprass = $this->sarpras->where('lokasi_id', 'like', '%' . $name . '%')->get(); 
        return  response()->json([
            'data' =>  $this->sarprasTransformer->transformCollection($sarprass)
        ]);
    }
    /**
     * Fungsi untuk menyimpan data sarpras
     */
    public function store(SarprasRequest $request)
    {
        $user = $this->getUser();
        $this->sarpras->lokasi_id = $request->lokasi_id;
        $this->sarpras->nama = $request->nama;
        $this->sarpras->spesifikasi = $request->spesifikasi;
        $this->sarpras->kode = $request->kode;
        $this->sarpras->unit_id = $request->unit_id;
        $this->sarpras->kondisi = $request->kondisi;
        $this->sarpras->status = 'Terpasang';
        $this->sarpras->last_updated_by = $user->id;
        
        
        if ($request->hasFile('foto')) {
            // Mengambil file yang diupload
            $uploaded_avatar = $request->file('foto');
            // Mengambil extension file
            $extension = $uploaded_avatar->getClientOriginalExtension();
            // Membuat nama file random berikut extension
            $filename = md5(time()) . "." . $extension;
            // Menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/sarpras';
            $uploaded_avatar->move($destinationPath, $filename);
            // Mengisi field cover di book dengan filename yang baru dibuat
            $this->sarpras->foto = $filename;
            $this->sarpras->save();
        }

        try{            
            $this->sarpras->save();
            $riwayat = new RiwayatSarpras;
            $riwayat->sarpras_id = $this->sarpras->id;
            $riwayat->unit_id = $user->unit_id;
            $riwayat->user_id = $user->id;
            $riwayat->laporan = $this->sarpras->kondisi;
            $riwayat->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Sarpras berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sarpras = $this->sarpras->find($id);  
        if(empty($sarpras)){
            return $this->respondNotFound();
        }
        return response()->json($this->sarprasTransformer->transform($sarpras));
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
    public function update(SarprasRequest $request, $id)
    {
        $user = $this->getUser();
        $sarpras = $this->sarpras->find($id);  
        if(empty($sarpras)){
            return $this->respondNotFound();
        }
        $sarpras->lokasi_id = $request->lokasi_id;
        $sarpras->nama = $request->nama;
        $sarpras->spesifikasi = $request->spesifikasi;
        $sarpras->kode = $request->kode;
        $sarpras->unit_id = $request->unit_id;
        $sarpras->kondisi = $request->kondisi;
        $sarpras->last_updated_by = $user->id;

        if ($request->hasFile('foto')) {
            // Mengambil proposal yang diupload berikut ektensinya
            $filename = null;
            $uploaded_upload = $request->file('foto');
            $extension = $uploaded_upload->getClientOriginalExtension();
            // Membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            // Menyimpan proposal ke folder public/proposal
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/sarpras';
            $uploaded_upload->move($destinationPath, $filename);
            // Hapus proposal lama, jika ada
            if ($sarpras->foto) {
                $old_upload = $sarpras->foto;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/sarpras' . DIRECTORY_SEPARATOR . $sarpras->foto;
                try {
                    File::delete($filepath);
                } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }
            // Ganti field proposal dengan proposal yang baru
            $sarpras->foto = $filename;
            $sarpras->save();
        }
        try{            
            $sarpras->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Sarpras telah diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sarpras = $this->sarpras->find($id);
        if(empty($sarpras)){
            return $this->respondNotFound();
        }
        try{            
            $sarpras->delete();
        }catch(\Exception $e){
             if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Reference this data']);
            }
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }
        if ($sarpras->foto) {
            $old_upload = $sarpras->foto;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/sarpras' . DIRECTORY_SEPARATOR . $sarpras->foto;
            try {
                File::delete($filepath);
            } catch (FileNotFoundException $e) {
                // File sudah dihapus/tidak ada
            }
        }

        return response()->json(['error' => false, 'message' => 'Sarpras telah dihapus']);
    }
}
