<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultation_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('symptoms')->default(false);
            $table->boolean('indications')->default(false);
            $table->boolean('records')->default(false);
            $table->boolean('physiological_constants')->default(false);
            $table->boolean('diagnoses')->default(false);
            $table->boolean('vaccines')->default(false);
            $table->boolean('dewormings')->default(false);
            $table->boolean('products_and_services_applied')->default(false);


            //Usuario que crea
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('consultation_purposes');
    }
}
