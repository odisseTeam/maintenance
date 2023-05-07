<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobStatusRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_status_ref', function (Blueprint $table) {
            $table->increments('id_maintenance_job_status_ref',true);
            $table->string('job_status_code');
            $table->string('job_status_name');
            $table->string('job_status_icon')->nullable();
            $table->tinyInteger('maintenance_job_status_ref_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_status_ref');
    }
}
