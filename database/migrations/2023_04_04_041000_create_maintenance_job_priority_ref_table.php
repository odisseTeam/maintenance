<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobPriorityRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_priority_ref', function (Blueprint $table) {
            $table->increments('id_maintenance_job_priority_ref',true);
            $table->string('priority_code');
            $table->string('priority_name');
            $table->string('priority_icon')->nullable();
            $table->tinyInteger('maintenance_job_priority_ref_active');
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
        Schema::dropIfExists('maintenance_job_priority_ref');
    }
}
