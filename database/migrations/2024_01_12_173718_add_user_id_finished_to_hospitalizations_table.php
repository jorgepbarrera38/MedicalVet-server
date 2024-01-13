<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdFinishedToHospitalizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospitalizations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_finalized_id')->nullable()->after('user_id');
            $table->foreign('user_finalized_id')->references('id')->on('users');
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
