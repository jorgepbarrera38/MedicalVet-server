<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClinicHistoryProductIdToDewormingsAndVaccinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dewormings', function (Blueprint $table) {
            $table->dropForeign('dewormings_billing_pending_billing_id_foreign');
            $table->dropColumn('billing_pending_billing_id');

            $table->unsignedBigInteger('clinic_history_product_id')->after('user_id');
            $table->foreign('clinic_history_product_id')->references('id')->on('clinic_history_products');
        });

        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropForeign('vaccines_billing_pending_billing_id_foreign');
            $table->dropColumn('billing_pending_billing_id');

            $table->unsignedBigInteger('clinic_history_product_id')->after('user_id');
            $table->foreign('clinic_history_product_id')->references('id')->on('clinic_history_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
