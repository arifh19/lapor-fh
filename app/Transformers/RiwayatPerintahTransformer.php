<?php

namespace App\Transformers;


class RiwayatPerintahTransformer extends Transformer {

    public function transform($data)
    {        
        if(!empty($data["dokumentasi"])){
            $foto = url('images/riwayatperintah/'.$data["dokumentasi"]);
        }else{
            $foto = "";
        }
        return [
            'unit_id' => $data->unit["nama"],
            'laporan' => $data["laporan"],
            'dokumentasi' => $foto,
            'pengirim' => $data->pengirim["name"],
            'last_updated_by' => $data->perintah->user['name'],

            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
