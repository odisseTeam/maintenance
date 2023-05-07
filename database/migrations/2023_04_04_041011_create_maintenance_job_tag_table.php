<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_tag', function (Blueprint $table) {
            $table->increments('id_maintenance_job_tag',true);
            $table->integer('id_tag_ref')->index('fk_maintanance_job_tag_ref1_idx');
            $table->integer('id_first_job')->index('fk_maintanance_job_tag_job1_idx');
            $table->integer('id_second_job')->index('fk_maintanance_job_tag_job2_idx');
            $table->tinyInteger('maintenance_job_tag_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";


            $table->foreign('id_tag_ref', 'fk_maintanance_job_tag_ref1_idx')->references('id_maintenance_job_tag_ref')->on('maintenance_job_tag_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_first_job', 'fk_maintanance_job_tag_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_second_job', 'fk_maintanance_job_tag_job2_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_tag');
    }
}
