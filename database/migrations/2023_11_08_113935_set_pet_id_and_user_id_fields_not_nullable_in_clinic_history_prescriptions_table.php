<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetPetIdAndUserIdFieldsNotNullableInClinicHistoryPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable(false)->change();
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
}
