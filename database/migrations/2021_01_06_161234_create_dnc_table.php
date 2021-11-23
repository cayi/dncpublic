<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDNCTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dnc', function (Blueprint $table) {
            $table->id();
            $table->string('fk_cve_periodo',3)->default("210");
            $table->bigInteger('num_emp');
            //  nota los siguientes campos quedarian fuera una vez ligada a plantillas
            $table->string('nombre_completo',80);
            $table->string('dep_o_ent',180)->default("SECRETARIA DE HACIENDA");
            $table->string('unidad_admva',180)->default("SUBSECRETARIA DE RECURSOS HUMANOS");
            // terminan los campos repetitivos
            $table->string('area',180)->default("DESARROLLO ORGANIZACIONAL");            
            $table->string('grado_est',80);
            $table->string('correo',80);
            $table->string('telefono',40);
            $table->text('funciones');
            $table->text('word_int');
            $table->text('word_ava');
            $table->text('excel_int');
            $table->text('excel_ava');
            $table->text('power_point');
            $table->text('nuevas_tec');
            $table->text('acc_institucionales');
            $table->text('acc_des_humano');
            $table->text('acc_administrativas');
            $table->text('otro_curso');
            $table->string('interes_instructor',2);
            $table->text('tema');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id')->references('id')->
            on('plantillas')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('dnc');
    }
}