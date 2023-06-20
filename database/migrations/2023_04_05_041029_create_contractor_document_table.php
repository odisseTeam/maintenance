<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_document', function (Blueprint $table) {
            $table->increments('id_contractor_document',true);
            $table->integer('id_contractor')->index('fk_contractor_document_contractor1_idx');
            $table->string('document_name');
            $table->string('document_address');
            $table->string('document_extention');
            $table->text('description')->nullable();
            $table->tinyInteger('contractor_document_active')->default(1);
            $table->charset = "utf8";
            $table->collation = "utf8_general_ci";

            $table->foreign('id_contractor', 'fk_contractor_document_contractor1_idx')->references('id_contractor')->on('contractor')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_document');
    }
}
