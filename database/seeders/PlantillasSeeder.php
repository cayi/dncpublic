<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasSeeder extends Seeder
{
    public function run()
    {
        DB::table('plantillas')->insert([   
            'id' => "1",
            'num_emp' => '1',
            'nombre_completo' => 'JOSE LUIS LOPEZ PEREZ',
            'sexo' => 'MESCULINO',
            'nivel' => '08B',
            'dependencia' => 'CENTRO DE EVALUACION Y CONTROL DE CONFIANZA DEL EDO.',
            'unidad_admva' => 'N/D',
            'puesto' => 'INVESTIGADOR',
            'municipio' => 'N/D',
            'plaza' => 'N/D',
            'tipo_plaza' => 'N/D',
            'fuente' => 'ORGANISMOS',
            'plantilla' => 'QNA 18',
            'tipo_org' => '3C',
            'num_plaza' => 'N/D',
        ]);
    }
}