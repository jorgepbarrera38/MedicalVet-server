<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicHistoryReviewBySystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_history_review_by_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_history_id');
            $table->foreign('clinic_history_id')->references('id')->on('clinic_histories');

            $table->string('tegumentary_system_state');
            $table->text('tegumentary_system_observations')->nullable();

            $table->string('nervous_system_state');
            $table->text('nervous_system_observations')->nullable();

            $table->string('genitourinary_system_state');
            $table->text('genitourinary_system_observations')->nullable();

            $table->string('lymph_nodes_state');
            $table->text('lymph_nodes_observations')->nullable();

            $table->string('musculoskeletal_system_state');
            $table->text('musculoskeletal_system_observations')->nullable();

            $table->string('reproductive_system_state');
            $table->text('reproductive_system_observations')->nullable();

            $table->string('digestive_system_state');
            $table->text('digestive_system_observations')->nullable();

            $table->string('oral_cavity_state');
            $table->text('oral_cavity_observations')->nullable();

            

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
        Schema::dropIfExists('clinic_history_review_by_systems');
    }
}
