<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalMaintenanceJobManHourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_maintenance_job_man_hour', function (Blueprint $table) {
            $table->increments('id_historical_maintenance_job_man_hour',true);
            $table->integer('id_maintenance_job_man_hour')->index('fk_historical_maintenance_job_man_hour_maintanance_job_man_hour1_idx');
            $table->integer('id_maintenance_job')->index('fk_historical_maintenance_job_man_hour_maintanance_job1_idx');
            $table->integer('id_saas_staff')->index('fk_historical_maintanance_job_man_hour_user1_idx');
            $table->dateTime('activity_date_time');
            $table->integer('activity_duration');
            $table->tinyInteger('maintenance_job_man_hour_active');
            $table->integer('edited_by');
            $table->date('history_valid_from')->nullable();
            $table->date('history_valid_to')->nullable();
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job_man_hour', 'fk_historical_maintenance_job_man_hour_maintanance_job_man_hour1_idx')->references('id_maintenance_job_man_hour')->on('maintenance_job_man_hour')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job', 'fk_historical_maintenance_job_man_hour_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_saas_staff', 'fk_historical_maintanance_job_man_hour_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_maintenance_job_man_hour');
    }
}
