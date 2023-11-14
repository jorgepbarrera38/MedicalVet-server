<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultingroom;
use App\Models\ReasonCancellation;

class BasicInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Consultingroom::create(['name' => 'Consultorio 1']);

        ReasonCancellation::create(['name' => 'Cancelación voluntaria']);
        ReasonCancellation::create(['name' => 'Inasistencia']);
        ReasonCancellation::create(['name' => 'Reprogramación']);
        
    }
}
