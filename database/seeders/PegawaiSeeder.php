<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::create([
            'nip' => 'admin',
            'nama' => 'Administrator',
            'jabatan' => 'Administrator',
            'pangkat' => '-',
            'level' => 'Admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}
