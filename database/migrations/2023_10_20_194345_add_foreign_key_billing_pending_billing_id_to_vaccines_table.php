<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyBillingPendingBillingIdToVaccinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_pending_billing_id')->nullable()->after('user_id');
            $table->foreign('billing_pending_billing_id')->references('id')->on('billing_pending_billings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropForeign('billing_pending_billings');
            $table->dropColumn('billing_pending_billings');
        });
    }
}
