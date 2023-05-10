<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobSlaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_sla', function (Blueprint $table) {
            $table->increments('id_maintenance_job_sla',true);
            $table->integer('id_maintenance_job_sla_ref')->index('fk_maintenance_job_sla_maintenance_job_sla_ref1_idx');
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_sla_maintenance_job1_idx');
            $table->tinyInteger('maintenance_job_sla_active');



            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job_sla_ref', 'fk_maintenance_job_sla_maintenance_job_sla_ref1_idx')->references('id_maintenance_job_sla_ref')->on('maintenance_job_sla_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job', 'fk_maintenance_job_sla_maintenance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_sla');
    }
}
