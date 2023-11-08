<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetIdFieldToClinicHistoryPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable()->after('clinic_history_id');
            $table->foreign('pet_id')->references('id')->on('pets');
        });

        \DB::statement("UPDATE clinic_history_prescriptions 
                                JOIN clinic_histories ON clinic_history_prescriptions.clinic_history_id = clinic_histories.id
                            SET clinic_history_prescriptions.pet_id = clinic_histories.pet_id ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->dropForeign('clinic_history_prescriptions_pet_id_foreign');
            $table->dropColumn('pet_id');
        });
    }
}
