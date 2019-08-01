<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UnitRequest;
use App\Transformers\UnitTransformer;
use App\Unit;
use Laratrust\LaratrustFacade as Laratrust;

class UnitController extends Controller
{
    /**
     * Contructor
     */
    public function __construct(Unit $unit,UnitTransformer $unitTransformer){
        $this->unit = $unit;
        $this->unitTransformer = $unitTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = $this->unitTransformer->transformCollection($this->unit->all());
        return response()->json($units);
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
     * Fungsi untuk menyimpan data unit 
     */
    public function store(UnitRequest $request)
    {
        $this->unit->nama = $request->nama;
        try{            
            $this->unit->save();
        }catch(\Exception $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'message' => 'Unit berhasil ditambahkan']);
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
    public function update(Request $request, $id)
    {
        $unit = $this->unit->find($id);  
        if(empty($unit)){
            return $this->respondNotFound();
        }
        $unit->nama = $request->nama;
        try{            
            $unit->save();
        }catch(\Exception $e){
            if($e->getCode() == "23000"){
                return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
            }
            
            return response()->json(['error' => true, 'message' => 'There is problem on server']);
        }

        return response()->json(['error' => false, 'message' => 'Unit telah diperbaharui']);
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
            $unit = $this->unit->find($id);
            if (empty($unit)) {
                return $this->respondNotFound();
            }
            try {
                $unit->delete();
            } catch (\Exception $e) {
                if ($e->getCode() == "23000") {
                    return response()->json(['error' => true, 'message' => 'Failed, Other Data Refrence this data']);
                }
                return response()->json(['error' => true, 'message' => 'There is problem on server']);
            }
    
            return response()->json(['error' => false, 'message' => 'Unit telah dihapus']);
        }
        else{
            return response()->json(['error' => true, 'message' => 'Akses tidak diizinkan']);
        }
    }
}
