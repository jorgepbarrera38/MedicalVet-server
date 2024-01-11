<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumnToClinicHistoryProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_products', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('billing_pending_billing_id');
            $table->foreign('user_id')->references('id')->on('users');
        });

        \DB::statement("UPDATE clinic_history_products
                            JOIN clinic_histories ON clinic_history_products.clinic_history_id = clinic_histories.id
                        SET clinic_history_products.user_id = clinic_histories.user_id ");

        Schema::table('clinic_history_products', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });                        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_products', function (Blueprint $table) {
            $table->dropColumn();
        });
    }
}
