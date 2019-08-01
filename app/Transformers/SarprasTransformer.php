<?php

namespace App\Transformers;


class SarprasTransformer extends Transformer {

    public function transform($data)
    {        
        if(!empty($data["foto"])){
            $foto = url('images/sarpras/'.$data["foto"]);
        }else{
            $foto = "";
        }
        return [
            'id' => $data['id'],
            'gedung' => $data->lokasi['gedung'],  
            'ruangan' => $data->lokasi['ruangan'],  
            'lokasi_id' => $data['lokasi_id'],  
            'nama' => $data['nama'],  
            'kode' => $data['kode'],   
            'spesifikasi' => $data['spesifikasi'],
            'unit' => $data->unit['nama'],  
            'unit_id' => $data['unit_id'],  
            'kondisi' => $data["kondisi"],
            'status' => $data["status"],
            'foto' => $foto,
            'last_updated_by' => $data->user["name"],
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
