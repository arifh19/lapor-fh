<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use App\Unit;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = new Role();
        $adminRole->name = "admin";
        $adminRole->display_name = "Admin";
        $adminRole->save();
        // Create Member role
        $teknisRole = new Role();
        $teknisRole->name = "teknis";
        $teknisRole->display_name = "Tim Teknis";
        $teknisRole->save();

        $memberRole = new Role();
        $memberRole->name = "pengguna";
        $memberRole->display_name = "Pengguna";
        $memberRole->save();

        $umum = new Unit();
        $umum->nama = "Pimpinan";
        $umum->save();
        ///
        $kebersihan = new Unit();
        $kebersihan->nama = "Kebersihan";
        $kebersihan->save();
        // Create Member role
        $unitAkademik = new Unit();
        $unitAkademik->nama = "Mahasiswa";
        $unitAkademik->save();


        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@ugm.ac.id';
        $admin->password = bcrypt('rahasia');
        $admin->unit_id = 1;
        $admin->is_verified = 1;
        $admin->save();
        $admin->attachRole($adminRole);
         // Create Sample member
        $teknis = new User();
        $teknis->name = 'Tim Teknis';
        $teknis->email = 'teknis@gmail.com';
        $teknis->password = bcrypt('rahasia');
        $teknis->unit_id = 2;
        $teknis->is_verified = 1;
        $teknis->save();
        $teknis->attachRole($teknisRole);

        $member = new User();
        $member->name = 'Mahasiswa';
        $member->email = 'member@gmail.com';
        $member->password = bcrypt('rahasia');
        $member->unit_id = 3;
        $member->is_verified = 1;
        $member->save();
        $member->attachRole($memberRole);
    }
}
