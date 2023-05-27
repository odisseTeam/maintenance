<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorSkillRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_skill_ref', function (Blueprint $table) {
            $table->increments('id_contractor_skill_ref',true);
            $table->string('skill_name',255);
            $table->tinyInteger('contractor_skill_ref_active');
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
        Schema::dropIfExists('contractor_skill_ref');
    }
}
