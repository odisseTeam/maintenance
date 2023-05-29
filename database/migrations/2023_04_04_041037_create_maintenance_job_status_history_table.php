<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobStatusHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_status_history', function (Blueprint $table) {
            $table->increments('id_maintenance_job_status_history',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_status_history_maintanance_job1_idx');
            $table->integer('id_maintenance_staff')->index('fk_maintenance_job_status_history_user1_idx');
            $table->integer('id_maintenance_job_status')->index('fk_maintenance_job_status_history_maintenance_job_status_ref1_idx');
            $table->date('maintenance_status_start_date_time')->nullable()->default(now());
            $table->date('maintenance_status_end_date_time')->nullable();
            $table->tinyInteger('maintenance_job_status_history_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintenance_job_status_history_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_status', 'fk_maintenance_job_status_history_maintenance_job_status_ref1_idx')->references('id_maintenance_job_status_ref')->on('maintenance_job_status_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_staff', 'fk_maintenance_job_status_history_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_status_history');
    }
}
