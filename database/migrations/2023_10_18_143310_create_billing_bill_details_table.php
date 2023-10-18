<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingBillDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_bill_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('billing_bill_id');
            $table->foreign('billing_bill_id')->references('id')->on('billing_bills');

            $table->string('code');
            $table->text('description');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services');
            
            $table->double('price');
            $table->integer('quantity');

            $table->unsignedBigInteger('billing_pending_billing_id')->nullable();
            $table->foreign('billing_pending_billing_id')->references('id')->on('billing_pending_billings');

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
        Schema::dropIfExists('billing_bill_details');
    }
}
