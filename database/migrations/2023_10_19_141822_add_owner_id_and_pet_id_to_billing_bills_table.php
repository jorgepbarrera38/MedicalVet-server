<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerIdAndPetIdToBillingBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->after('cancelled');
            $table->foreign('owner_id')->references('id')->on('owners');

            $table->unsignedBigInteger('pet_id')->after('owner_id');
            $table->foreign('pet_id')->references('id')->on('pets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_bills', function (Blueprint $table) {
            $table->dropColumn('owner_id');
            $table->dropColumn('pet_id');
        });
    }
}
