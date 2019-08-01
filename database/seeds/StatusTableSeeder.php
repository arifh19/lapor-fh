<?php

use Illuminate\Database\Seeder;
use App\Kondisi;
use App\Status;
class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kondisi = new Kondisi;
        $kondisi->keterangan = 'Baik';
        $kondisi->save();

        $kondisi = new Kondisi;
        $kondisi->keterangan = 'Rusak Ringan';
        $kondisi->save();

        $kondisi = new Kondisi;
        $kondisi->keterangan = 'Rusak Sedang';
        $kondisi->save();

        $kondisi = new Kondisi;
        $kondisi->keterangan = 'Rusak Berat';
        $kondisi->save();


        $status = new Status;
        $status->keterangan = 'Terpasang';
        $status->save();

        $status = new Status;
        $status->keterangan = 'diperbaiki';
        $status->save();

        $status = new Status;
        $status->keterangan = 'diganti';
        $status->save();
    }
}
