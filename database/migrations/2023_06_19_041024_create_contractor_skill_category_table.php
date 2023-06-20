<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorSkillCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_skill_category', function (Blueprint $table) {
            $table->increments('id_contractor_skill_category',true);
            $table->integer('id_contractor_skill_ref')->index('fk_contractor_skill_category_contractor_skill_ref1_idx');
            $table->integer('id_maintenance_job_category_ref')->index('fk_contractor_skill_category_maintenance_job_category_ref1_idx');
            $table->tinyInteger('contractor_skill_category_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor_skill_ref', 'fk_contractor_skill_category_contractor_skill_ref1_idx')->references('id_contractor_skill_ref')->on('contractor_skill_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_skill_category');
    }
}
