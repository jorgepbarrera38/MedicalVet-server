<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicHistoryInformedConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_history_informed_consents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pet_id');
            $table->foreign('pet_id')->references('id')->on('pets');
            
            $table->datetime('registration_date');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->text('procedure');
            $table->text('risks');

            $table->unsignedBigInteger('informed_consent_template_id');
            $table->foreign('informed_consent_template_id', 'clinic_history_informed_consents_template_id')->references('id')->on('informed_consent_templates');

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
        Schema::dropIfExists('clinic_history_informed_consents');
    }
}
