<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationDateAndApplicationDateToVaccinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->datetime('registration_date')->nullable()->after('pet_id');
            $table->date('application_date')->nullable()->after('registration_date');
        });

        \DB::statement("UPDATE vaccines set registration_date = created_at, application_date =  created_at");

        Schema::table('vaccines', function (Blueprint $table) {
            $table->datetime('registration_date')->nullable(false)->change();
            $table->date('application_date')->nullable(false)->change();
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
            $table->dropColumn('registration_date');
            $table->dropColumn('application_date');
        });
    }
}
