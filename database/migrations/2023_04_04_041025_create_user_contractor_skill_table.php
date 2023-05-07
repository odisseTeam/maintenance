<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserContractorSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contractor_skill', function (Blueprint $table) {
            $table->increments('id_user_contractor_skill',true);
            $table->integer('id_contractor')->index('fk_user_contractor_skill_user1_idx');
            $table->integer('id_contractor_skill')->index('fk_user_contractor_skill_contractor_skill1_idx');
            $table->string('coverage_area')->nullable();
            $table->tinyInteger('user_contractor_skill_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor', 'fk_user_contractor_skill_user1_idx')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_contractor_skill', 'fk_user_contractor_skill_contractor_skill1_idx')->references('id_contractor_skill')->on('contractor_skill')->onUpdate('NO ACTION')->onDelete('NO ACTION');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_contractor_skill');
    }
}
