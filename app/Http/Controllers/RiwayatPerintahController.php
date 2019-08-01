<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laratrust\LaratrustFacade as Laratrust;
use App\RiwayatPerintah;
use App\Transformers\RiwayatPerintahTransformer;

class RiwayatPerintahController extends Controller
{
    public function __construct(RiwayatPerintah $riwayat,RiwayatPerintahTransformer $riwayatTransformer){
        $this->riwayat = $riwayat;
        $this->riwayatTransformer = $riwayatTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ((Laratrust::hasRole('admin'))||(Laratrust::hasRole('teknis'))) {
            $riwayats = $this->riwayatTransformer->transformCollection($this->riwayat->all());
            return response()->json($riwayats);
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
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
