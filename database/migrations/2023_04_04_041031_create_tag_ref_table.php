<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_ref', function (Blueprint $table) {
            $table->increments('id_tag_ref',true);
            $table->string('tag_code');
            $table->string('tag_name');
            $table->integer('tag_ref_active')->default(1);


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
        Schema::dropIfExists('tag_ref');
    }
}
