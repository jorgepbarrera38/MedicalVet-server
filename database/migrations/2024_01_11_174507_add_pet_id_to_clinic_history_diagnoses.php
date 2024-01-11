<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetIdToClinicHistoryDiagnoses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_diagnoses', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable()->after('clinic_history_id');
            $table->foreign('pet_id')->references('id')->on('pets');
        });
        
        \DB::statement("UPDATE clinic_history_diagnoses
                        JOIN clinic_histories ON clinic_history_diagnoses.clinic_history_id = clinic_histories.id
                        SET clinic_history_diagnoses.pet_id = clinic_histories.pet_id");

        Schema::table('clinic_history_diagnoses', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable(false)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_diagnoses', function (Blueprint $table) {
            //
        });
    }
}
