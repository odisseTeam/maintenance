<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobTagRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_tag_ref', function (Blueprint $table) {
            $table->increments('id_maintenance_job_tag_ref',true);
            $table->string('tag_code');
            $table->string('tag_name');
            $table->tinyInteger('maintenance_job_tag_ref_active');
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
        Schema::dropIfExists('maintenance_job_tag_ref');
    }
}
