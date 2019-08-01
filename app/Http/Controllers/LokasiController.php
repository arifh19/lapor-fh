<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LokasiRequest;
use App\Transformers\LokasiTransformer;
use App\Lokasi;
use Laratrust\LaratrustFacade as Laratrust;

class LokasiController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Lokasi $lokasi,LokasiTransformer $lokasiTransformer){
        $this->lokasi = $lokasi;
        $this->lokasiTransformer = $lokasiTransformer;
    }

    public function searchByName(Request $request,$name){
        $lokasis = $this->lokasi->where('gedung', 'like', '%' . $name . '%')->get(); 
        return  response()->json([
            'data' =>  $this->lokasiTransformer->transformCollection($lokasis)
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lokasis = $this->lokasiTransformer->transformCollection($this->lokasi->all());
        return response()->json($lokasis);
    }
    public function getGedung()
    {
        $lokasis = $this->lokasi->select('gedung')->groupBy('gedung')->get(); 
        //$lokasis = $this->lokasiTransformer->transformCollection($this->lokasi->groupBy('gedung')->get());
        return response()->json($lokasis);
    }
    public function getRuangan()
    {
        $lokasis = $this->lokasiTransformer->transformCollection($this->lokasi->all());
        return response()->json($lokasis);
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
    /**
     * Fungsi untuk menyimpan data lokasi 
     */
    public function store(LokasiRequest $request)
    {
        $user = $this->getUser();
        $this->lokasi->gedung = $request->gedung;
        $this->lokasi->ruangan = $request->ruangan;
        $this->lokasi->last_updated_by = $user->id;;
        try{            
            $this->lokasi->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Lokasi berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(LokasiRequest $request, $id)
    {
        $user = $this->getUser();
        $lokasi = $this->lokasi->find($id);  
        if(empty($lokasi)){
            return $this->respondNotFound();
        }
        $lokasi->gedung = $request->gedung;
        $lokasi->ruangan = $request->ruangan;
        $lokasi->last_updated_by = $user->id;;
        try{            
            $lokasi->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Lokasi telah diperbaharui']);
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
            $lokasi = $this->lokasi->find($id);
            if (empty($lokasi)) {
                return $this->respondNotFound();
            }
            try {
                $lokasi->delete();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }
    
            return response()->json(['error' => false, 'message' => 'Lokasi telah dihapus']);
        }
        else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
    }
}
