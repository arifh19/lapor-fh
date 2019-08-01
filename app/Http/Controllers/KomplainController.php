<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\KomplainRequest;
use App\Http\Requests\LaporanRequest;
use App\Http\Requests\TindaklanjutRequest;
use App\Http\Requests\DisposisiRequest;
use App\Transformers\KomplainTransformer;
use Illuminate\Support\Facades\File;
use App\Komplain;
use App\RiwayatSarpras;
use App\Sarpras;
use Laratrust\LaratrustFacade as Laratrust;


class KomplainController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Komplain $komplain,KomplainTransformer $komplainTransformer){
        $this->komplain = $komplain;
        $this->komplainTransformer = $komplainTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getUser();
        if (Laratrust::hasRole('admin')) {
            $komplains = $this->komplainTransformer->transformCollection($this->komplain->all());
            return response()->json($komplains);
        }else if(Laratrust::hasRole('teknis')) {
            $komplains = $this->komplainTransformer->transformCollection($this->komplain->where('unit_id',$user->unit_id)->get());
            return response()->json($komplains);
        }else if (Laratrust::hasRole('pengguna')) {
            $komplains = $this->komplainTransformer->transformCollection($this->komplain->where('user_id', $user->id)->get());
            return response()->json($komplains);
        }
        else{
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki izin']);
        }
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KomplainRequest $request)
    {
        $user = $this->getUser();
        $sarpras = Sarpras::find($request->sarpras_id);
        $this->komplain->pelapor = $request->pelapor;
        $this->komplain->identitas = $request->identitas;
        $this->komplain->sarpras_id = $request->sarpras_id;
        $this->komplain->unit_id = $sarpras->unit_id;
        $this->komplain->keterangan = $request->keterangan;
        $this->komplain->user_id = $user->id;
        $this->komplain->kondisi_id = $request->kondisi_id;

        if ($request->hasFile('dokumentasi')) {
            // Mengambil file yang diupload
            $uploaded_avatar = $request->file('dokumentasi');
            // Mengambil extension file
            $extension = $uploaded_avatar->getClientOriginalExtension();
            // Membuat nama file random berikut extension
            $filename = md5(time()) . "." . $extension;
            // Menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/komplain';
            $uploaded_avatar->move($destinationPath, $filename);
            // Mengisi field cover di book dengan filename yang baru dibuat
            $this->komplain->dokumentasi = $filename;
            $this->komplain->save();
        }

        try{            
            $this->komplain->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Komplain berhasil dikirim']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $komplain = $this->komplain->find($id);  
        if(empty($komplain)){
            return $this->respondNotFound();
        }
        return response()->json($this->komplainTransformer->transform($komplain));
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
    public function update(KomplainRequest $request, $id)
    {
        $user = $this->getUser();
        $komplain = $this->komplain->find($id);  
        if(empty($komplain)){
            return $this->respondNotFound();
        }
        $aset = Aset::find($request->aset_id);
        $komplain->pelapor = $request->pelapor;
        $komplain->identitas = $request->identitas;
        $komplain->aset_id = $request->aset_id;
        $komplain->unit_id = $request->unit_id;
        $komplain->keterangan = $request->keterangan;
        $komplain->user_id = $user->id;
        $aset->kondisi = $request->kondisi;

        if ($request->hasFile('dokumentasi')) {
            // Mengambil proposal yang diupload berikut ektensinya
            $filename = null;
            $uploaded_upload = $request->file('dokumentasi');
            $extension = $uploaded_upload->getClientOriginalExtension();
            // Membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            // Menyimpan proposal ke folder public/proposal
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/komplain';
            $uploaded_upload->move($destinationPath, $filename);
            // Hapus proposal lama, jika ada
            if ($komplain->dokumentasi) {
                $old_upload = $komplain->dokumentasi;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/komplain' . DIRECTORY_SEPARATOR . $komplain->dokumentasi;
                try {
                    File::delete($filepath);
                } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }
            // Ganti field proposal dengan proposal yang baru
            $komplain->dokumentasi = $filename;
            $komplain->save();
        }
        try{            
            $komplain->save();
            $aset->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Komplain telah diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ((Laratrust::hasRole('admin'))) {
            $komplain = $this->komplain->find($id);
            if (empty($komplain)) {
                return $this->respondNotFound();
            }
            try {
                $komplain->delete();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Reference this data']);
                }
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }
            if ($komplain->dokumentasi) {
                $old_upload = $komplain->dokumentasi;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'images/komplain' . DIRECTORY_SEPARATOR . $komplain->dokumentasi;
                try {
                    File::delete($filepath);
                } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                }
            }
            return response()->json(['error' => false, 'message' => 'Komplain telah dihapus']);
        }else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
    }

    public function tindak(TindaklanjutRequest $request, $id)
    {
        if ((Laratrust::hasRole('admin'))||(Laratrust::hasRole('teknis'))) {
            $user = $this->getUser();
            $komplain = $this->komplain->find($id);
            if (empty($komplain)) {
                return $this->respondNotFound();
            }
            $sarpras = Sarpras::find($komplain->sarpras_id);
            $komplain->keterangan = $request->keterangan;
            $komplain->is_check = 1;
            $komplain->user_id = $user->id;
            $sarpras->status = $request->status;
            $riwayat = new RiwayatSarpras;
            $riwayat->sarpras_id = $komplain->sarpras_id;
            $riwayat->unit_id = $user->unit_id;
            $riwayat->user_id = $user->id;
            $riwayat->laporan = $request->keterangan;

            try {
                $komplain->save();
                $sarpras->save();
                $riwayat->save();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }

            return response()->json(['error' => false, 'message' => 'Tindaklanjut terkirim']);
        }else{
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki izin']);
        }
    }

    public function laporan(LaporanRequest $request, $id)
    {
        if ((Laratrust::hasRole('admin'))||(Laratrust::hasRole('teknis'))) {

            $user = $this->getUser();
            $komplain = $this->komplain->find($id);  
            if(empty($komplain)){
                return $this->respondNotFound();
            }
            $sarpras = Sarpras::find($komplain->sarpras_id);
            $riwayat = new RiwayatSarpras;
            $komplain->keterangan = $request->laporan;
            $komplain->user_id = $user->id;
            $sarpras->status = $request->status;
            $sarpras->kondisi = $request->kondisi;
            $riwayat->sarpras_id = $komplain->sarpras_id;
            $riwayat->unit_id = $user->unit_id;
            $riwayat->user_id = $user->id;
            $riwayat->laporan = $request->laporan;

            if ($request->hasFile('dokumentasi')) {
                // Mengambil file yang diupload
                $uploaded_avatar = $request->file('dokumentasi');
                // Mengambil extension file
                $extension = $uploaded_avatar->getClientOriginalExtension();
                // Membuat nama file random berikut extension
                $filename = md5(time()) . "." . $extension;
                // Menyimpan cover ke folder public/img
                $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/riwayataset';
                $uploaded_avatar->move($destinationPath, $filename);
                // Mengisi field cover di book dengan filename yang baru dibuat
                $riwayat->dokumentasi = $filename;
                $riwayat->save();
            }
            try{            
                $komplain->save();
                $sarpras->save();
                $riwayat->save();

            }catch(\Exception $e){
                if($e->getCode() == "23000"){
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }

            return response()->json(['error' => false, 'message' => 'Laporan telah terkirim']);
        }else{
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki izin']);
        }
    }

    public function disposisi(DisposisiRequest $request, $id)
    {
        if (Laratrust::hasRole('admin')) {
            $user = $this->getUser();
            $komplain = $this->komplain->find($id);
            if (empty($komplain)) {
                return $this->respondNotFound();
            }
            $komplain->unit_id = $request->unit_id;
            $komplain->user_id = $user->id;
            try {
                $komplain->save();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }

            return response()->json(['error' => false, 'message' => 'Disposisi terkirim']);
        }else{
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki izin']);
        }
    }
}
