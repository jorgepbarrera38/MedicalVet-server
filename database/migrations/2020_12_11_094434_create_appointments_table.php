<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->foreign('pet_id')->references('id')->on('pets');    
            $table->unsignedBigInteger('consultingroom_id');
            $table->foreign('consultingroom_id')->references('id')->on('consultingrooms');
            $table->unsignedBigInteger('activity_id');
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->datetime('date_time_start');
            $table->integer('duration');
            $table->datetime('date_time_finish');
            $table->text('observations')->nullable();
            $table->enum('state', ['PENDIENTE', 'CUMPLIDA', 'INCUMPLIDA', 'CANCELADA'])->default('PENDIENTE');
            //Usuario que crea la cita
            $table->unsignedBigInteger('user_id_creates')->nullable();
            $table->foreign('user_id_creates')->references('id')->on('users');
            //Usuario que cumple la cita
            $table->unsignedBigInteger('user_id_fulfill')->nullable();
            $table->foreign('user_id_fulfill')->references('id')->on('users');
            //Usuario que incumple la cita
            $table->unsignedBigInteger('user_id_unfulfill')->nullable();
            $table->foreign('user_id_unfulfill')->references('id')->on('users');
            //Usuario que cancela la cita
            $table->unsignedBigInteger('user_id_cancel')->nullable();
            $table->foreign('user_id_cancel')->references('id')->on('users');

            $table->unsignedBigInteger('reason_cancellation_id')->nullable();
            $table->foreign('reason_cancellation_id')->references('id')->on('reason_cancellations');

            //Folio desde el que se cumple la cita
            $table->unsignedBigInteger('clinic_history_id')->nullable();
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
        Schema::dropIfExists('appointments');
    }
}
