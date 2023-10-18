<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetIdToBillingPendingBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_pending_billings', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->after('quantity')->nullable();
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
        Schema::table('billing_pending_billings', function (Blueprint $table) {
            $table->dropColumn('pet_id');
        });
    }
}
