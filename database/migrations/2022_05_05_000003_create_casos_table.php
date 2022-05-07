<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->bigIncrements('id'); 
                 
            $table->date('dataOcorrencia');
            $table->integer('idade');
            $table->string('resultado', 100);

            $table->unsignedBigInteger('idBairro');
            $table->foreign('idBairro')->references('id')->on('bairros');

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('casos');
    }
};
