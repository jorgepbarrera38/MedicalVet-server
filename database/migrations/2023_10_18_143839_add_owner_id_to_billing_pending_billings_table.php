<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerIdToBillingPendingBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_pending_billings', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->after('quantity')->nullable();
            $table->foreign('owner_id')->references('id')->on('owners');
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
            $table->dropColumn('owner_id');
        });
    }
}
