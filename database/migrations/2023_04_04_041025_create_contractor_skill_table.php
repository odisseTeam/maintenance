<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_skill', function (Blueprint $table) {
            $table->increments('id_contractor_skill',true);
            $table->string('skill_name',255);
            $table->tinyInteger('contractor_skill_active');
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
        Schema::dropIfExists('contractor_skill');
    }
}
