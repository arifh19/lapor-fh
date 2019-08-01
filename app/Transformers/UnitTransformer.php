<?php

namespace App\Transformers;


class UnitTransformer extends Transformer {

    public function transform($data)
    {        
        return [
            'id' => $data['id'],
            'nama' => $data['nama'],  
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
