<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceJobDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_job_document', function (Blueprint $table) {
            $table->increments('id_maintenance_job_document',true);
            $table->integer('id_maintenance_job')->index('fk_maintenance_job_document_maintanance_job1_idx');
            $table->string('document_name');
            $table->string('document_address');
            $table->string('document_extention');
            $table->text('description')->nullable();
            $table->tinyInteger('maintenance_job_document_active')->default(1);
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintenance_job_document_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_job_document');
    }
}
