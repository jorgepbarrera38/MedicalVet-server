<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClinicHistoryServiceIdToHospitalizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospitalizations', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_history_service_id')->nullable()->after('user_finalized_id');
            $table->foreign('clinic_history_service_id')->references('id')->on('clinic_history_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hospitalizations', function (Blueprint $table) {
            //
        });
    }
}
