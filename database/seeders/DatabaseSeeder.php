<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrador',
            'cuil' => '00123456780',
            'password' => Hash::make('admin'),
        ]);
        DB::table('barrios')->insert(['name' => 'Centro']);
        DB::table('barrios')->insert(['name' => 'Belgrano']);
        DB::table('barrios')->insert(['name' => 'San Martín']);

        DB::table('estados')->insert(['name' => 'Nuevo']);
        /* \App\Models\Solicitud::factory(10)->create(); */
    }
}
