<?php

namespace App\Transformers;


class KomplainTransformer extends Transformer {

    public function transform($data)
    {        
        if(!empty($data["dokumentasi"])){
            $foto = url('images/komplain/'.$data["dokumentasi"]);
        }else{
            $foto = "";
        }
        return [
            'id' => $data['id'],
            'pelapor' => $data['pelapor'],  
            'identitas' => $data['identitas'],  
            'keterangan' => $data['keterangan'],  
            'nama_sarpras' => $data->sarpras['nama'],  
            'gedung_sarpras' => $data->sarpras->lokasi['gedung'],  
            'lokasi_sarpras' => $data->sarpras->lokasi['ruangan'],  
            'kondisi_sarpras' => $data->kondisi['keterangan'],  
            'estimasi' => $data->kondisi['estimasi'],  
            'status_aset' => $data->sarpras['status'],  
            'unit_id' => $data->unit["nama"],
            'dokumentasi' => $foto,
            'last_updated_by' => $data->user["name"],
            'is_check' => $data['is_check'],  
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
