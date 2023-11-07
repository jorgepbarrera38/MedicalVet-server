<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillAuthorizationColumnToBillingBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_bills', function (Blueprint $table) {
            $table->string('prefix')->nullable()->after('date');
            $table->string('prefix_and_number_bill')->after('number_bill');
            $table->unsignedBigInteger('bill_billing_authorization_detail_id')->after('pet_id');
            $table->foreign('bill_billing_authorization_detail_id')->references('id')->on('bill_billing_authorization_details');
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
            $table->dropForeign('bill_billing_authorization_detail_id');
            $table->dropColumn('bill_billing_authorization_detail_id');
            $table->dropColumn('prefix');
        });
    }
}
