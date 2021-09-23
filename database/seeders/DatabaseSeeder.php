<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
        
        //Usuarios
        
        $user = \App\Models\User::create([
            'username' => 'admin',
            'identification' => '1',
            'names' => 'Administrador',
            'surnames' => 'Usuario',
            'is_vet' => true,
            'password' => bcrypt(123)
            ]);
            


        $user->permissions()->attach([71,72,73,74,75]);
        
        /*
        \App\Models\Owner::factory()->count(400)->create();

        \App\Models\Species::factory()->count(100)->create();
        \App\Models\Race::factory()->count(200)->create();      

        \App\Models\Pet::factory()->count(300)->create();
        
        \App\Models\ConsultationPurposes::factory()->count(40)->create();

        \App\Models\Diagnosis::factory()->count(500)->create();
        
        \App\Models\Product::factory()->count(500)->create();

        \App\Models\Service::factory()->count(500)->create();

        \App\Models\Unity::factory()->count(30)->create();*/
        
    }
}
