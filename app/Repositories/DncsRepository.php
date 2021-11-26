<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// Necesario para la clase Session
use Session;
use Auth;

use App\Models\Dncs;
use App\Models\Perfilusers;
use App\Models\User;
use App\Models\Periodos;

class DncsRepository extends Controller
{
    private $model;
    public function __construct()
    {        
        $this->model = New Dncs();
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
        $dnc = DB::table('dncs')
        ->join('periodos', 'periodos.cve_periodo', '=', 'dncs.fk_cve_periodo')
        ->join('plantillas', 'plantillas.id', '=', 'dncs.id')
        ->orderBy('id', 'ASC')
        ->select(
                'dncs.id',
                'dncs.fk_cve_periodo',
                'dncs.num_emp',
                'dncs.nombre_completo',
                'dncs.dep_o_ent',
                'dncs.unidad_admva',
                'dncs.area',
                'dncs.grado_est',
                'dncs.correo',
                'dncs.telefono',
                'dncs.funciones',
                'dncs.word_int',
                'dncs.word_ava',
                'dncs.excel_int',
                'dncs.excel_ava',
                'dncs.power_point',
                'dncs.nuevas_tec',
                'dncs.acc_institucionales',
                'dncs.acc_des_humano',
                'dncs.acc_administrativas',
                'dncs.otro_curso',
                'dncs.interes_instructor',
                'dncs.tema',
                'dncs.activo',
                'periodos.descripcion as periodo_descripcion',
                'periodos.activo as periodo_activo',
                'plantillas.activo as plantilla_activo',
                'plantillas.puesto as plantilla_puesto'
                )
                ->where('dncs.activo',true)
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
        //$vista = "ok";
        return $vista;
    }
    public function perfil_usuarios()
    {
        return( Perfilusers::all()->SortBy('cve_perfil_usuario'));
    }
    public function usuarios()
    {        
        return( User::all()->SortBy('email'));
    }
    public function periodos()
    {        
        return( Periodos::all()->SortBy('cve_perdiodo'));
    }
    public function all()
    {   
        return( $this->model->orderBy('num_emp', 'asc')->paginate(5));
    }
    public function dncs_blank(){
        $dncs = Dncs::FindOrFail(1);        
        $dncs->activo = true;
        return ( $dncs);
    }
}