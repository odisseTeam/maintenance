<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobStaffHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_staff_history', function (Blueprint $table) {
            $table->increments('id_maintenance_job_staff_history',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_staff_history_maintanance_job1_idx');
            $table->integer('id_maintenance_staff')->index('fk_maintenance_job_staff_history_user1_idx');
            $table->integer('id_maintenance_assignee')->index('fk_maintenance_job_staff_history_user2_idx')->nullable();
            $table->dateTime('staff_assign_date_time');
            $table->dateTime('staff_start_date_time')->nullable();
            $table->dateTime('staff_end_date_time')->nullable();
            $table->tinyInteger('is_last_one')->default(1);
            $table->tinyInteger('maintenance_job_staff_history_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_staff', 'fk_maintenance_job_staff_history_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_assignee', 'fk_maintenance_job_staff_history_user2_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job', 'fk_maintenance_job_staff_history_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_staff_history');
    }
}
