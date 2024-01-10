<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationDateToClinicHistoryServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_services', function (Blueprint $table) {
            $table->datetime('application_date')->nullable()->after('observations');
        });
        
        \DB::statement("UPDATE clinic_history_services SET application_date = created_at");

        Schema::table('clinic_history_services', function (Blueprint $table) {
            $table->datetime('application_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_services', function (Blueprint $table) {
            $table->dropColumn('application_date');
        });
    }
}
