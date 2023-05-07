<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobPriorityHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_priority_history', function (Blueprint $table) {
            $table->increments('id_maintenance_job_priority_history',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_priority_history_maintanance_job1_idx');
            $table->integer('id_maintenance_job_priority_ref')->index('fk_maintenance_job_priority_history_maintanance_job_priority_ref1_idx');
            $table->dateTime('priority_start_date_time');
            $table->dateTime('priority_end_date_time')->nullable();
            $table->tinyInteger('maintenance_job_priority_history_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintenance_job_priority_history_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_priority_ref', 'fk_maintenance_job_priority_history_maintanance_job_priority_ref1_idx')->references('id_maintenance_job_priority_ref')->on('maintenance_job_priority_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_priority_history');
    }
}
