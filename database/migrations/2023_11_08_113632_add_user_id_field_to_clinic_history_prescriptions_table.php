<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdFieldToClinicHistoryPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('observations');
            $table->foreign('user_id')->references('id')->on('users');
        });

        \DB::statement("UPDATE clinic_history_prescriptions 
                                JOIN clinic_histories ON clinic_history_prescriptions.clinic_history_id = clinic_histories.id
                            SET clinic_history_prescriptions.user_id = clinic_histories.user_id ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_history_prescriptions', function (Blueprint $table) {
            $table->dropForeign('clinic_history_prescriptions_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
