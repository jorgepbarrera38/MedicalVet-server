<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysiologicalConstantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physiological_constants', function (Blueprint $table) {
            $table->id();
            $table->float('weight'); //Peso
            $table->float('temperature');
            $table->float('heart_rate'); //Frecuencia cardíaca
            $table->float('breathing_frequency'); //Frecuencia respiratoria
            $table->unsignedBigInteger('clinic_history_id');
            $table->foreign('clinic_history_id')->references('id')->on('clinic_histories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physiological_constants');
    }
}
