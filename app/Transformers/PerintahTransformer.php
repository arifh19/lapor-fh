<?php

namespace App\Transformers;


class PerintahTransformer extends Transformer {

    public function transform($data)
    {        
        
        return [
            'id' => $data['id'],
            'keterangan' => $data['keterangan'],  
            'status' => $data['status'],  
            'unit_id' => $data->unit["nama"],
            'pengirim' => $data->pengirim["name"],
            'diperbaharui_oleh' => $data->user["name"],
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
