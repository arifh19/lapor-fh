<?php

namespace App\Transformers;


class RiwayatAsetTransformer extends Transformer {

    public function transform($data)
    {        
        if(!empty($data["dokumentasi"])){
            $foto = url('images/riwayataset/'.$data["dokumentasi"]);
        }else{
            $foto = "";
        }
        return [
            'nama_aset' => $data->aset['nama'],  
            'lokasi_aset' => $data->aset->lokasi['nama'],  
            'kondisi_aset' => $data->aset['kondisi'],  
            'unit_id' => $data->unit["nama"],
            'laporan' => $data["laporan"],
            'dokumentasi' => $foto,
            'last_updated_by' => $data->user["name"],
            'created_at' => $data->getReadableCreatedAt(),
            'updated_at' => $data->getReadableUpdatedAt(), 
        ];
    }
}
