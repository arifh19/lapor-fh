<?php

namespace App\Transformers;


class AsetTransformer extends Transformer {

    public function transform($data)
    {        
        if(!empty($data["foto"])){
            $foto = url('images/aset/'.$data["foto"]);
        }else{
            $foto = "";
        }
        return [
            'id' => $data['id'],
            'lokasi' => $data->lokasi['nama'],  
            'nama' => $data['nama'],  
            'kode' => $data['kode'],  
            'spesifikasi' => $data['spesifikasi'],
            'unit' => $data->unit['nama'],  
            'kondisi' => $data["kondisi"],
            'status' => $data["status"],
            'foto' => $foto,
            'last_updated_by' => $data->user["name"],
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
