<?php

namespace App\Transformers;
use App\Role;

class UserTransformer extends Transformer {

    public function transform($data)
    {        
        $role_id = $data->roleUser['role_id'];
        $role = Role::where('id',$role_id)->first();
        $status =  $data['is_verified'] ? 'Aktif':'Non Aktif';

        return [
            'id' => $data['id'],
            'name' => $data['name'],  
            'email' => $data['email'],  
            'role' => $role['display_name'],  
            'status' => $status,  
            'unit' => $data->unit['nama'],  
            'unit_id' => $data['unit_id'],  
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
