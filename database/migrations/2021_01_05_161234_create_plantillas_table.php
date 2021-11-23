<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePlantillasTable extends Migration
{    
    public function up()
    {
        Schema::create('plantillas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('num_emp');
            $table->string('nombre_completo',60);
            $table->string('sexo',10);
            $table->string('nivel',5);
            $table->string('dependencia',120);
            $table->string('unidad_admva',180);
            $table->string('puesto',80);
            $table->string('municipio',180);
            $table->string('plaza',10);
            $table->string('tipo_plaza',60);
            $table->string('fuente',10);
            $table->string('plantilla',10);
            $table->string('tipo_org',20);
            $table->string('num_plaza',5);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('plantillas');
    }
}

