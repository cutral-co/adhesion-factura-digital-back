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
            'cuit' => '20999999991',
            'password' => Hash::make('20999999991'),
        ]);
        DB::table('barrios')->insert(['name' => 'Centro']);
        DB::table('barrios')->insert(['name' => 'Belgrano']);
        DB::table('barrios')->insert(['name' => 'San Martín']);

        DB::table('estados')->insert(['name' => 'Nuevo']);
        /* \App\Models\Solicitud::factory(10)->create(); */
    }
}
