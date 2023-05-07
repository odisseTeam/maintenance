<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobCategoryRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_category_ref', function (Blueprint $table) {
            $table->increments('id_maintenance_job_category_ref',true);
            $table->string('job_category_code',4);
            $table->string('job_category_name',255);
            $table->string('job_category_icon',255)->nullable();
            $table->tinyInteger('maintenance_job_category_ref_active');
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
        Schema::dropIfExists('maintenance_job_category_ref');
    }
}
