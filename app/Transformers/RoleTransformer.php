<?php

namespace App\Transformers;


class RoleTransformer extends Transformer {

    public function transform($data)
    {        
        
        return [
            'role' => $data->role->role['name'],  
        ];
    }
}
