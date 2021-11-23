<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DNC extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'fk_cve_periodo',
        'fk_num_emp',
        'tot_evaluar',
        'tot_evaluado',
        'pen_evaluar',
        'fk_cve_area',
        'puesto',
    ];   
    public function users () {
        return $this->hasMany(User::Class);
    }
}