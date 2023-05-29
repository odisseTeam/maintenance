<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_log', function (Blueprint $table) {
            $table->increments('id_maintenance_log',true);
            $table->integer('id_maintenance_job')->index('fk_maintanance_log_maintanance_job1_idx');
            $table->integer('id_staff')->index('fk_maintanance_log_user1_idx');
            $table->dateTime('log_date_time')->nullable()->default(now());
            $table->text('log_note')->nullable();
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_maintenance_job', 'fk_maintanance_log_maintanance_job1_idx')->references('id_maintenance_job')->on('maintenance_job')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_staff', 'fk_maintanance_log_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_log');
    }
}
