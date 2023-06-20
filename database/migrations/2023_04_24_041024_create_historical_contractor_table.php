<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_contractor', function (Blueprint $table) {
            $table->increments('id_historical_contractor',true);
            $table->integer('id_contractor')->index('fk_historical_contractor_contractor1_idx');
            $table->integer('id_saas_client_business');
            $table->string('name');
            $table->string('short_name')->nullable;
            $table->string('vat_number')->nullable();
            $table->string('tel_number1')->nullable();
            $table->string('tel_number2')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('contractor_active');
            $table->integer('edited_by');
            $table->date('history_valid_from')->nullable();
            $table->date('history_valid_to')->nullable();
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor', 'fk_historical_contractor_contractor1_idx')->references('id_contractor')->on('contractor')->onUpdate('NO ACTION')->onDelete('NO ACTION');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_contractor');
    }
}
