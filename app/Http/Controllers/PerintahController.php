<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PerintahRequest;
use App\Http\Requests\LaporanPerintahRequest;
use App\Http\Requests\TindakPerintahRequest;
use App\Transformers\PerintahTransformer;
use Illuminate\Support\Facades\File;
use App\Perintah;
use App\RiwayatPerintah;
use Laratrust\LaratrustFacade as Laratrust;

class PerintahController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Perintah $perintah,PerintahTransformer $perintahTransformer){
        $this->perintah = $perintah;
        $this->perintahTransformer = $perintahTransformer;
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
            $perintahs = $this->perintahTransformer->transformCollection($this->perintah->where('user_id', $user->id)->get());
            return response()->json($perintahs);
        }else if(Laratrust::hasRole('teknis')) {
            $perintahs = $this->perintahTransformer->transformCollection($this->perintah->where('unit_id',$user->unit_id)->get());
            return response()->json($perintahs);
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
    public function store(PerintahRequest $request)
    {
        if ((Laratrust::hasRole('admin'))) {

            $user = $this->getUser();
            $this->perintah->keterangan = $request->keterangan;
            $this->perintah->unit_id = $request->unit_id;
            $this->perintah->status = 'Menunggu konfirmasi';
            $this->perintah->user_id = $user->id;
            $this->perintah->last_updated_by = $user->id;
            try{            
                $this->perintah->save();
            }catch(\Exception $e){
                return response()->json(['error' => true, 'message' => $e->getMessage()]);
            }

            return response()->json(['error' => false, 'message' => 'Perintah berhasil dikirim']);
        }
        else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $perintah = $this->perintah->find($id);  
        if(empty($perintah)){
            return $this->respondNotFound();
        }
        return response()->json($this->perintahTransformer->transform($perintah));
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
    public function update(PerintahRequest $request, $id)
    {
        if ((Laratrust::hasRole('admin'))) {
            $user = $this->getUser();

            $perintah = $this->perintah->find($id);  
            if(empty($perintah)){
                return $this->respondNotFound();
            }
            $perintah->keterangan = $request->keterangan;
            $perintah->unit_id = $request->unit_id;
            $perintah->status = 'Menunggu konfirmasi';
            $perintah->user_id = $user->id;
            $perintah->last_updated_by = $user->id;
            
            try{            
                $perintah->save();
            }catch(\Exception $e){
                if($e->getCode() == "23000"){
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }
            return response()->json(['error' => false, 'message' => 'Perintah telah diperbaharui']);
        }else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
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
            $perintah = $this->perintah->find($id);
            if (empty($perintah)) {
                return $this->respondNotFound();
            }
            try {
                $perintah->delete();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }
    
            return response()->json(['error' => false, 'message' => 'Perintah telah dihapus']);
        }
        else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
    }

    public function tindak(TindakPerintahRequest $request, $id)
    {
        if ((Laratrust::hasRole('teknis'))) {
            $user = $this->getUser();
            $perintah = $this->perintah->find($id);
            if (empty($perintah)) {
                return $this->respondNotFound();
            }
            $perintah->status = $request->status;
            $perintah->last_updated_by = $user->id;

            try {
                $perintah->save();
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
    public function laporan(LaporanPerintahRequest $request, $id)
    {
        if ((Laratrust::hasRole('teknis'))) {

            $user = $this->getUser();
            $perintah = $this->perintah->find($id);  
            if(empty($perintah)){
                return $this->respondNotFound();
            }
            $riwayat = new RiwayatPerintah;
            $perintah->status = $request->status;
            $perintah->last_updated_by = $user->id;
            $riwayat->perintah_id = $perintah->id;
            $riwayat->unit_id = $perintah->unit_id;
            $riwayat->user_id = $perintah->user_id;
            $riwayat->laporan = $request->laporan;

            if ($request->hasFile('dokumentasi')) {
                // Mengambil file yang diupload
                $uploaded_avatar = $request->file('dokumentasi');
                // Mengambil extension file
                $extension = $uploaded_avatar->getClientOriginalExtension();
                // Membuat nama file random berikut extension
                $filename = md5(time()) . "." . $extension;
                // Menyimpan cover ke folder public/img
                $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'images/riwayatperintah';
                $uploaded_avatar->move($destinationPath, $filename);
                // Mengisi field cover di book dengan filename yang baru dibuat
                $riwayat->dokumentasi = $filename;
                $riwayat->save();
            }
            try{            
                $perintah->save();
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
}
