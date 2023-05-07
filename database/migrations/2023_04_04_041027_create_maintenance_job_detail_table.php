<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_detail', function (Blueprint $table) {
            $table->increments('id_maintenance_job_detail',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_detail_maintanance_job1_idx');
            $table->dateTime('maintenance_job_detail_date_time');
            $table->integer('id_staff')->nullable();
            $table->text('job_detail_note')->nullable();
            $table->tinyInteger('maintenance_job_detail_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintenance_job_detail_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            // $table->foreign('id_staff', 'fk_maintenance_job_detail_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_detail');
    }
}
