<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobSlaRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_sla_ref', function (Blueprint $table) {
            $table->increments('id_maintenance_job_sla_ref',true);
            $table->integer('id_saas_client_business')->index('fk_sla_saas_client_business1_idx');
            $table->integer('id_maintenance_job_priority')->index('fk_sla_maintenance_job_priority1_idx');
            $table->integer('id_client')->nullable();
            $table->dateTime('maximum_expected_seen_date')->nullable();
            $table->dateTime('expected_target_date')->nullable();
            $table->tinyInteger('maintenance_job_sla_ref_active');



            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_saas_client_business', 'fk_sla_saas_client_business1_idx')->references('id_saas_client_business')->on('saas_client_business')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_maintenance_job_priority', 'fk_sla_maintenance_job_priority1_idx')->references('id_maintenance_job_priority_ref')->on('maintenance_job_priority_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_sla_ref');
    }
}
