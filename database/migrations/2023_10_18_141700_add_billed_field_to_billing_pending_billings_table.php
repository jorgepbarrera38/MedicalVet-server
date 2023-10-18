<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBilledFieldToBillingPendingBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_pending_billings', function (Blueprint $table) {
            $table->boolean('billed')->default(false)->after('pet_id');
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
            $table->dropColumn('billed');
        });
    }
}
