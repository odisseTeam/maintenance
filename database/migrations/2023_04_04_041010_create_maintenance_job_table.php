<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job', function (Blueprint $table) {
            $table->increments('id_maintenance_job',true);
            $table->integer('id_saas_client_business')->index('fk_maintanance_job_saas_client_business1_idx');
            $table->integer('id_parent_job')->nullable();
            $table->integer('id_saas_staff_reporter')->index('fk_maintanance_job_user1_idx');
            $table->dateTime('job_report_date_time')->nullable()->default(now());
            $table->dateTime('job_start_date_time')->nullable();
            $table->dateTime('job_finish_date_time')->nullable();
            $table->integer('id_maintenance_job_category')->index('fk_maintanance_job_category1_idx');
            $table->integer('id_maintenance_job_priority')->index('fk_maintanance_job_priority1_idx');
            $table->integer('id_maintenance_job_status')->index('fk_maintanance_job_status1_idx');
            $table->string('maintenance_job_title');
            $table->text('maintenance_job_description')->nullable();
            $table->integer('id_resident_reporter')->nullable();
            $table->tinyInteger('maintenance_job_active');

            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_saas_client_business', 'fk_maintanance_job_saas_client_business1_idx')->references('id_saas_client_business')->on('saas_client_business')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_saas_staff_reporter', 'fk_maintanance_job_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_category', 'fk_maintanance_job_category1_idx')->references('id_maintenance_job_category_ref')->on('maintenance_job_category_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_priority', 'fk_maintanance_job_priority1_idx')->references('id_maintenance_job_priority_ref')->on('maintenance_job_priority_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_status', 'fk_maintanance_job_status1_idx')->references('id_maintenance_job_status_ref')->on('maintenance_job_status_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job');
    }
}
