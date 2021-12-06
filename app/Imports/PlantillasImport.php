<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Plantillas;

class PlantillasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Plantillas([
            'num_emp'           => $row['num_emp'],
            'nombre_completo'   => $row['nombre_completo'],
            'sexo'              => $row['sexo'],
            'nivel'             => $row['nivel'],
            'dependencia'       => $row['dependencia'],
            'unidad_admva'      => $row['unidad_admva'],
            'puesto'            => $row['puesto'],
            'municipio'         => $row['municipio'],
            'plaza'             => $row['plaza'],
            'tipo_plaza'        => $row['tipo_plaza'],
            'fuente'            => $row['fuente'],
            'plantilla'         => $row['plantilla'],
            'tipo_org'          => $row['tipo_org'],
            'num_plaza'         => $row['num_plaza'],
            'activo'            => $row['activo'],
        ]);
    }
}