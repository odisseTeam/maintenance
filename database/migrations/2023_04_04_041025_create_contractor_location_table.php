<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_location', function (Blueprint $table) {
            $table->increments('id_contractor_location',true);
            $table->integer('id_contractor_location_ref')->index('fk_contractor_location_contractor_location_ref1_idx');;
            $table->integer('id_contractor')->index('fk_contractor_location_contractor1_idx');;
            $table->tinyInteger('contractor_location_active');
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor', 'fk_contractor_location_contractor1_idx')->references('id_contractor')->on('contractor')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id_contractor_location_ref', 'fk_contractor_location_contractor_location_ref1_idx')->references('id_contractor_location_ref')->on('contractor_location_ref')->onUpdate('NO ACTION')->onDelete('NO ACTION');




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_location');
    }
}
