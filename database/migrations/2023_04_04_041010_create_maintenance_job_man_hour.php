<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobManHourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_man_hour', function (Blueprint $table) {
            $table->increments('id_maintenance_job_man_hour',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_man_hour_maintanance_job1_idx');
            $table->integer('id_saas_staff')->index('fk_maintanance_job_man_hour_user1_idx');
            $table->dateTime('activity_date_time');
            $table->integer('activity_duration');
            $table->tinyInteger('maintenance_job_man_hour_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintenance_job_man_hour_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_saas_staff', 'fk_maintanance_job_man_hour_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_man_hour');
    }
}
