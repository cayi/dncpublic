<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
//use Exception;
//use Auth;
// Necesario para la clase Session
use Session;
use Auth;

use App\Models\DNC;

class DNCRepository extends Controller
{
    private $model;
    public function __construct()
    {        
        $this->model = New DNC();
    }
    public function eva2()
    {
        //dd(Auth::user());
        Session::pull('mensaje');
        Session::pull('filter');
        $evaluados = DB::table('evaluados')
                 ->where('fk_ne_jefe', Auth::user()->num_emp)
                 ->orderBy('id')
                 ->get();
        $evaluador = DB::table('evaluadores')
                 ->join('periodos', 'periodos.cve_periodo', '=', 'evaluadores.fk_cve_periodo_ultimo')
                 ->join('areas', 'areas.cve_area', '=', 'evaluadores.fk_cve_area')
                 ->join('unidades', 'unidades.cve_unidad', '=', 'areas.fk_cve_unidad')
                 ->join('dependencias', 'dependencias.cve_dependencia', '=', 'unidades.fk_cve_dependencia')
                 ->join('tipo_dependencia', 'tipo_dependencia.cve_tipo_dependencia', '=', 'dependencias.fk_cve_tipo_dependencia')
                 ->orderBy('id', 'ASC')
                 ->select(
                     'evaluadores.id',
                     'evaluadores.fk_cve_periodo_ultimo',
                     'evaluadores.ne_jefe',
                     'evaluadores.tot_evaluar',
                     'evaluadores.tot_evaluado',
                     'evaluadores.pen_evaluar',
                     'evaluadores.puesto',
                     'evaluadores.fk_cve_area',
                     'areas.descripcion as area',
                     'areas.activa as area_activa',
                     'unidades.descripcion as unidad_admva',
                     'unidades.activa as unidad_admva_activa',
                     'dependencias.descripcion as dependencia',
                     'dependencias.activa as dependencia_activa',
                     'tipo_dependencia.descripcion as tipo_dependencia')
                 ->where('ne_jefe', Auth::user()->num_emp)->get();
        $success = false;
        if ($evaluador->isEmpty()) 
             {
                 $success = "Error, El usuario No. ".Auth::user()->num_emp." no existe ers.index().";
             };
        $tot_evaluar = 0;
        $pen_evaluar = 0;
        // debe entrar una sola vez a este ciclo, solo un registro se extrae
        foreach($evaluador as $evalr) 
             {
                 $tot_evaluar = $evalr->tot_evaluar;
                 $pen_evaluar = $evalr->pen_evaluar;
        };
        if($pen_evaluar == "0") 
             {
                 $success = "Usted ya no tiene empleados por evaluar.";
             } else 
             {
                 $success = "Usted tiene ". $tot_evaluar.
                     " empleados asignados y le quedan ". $pen_evaluar. " por evaluar.";
        };
        $datos=[
                 "usuario"=>Auth::user()->name,
                 "cve_perfil_usuario"=>Auth::user()->cve_perfil_usuario,
                 "email"=>Auth::user()->email,
                 "numeroEmpleado"=>Auth::user()->num_emp,
                 "evaluados"=>$evaluados,
                 "success"=>$success
             ];
        //dd($datos);
        // es administrador?
        if (Auth::user()->fk_cve_perfil_usuario == "A") 
             { 
                 $datos['success']  = "Tenga cuidado con estas opciones";
                return view('administrador',$datos);
        }
        else
        {
            foreach($evaluador as $evalr) 
                 {
                     if($evalr->area_activa &&
                        $evalr->unidad_admva_activa  &&
                        $evalr->dependencia_activa ) 
                     {
                         //dd($evaluador);
                         //dd("USUARIO MORTAL");
                         return view('evaluadores',$datos);
                     } else
                     {
                         //dd($evalr);
                         $datos['cve_area'] = $evalr->fk_cve_area;                        
                         return view('inactivo',$datos);
                     }
                 }                
        };
    }
    // completa la vista del Usuario normal Evaluador!!
    public function index()
    {
        $datos = [
            "usuario"=>Auth::user()->name,
            "cve_perfil_usuario"=>Auth::user()->fk_cve_perfil_usuario,
            "email"=>Auth::user()->email,
            "success"=>""
        ];
        //dd("USUARIO MORTAL");
        $dnc = DB::table('dnc')
        ->join('periodos', 'periodos.cve_periodo', '=', 'dnc.fk_cve_periodo')
        ->join('plantillas', 'plantillas.id', '=', 'dnc.id')
        ->orderBy('id', 'ASC')
        ->select(
                'dnc.id',
                'dnc.fk_cve_periodo',
                'dnc.num_emp',
                'dnc.nombre_completo',
                'dnc.dep_o_ent',
                'dnc.unidad_admva',
                'dnc.area',
                'dnc.grado_est',
                'dnc.correo',
                'dnc.telefono',
                'dnc.funciones',
                'dnc.word_int',
                'dnc.word_ava',
                'dnc.excel_int',
                'dnc.excel_ava',
                'dnc.power_point',
                'dnc.nuevas_tec',
                'dnc.acc_institucionales',
                'dnc.acc_des_humano',
                'dnc.acc_administrativas',
                'dnc.otro_curso',
                'dnc.interes_instructor',
                'dnc.tema',
                'dnc.activo',
                'periodos.descripcion as periodo_descripcion',
                'periodos.activo as periodo_activo',
                'plantillas.activo as plantilla_activo',
                'plantillas.puesto as plantilla_puesto'
                )
                ->where('dnc.activo',true)
                ->where('periodos.activo',true)
                ->where('plantillas.activo',true)
                ->get();
        //dd($dnc);
        if ($dnc->isEmpty())
        { 
            $vista= back()->with("Error, tabla dnc vacía o periodo inactivo o empleado(a) inactivo(a).");
            //dd($vista);
            return $vista;
        };
        //dd($dnc);
         $vista= view('consideraciones',$datos);
         return $vista;
    }
    // completa la vista del Administrador!!
    public function indexAdmin()
    {
        //dd("Administrador");
        //echo "Home Admin"
        $datos = [
            "usuario"=>Auth::user()->name,
            "cve_perfil_usuario"=>Auth::user()->fk_cve_perfil_usuario,
            "email"=>Auth::user()->email,            
            "success"=>""
        ];
        $datos['success']  = "Tenga cuidado con estas opciones";
        $vista = view('administrador',$datos);
        return $vista;
    }
}