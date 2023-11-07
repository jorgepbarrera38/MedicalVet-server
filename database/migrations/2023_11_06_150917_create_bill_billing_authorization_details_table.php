<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillBillingAuthorizationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_billing_authorization_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_billing_authorization_id');
            $table->foreign('bill_billing_authorization_id', 'bill_billing_authorization_id')->references('id')->on('bill_billing_authorizations');
            $table->boolean('active');
            $table->string('resolution_number');
            $table->date('resolution_date');
            $table->bigInteger('initial_bill');
            $table->bigInteger('final_bill');
            $table->date('initial_date');
            $table->date('final_date');
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
        Schema::dropIfExists('bill_billing_authorization_details');
    }
}
