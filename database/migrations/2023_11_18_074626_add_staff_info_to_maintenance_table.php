<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_job', function (Blueprint $table) {
            //
            $table->integer('id_saas_staff_enter_data')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_job', function (Blueprint $table) {
            //
            $table->dropcolumn('id_saas_staff_enter_data');

        });
    }
};
