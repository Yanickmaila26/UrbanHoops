<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Yanick Maila',
            'email' => 'aetherin12@gmail.com',
            'password' => Hash::make('secreto123'),
        ]);
        User::create([
            'name' => 'Jorge Corrales',
            'email' => 'lucho19982005@hotmail.com',
            'password' => Hash::make('secreto123'),
        ]);
        User::create([
            'name' => 'Mateo Pozo',
            'email' => 'mateopozo240@gmail.com',
            'password' => Hash::make('secreto123'),
        ]);
        User::create([
            'name' => 'Esteban GarciaS',
            'email' => 'estebangarciaojeda@gmail.com',
            'password' => Hash::make('secreto123'),
        ]);
        $this->call(ProveedorSeeder::class);
        $this->call(ProductoSeeder::class);
    }
}
