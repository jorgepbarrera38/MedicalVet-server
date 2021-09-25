<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRespiratorySystemToClinicHistoryReviewBySystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_review_by_systems', function (Blueprint $table) {
            $table->string('respiratory_system_state')->default('false');
            $table->text('respiratory_system_observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_review_by_systems', function (Blueprint $table) {
            //
        });
    }
}
