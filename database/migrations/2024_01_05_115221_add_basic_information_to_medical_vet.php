<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Race;
use App\Models\Species;
use App\Models\Consultingroom;
use App\Models\ReasonCancellation;
use App\Models\Service;
use App\Models\Activity;

class AddBasicInformationToMedicalVet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Razas
        Race::create(['name' => 'Chihuahua', 'user_id' => 1]);
        Race::create(['name' => 'Beagle', 'user_id' => 1]);
        Race::create(['name' => 'Border Collie', 'user_id' => 1]);
        Race::create(['name' => 'Schnauzer ', 'user_id' => 1]);
        Race::create(['name' => 'Labrador ', 'user_id' => 1]);
        Race::create(['name' => 'Pastor Alemán', 'user_id' => 1]);
        Race::create(['name' => 'Bulldog Francés', 'user_id' => 1]);
        Race::create(['name' => 'Pinscher', 'user_id' => 1]);

        //Especies
        Species::create(['name' => 'Canino', 'user_id' => 1]);
        Species::create(['name' => 'Felino', 'user_id' => 1]);

        //Consultorio
        Consultingroom::create(['name' => 'Consultorio 1']);

        //Motivos de cancelación
        ReasonCancellation::create(['name' => 'Cancelación voluntaria']);
        ReasonCancellation::create(['name' => 'Cancelación por reprogramación']);

        //Servicios
        Service::create(['code' => 'S0001', 'name' => 'Baño y peluquería', 'price' => '60000', 'user_id' => 1]);
        Service::create(['code' => 'S0002', 'name' => 'Consulta general', 'price' => '30000', 'user_id' => 1]);
        
        //Actividades
        Activity::create(['name' => 'Baño y peluquería', 'duration' => '60', 'service_id' => 1]);
        Activity::create(['name' => 'Consulta general', 'duration' => '25', 'service_id' => 2]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
