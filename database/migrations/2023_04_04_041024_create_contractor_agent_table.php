<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_agent', function (Blueprint $table) {
            $table->increments('id_contractor_agent',true);
            $table->integer('id_contractor')->index('fk_contractor_agent_contractor1_idx');
            $table->integer('id_user')->index('fk_contractor_agent_user1_idx');
            $table->tinyInteger('contractor_agent_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor', 'fk_contractor_agent_contractor1_idx')->references('id_contractor')->on('contractor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_user', 'fk_contractor_agent_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_agent');
    }
}
