<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;
use App\Models\Owner;
use App\Models\Pet;

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
        $this->call(BasicInformationSeeder::class);  

        /*Solo en desarrollo*/
        //Departamentos
        State::create(['name' => 'Antioquia']);
        State::create(['name' => 'Cundinamarca']);

        //Ciudades
        City::create(['name' => 'Medellín', 'state_id' => 1]);
        City::create(['name' => 'Bogotá', 'state_id' => 2]);

        //Propietarios
        Owner::create([
            'identification' => '1120573514',
            'names' => 'Jorge Alexis',
            'surnames' => 'Paz Barrera',
            'address' => 'Calle 12 #16-98 B. Modelo',
            'phonenumber' => '3203719962',
            'phonenumber2' => '',
            'email' => 'digitalapp.soporte@gmail.com',
            'city_id' => 1,
            'user_id' => 1
        ]);

        //Mascotas
        Pet::create([
            'owner_id' => 1,
            'name' => 'Koby',
            'date_born' => '2020-02-20',
            'sex' => 'MACHO',
            'species_id' => 1,
            'race_id' => 5,
            'color' => 'DORADO',
            'size' => 'MEDIANO',
            'user_id' => 1,
        ]);

        /*Solo en desarrollo*/
      
    }
}
