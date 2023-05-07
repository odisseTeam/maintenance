<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor', function (Blueprint $table) {
            $table->increments('id_contractor',true);
            $table->integer('id_saas_client_business');
            $table->string('name');
            $table->string('short_name');
            $table->string('vat_number')->nullable();
            $table->string('tel_number1')->nullable();
            $table->string('tel_number2')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->tinyInteger('contractor_active');
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
        Schema::dropIfExists('contractor');
    }
}
