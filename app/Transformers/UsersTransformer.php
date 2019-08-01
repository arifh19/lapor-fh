<?php

namespace App\Transformers;

class UsersTransformer extends Transformer {

    public function transform($data)
    {        
        return [
            'id' => $data['id'],
            'name' => $data['name'],  
            'email' => $data['email'],  
            'unit_id' => $data->unit['nama'],  
            'role' => $data->role->role['name'],  
        ];
    }
}
