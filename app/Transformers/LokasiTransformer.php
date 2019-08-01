<?php

namespace App\Transformers;


class LokasiTransformer extends Transformer {

    public function transform($data)
    {        
        return [
            'id' => $data['id'],
            'gedung' => $data['gedung'],  
            'ruangan' => $data['ruangan'],  
            'last_updated_by' => $data->user["name"],
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
