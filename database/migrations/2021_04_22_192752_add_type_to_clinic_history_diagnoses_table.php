<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToClinicHistoryDiagnosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_diagnoses', function (Blueprint $table) {
            $table->enum('type', ['Presuntivo', 'Definitivo'])->after('diagnosis_id')->nullable();
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
