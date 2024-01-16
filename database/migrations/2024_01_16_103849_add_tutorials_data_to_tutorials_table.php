<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tutorial;

class AddTutorialsDataToTutorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Tutorial::create([
            'title' => '1. Registrar mascotas en Medical Vet',
            'url' => 'https://www.youtube.com/watch?v=bGVqdcQDhQU&list=PLzVSZrD1VbwGSqo6wyRMxktDf-vHri8Pm',
        ]);

        Tutorial::create([
            'title' => '2. Agendar citas en Medical Vet',
            'url' => 'https://www.youtube.com/watch?v=fqK5Fscex4I&list=PLzVSZrD1VbwGSqo6wyRMxktDf-vHri8Pm&index=2',
        ]);

        Tutorial::create([
            'title' => '3. Hospitalizacion de mascotas en Medical Vet',
            'url' => 'https://www.youtube.com/watch?v=g3SmjZXXbx0&list=PLzVSZrD1VbwGSqo6wyRMxktDf-vHri8Pm&index=3',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tutorials', function (Blueprint $table) {
            //
        });
    }
}
