<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// Necesario para la clase Session
use Session;
use Auth;
use Excel;

use App\Models\Dncs;
use App\Models\Plantillas;
use App\Models\Perfilusers;
use App\Models\User;
use App\Models\Periodos;

use App\Imports\DncsImport;
use App\Exports\UsersExport;
use App\Exports\DncsExport;
use App\Exports\PlantillasExport;

class DncsRepository extends Controller
{
    private $model;
    private $plantilla;
    private $ultimo_periodo;
    public function __construct()
    {        
        $this->model = New Dncs();
        $this->plantilla = DB::table('plantillas')                                
            ->where('plantillas.id', '=', "1")
            ->get();
        $this->ultimo_periodo = $this->ultimo_periodo();
    }
    // completa la vista del Administrador, viene del controller
    public function indexAdmin()
    {
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
    // viene del controller, menu capturista?
    public function index()
    { 
      //dd($this->es_administrador());
      if ( $this->es_administrador() == "Si")  {   return $this->indexcrud(); }
      else  { return $this->consideraciones(); }       
    }
    // viene del controller
    public function create( Request $request)
    {
        $perfil_usuarios    = $this->perfil_usuarios();
        $usuarios           = $this->usuarios();
        $periodos           = $this->periodos();
        $dncs               = $this->dncs_blank();
        return view('admin/Dncs.create', compact(
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'));
    }
    // viene del controller, pero puede venir del boron guardar o de confirmar
    public function store(Request $request)
    {   
        dd( $request); 
        if ( $request->confirmar == "1") {} )
        else {
            $this->insert( $request);
            return redirect("/admin/Dncs")->with('mensaje','Nuevo Formato DNC Agergado.');
        }
    } 
    // viene del controller
    public function destroy( Request $request, $id)
    {
        $this->model->destroy( $request, $id);
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Borrado.');
    }
    // viene del controller
    public function edit( Request $request, $id)
    {
        $perfil_usuarios    = $this->perfil_usuarios();
        $usuarios           = $this->usuarios();
        $periodos           = $this->periodos();
        $dncs               = $this->editdnc( $id);
        //dd($dncs);
        return view('admin/Dncs.edit', compact(
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'));
    }
    // boton Grabar datos - Editar, viene del controller
    public function update(Request $request, $id)
    {
        //dd($request);
        $this->save( $request, $id); 
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Actualizado.');
    }
    // viene del controller
    public function import(Request $request)
    {
      if ( $this->es_administrador() == "Si")   { return $this->importdnc( $request); }
      else { return $this->get_user_data(); }
    }
    // viene del controller   
    public function indeximport(Request $request)
    {
        if ( $this->es_administrador() == "Si")  {
            $periodos           = $this->periodos();
            $dncs               = $this->all();          
            return view('/admin/Dncs/Import', compact('dncs','periodos')); }
        else { return $this->get_user_data(); }
    }
    // viene del controller   
    public function repo( Request $request, $repo)
    {
        if ( $this->es_administrador() == "Si") { return $this->reportes( $repo); }
        else { return $this->get_user_data(); }
    }
    // viene del controller   
    public function exp( Request $request, $exp)
    {
        if ( $this->es_administrador() == "Si")  { return $this->export( $exp); }
        else { return $this->get_user_data(); }
    }
    // viene del controller   
    public function dncsrepo( Request $request)
    {
        // clic en reporte detallado?
        if (isset($request->repodet)) {
            return $this->dncsrepodet( $request);
        } else {
            return $this->dncsrepodep( $request);            
        }   
    }   
    // no hereda Request? viene del controller
    public function show( Request $request)
    {
        //dd("show repo");
        if ( $this->es_administrador() == "Si")  {  return redirect("/admin/Dncs/create"); }
        else
        {    
            if (Session::has('model')) { $this->session_to_model(); }
            $request = $this->model_to_request();
            return $this->createval( $request);
        }
    }    
    // viene del controller
    public function searchtoo(Request $request) 
    {        
        return $this->searchconfirm( $request);
    }
    // viene del controller
    public function search(Request $request) 
    {
        return $this->indexblank();
    }
    private function searchconfirm(Request $request) 
    {
        $num_emp = trim($request->num_emp);
        $dep_o_ent = trim($request->dependencia);
        $plan = DB::table('plantillas')
          ->orderBy('plantillas.num_emp', 'ASC')
          ->where('plantillas.dependencia', '=', $dep_o_ent)
          ->where('plantillas.num_emp', '=', $num_emp)
          ->get();      
        // SE ECNONTRÓ EN PLANTILLAS?
        if( count($plan) > 0)
        {        
          $this->plantilla = $plan;
          $this->model->fk_id_plantillas  = $this->plantilla[0]->id;
          $this->model->num_emp           = $this->plantilla[0]->num_emp;
          $this->model->nombre_completo   = $this->plantilla[0]->nombre_completo;
          $this->model->dep_o_ent         = $this->plantilla[0]->dependencia;
          $this->model->unidad_admva      = $this->plantilla[0]->unidad_admva;
          $this->model_to_session();
          return $this->createval2();
        } else {  
            //dd("not");
            return $this->not_found( $num_emp, $dep_o_ent); }
    }
    private function not_found( $num_emp, $dep_o_ent) 
    {
        $msg = 'Empleado con numero = '.$num_emp. 
            " y dependencia = ".$dep_o_ent .
          ', no pudo ser localizado en plantillas.';
            //dd($msg);
            return redirect("/admin/search")
                ->with('mensaje', $msg)
                ->with('num_emp',$num_emp)
                ->with('dep_o_ent',$dep_o_ent)
            ;
    }
    private function search2(Request $request) 
    {
      $num_emp = trim($request->num_emp);
      $dep_o_ent = trim($request->dependencia);
      $plan = DB::table('plantillas')
        ->orderBy('plantillas.num_emp', 'ASC')
        ->where('plantillas.dependencia', '=', $dep_o_ent)
        ->where('plantillas.num_emp', '=', $num_emp)
        ->get();      
      // SE ECNONTRÓ EN PLANTILLAS?
      if( count($plan) > 0)
      {        
        $this->plantilla = $plan;
        $this->model->fk_id_plantillas  = $this->plantilla[0]->id;
        $this->model->num_emp           = $this->plantilla[0]->num_emp;
        $this->model->nombre_completo   = $this->plantilla[0]->nombre_completo;
        $this->model->dep_o_ent         = $this->plantilla[0]->dependencia;
        $this->model->unidad_admva      = $this->plantilla[0]->unidad_admva;        
        $udnc = DB::table('dncs')
            ->orderBy('dncs.num_emp', 'ASC')
            ->orderBy('dncs.fk_cve_periodo', 'DESC')
            ->where('dncs.dep_o_ent', '=', $dep_o_ent)
            ->where('dncs.deleted_at', '=', NULL)
            ->where('dncs.num_emp', '=', $num_emp)
            ->get();        
        $this->model_to_session();
        // existe ak menos 1 formato DNC        
        if( count($udnc) > 0)
        {                        
            if( $udnc[0]->fk_cve_periodo == $this->ultimo_periodo->cve_periodo )
            {                
                $err = 
                "Ya existe un formato capturado para el periodo ='". $this->ultimo_periodo->descripcion.
                "' y empleado numero = '".$num_emp. 
                "' y dependencia = '".$dep_o_ent. "'.";
                return back()->with('mensaje', $err);
            }
            else
            {
                // pasa los datos de la ultimo formato dnc
                $this->model->fk_cve_periodo    = $this->ultimo_periodo->cve_periodo;
                $this->model->area              = $udnc[0]->area;
                $this->model->grado_est         = $udnc[0]->grado_est;
                $this->model->correo            = $udnc[0]->correo;
                $this->model->telefono          = $udnc[0]->telefono;
                $this->model->funciones         = trim($udnc[0]->funciones);
                // opcional los datos anteriores
                $this->model->word_int          = $udnc[0]->word_int;
                $this->model->word_ava          = $udnc[0]->word_ava;
                $this->model->excel_int         = $udnc[0]->excel_int;
                $this->model->excel_ava         = $udnc[0]->excel_ava;
                $this->model->power_point       = $udnc[0]->power_point;
                $this->model->nuevas_tec        = $udnc[0]->nuevas_tec;
                $this->model->acc_institucionales       = $udnc[0]->acc_institucionales;
                $this->model->acc_des_humano    = $udnc[0]->acc_des_humano;
                $this->model->acc_administrativas   = $udnc[0]->acc_administrativas;
                $this->model->otro_curso        = $udnc[0]->otro_curso;
                $this->model->interes_instructor        = $udnc[0]->interes_instructor;
                $this->model->tema              = $udnc[0]->tema;
                $this->model->activo            = true;
                $this->set_attributes();
                // pone el ultimo periodo activo registrado
                $this->model_to_session();                                
                return $this->createval();
            }
        }
        // no existe formato DNC        
        $this->model->fk_cve_periodo    = $this->ultimo_periodo->cve_periodo;
        $this->model->area              = '';
        $this->model->grado_est         = '';
        $this->model->correo            = '';
        $this->model->telefono          = '';
        $this->model->funciones         = '';
        // opcional los datos anteriores
        $this->model->word_int          = '';
        $this->model->word_ava          = '';
        $this->model->excel_int         = '';
        $this->model->excel_ava         = '';
        $this->model->power_point       = '';
        $this->model->nuevas_tec        = '';
        $this->model->acc_institucionales       = '';
        $this->model->acc_des_humano    = '';
        $this->model->acc_administrativas   = '';
        $this->model->otro_curso        = '';
        $this->model->interes_instructor        = '';
        $this->model->tema              = '';;
        $this->model->activo            = true;
        $this->model_to_session();        
        return $this->createval();
      }
      // ELSE  if( count($plan) > 0), no se enocntr+o en plantilla
      else  { return $thos->not_found( $num_emp, $dep_o_ent); }
    }
    private function get_user_data() 
    {
      $datos=[
        "usuario"=>Auth::user()->name,
        "email"=>Auth::user()->email,
        "success"=>"Error, Solo pueden entrar Administradores a esta opción"
      ]; 
      return $datos;
    }
    private function es_administrador() 
    {
      if (Auth::user()->fk_cve_perfil_usuario != "A") 
      {
        return back()->with('success', 'Error, solo pueden ingresar los Administradores.');  
      }
      return "Si";
    }
    // completa la vista del Usuario normal Evaluador!!
    private function indexdnc()
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
        if ($dnc->isEmpty())
        { 
            $vista= back()->with("Error, tabla dnc vacía o periodo inactivo o empleado(a) inactivo(a).");
            return $vista;
        };
         $vista= view('consideraciones',$datos);
         return $vista;
    }    
    private function perfil_usuarios()
    {
        return( Perfilusers::all()->SortBy('cve_perfil_usuario'));
    }
    private function usuarios()
    {        
        return( User::all()->SortBy('email'));
    }
    private function periodos()
    {        
        return( Periodos::all()->SortBy('cve_periodo'));
    }
    private function ultimo_periodo()
    {                
        return( DB::table('periodos')->orderBy('id', 'desc')
        ->where('periodos.activo', '=', true)
        ->first());
    }
    private function dependencias_de_plantillas()
    {                
        return Plantillas::select('dependencia')->
            distinct()->
            orderBy('dependencia','ASC')->
            get();
    }
    private function all()
    {       
        return $this->model->orderBy('id', 'asc')->paginate(5);
    }
    private function dncs_blank(){
        $ult_per = $this->ultimo_periodo();         
        $dncs = Dncs::FindOrFail(1);
        // obtiene el ultimo periodo de la tabla
        $dncs->fk_cve_periodo= $ult_per->cve_periodo;
        $dncs->num_emp= "";        
        $dncs->nombre_completo= "";
        $dncs->dep_o_ent= "";
        $dncs->unidad_admva= "";        
        $dncs->area= "";
        $dncs->grado_est= "";
        $dncs->correo= "";
        $dncs->telefono= "";
        $dncs->funciones= ""; 
        $dncs->activo = true;
        $dncs->word_int= "";
        $dncs->word_ava= "";
        $dncs->excel_int= "";
        $dncs->excel_ava= "";
        $dncs->power_point= "";
        $dncs->nuevas_tec= "";
        $dncs->acc_institucionales= "";
        $dncs->acc_des_humano= "";
        $dncs->acc_administrativas= "";
        $this->model = $dncs;
        return ( $dncs);
    }   
    private function fix_datos_dncs( $request) 
    {
        // elimina la variables _token , _method, y activao
        $datos_dncs = request()->except('_token', '_method', "activao","activa");
        $datos_dncs['activo'] = filter_var($request->activao, FILTER_VALIDATE_BOOLEAN);
        $datos_dncs['word_int']= "";
        $datos_dncs['word_ava']= "";
        $datos_dncs['excel_int']= "";
        $datos_dncs['excel_ava']= "";
        $datos_dncs['power_point']= "";
        $datos_dncs['nuevas_tec']= "";
        $datos_dncs['acc_institucionales']= "";
        $datos_dncs['acc_des_humano']= "";
        $datos_dncs['acc_administrativas']= "";        
        if (array_key_exists('word_int_tablas', $datos_dncs)) {
            $datos_dncs['word_int'] = "Tablas en Word.";
            unset($datos_dncs['word_int_tablas']);
        };
        if (array_key_exists('word_ava_correspondencia',$datos_dncs) ) {
            $datos_dncs['word_ava'] = "Combinación de Correspondencia.";
            unset($datos_dncs['word_ava_correspondencia']);
        };
        if (array_key_exists('excel_int_graficos',$datos_dncs)) {
            $datos_dncs['excel_int'] = "Gráficos en Excel.";
            unset($datos_dncs['excel_int_graficos']);
        };
        if (array_key_exists('excel_int_formulas',$datos_dncs)) {
            $datos_dncs['excel_int'] = $datos_dncs['excel_int']."\r\n".
            "Formulas Básicas en Excel.";
            unset($datos_dncs['excel_int_formulas']);
        };
        if (array_key_exists('excel_ava_herramientas',$datos_dncs)) {
            $datos_dncs['excel_ava'] = "Herramientas de visualización de datos.";
            unset($datos_dncs['excel_ava_herramientas']);
        };
        if (array_key_exists('excel_ava_funciones',$datos_dncs)) {
            $datos_dncs['excel_ava'] = $datos_dncs['excel_ava']."\r\n".
            "Funciones y herramientas avanzadas.";
            unset($datos_dncs['excel_ava_funciones']);
        };
        if (array_key_exists('power_point_cualidades',$datos_dncs)) {
            $datos_dncs['power_point'] = "Las Cualidades de las Presentaciones.";
            unset($datos_dncs['power_point_cualidades']);
        };
        if (array_key_exists('nuevas_tec_competencia',$datos_dncs)) {
            $datos_dncs['nuevas_tec'] = "Competencia comunicativa a través de la competencia digital.";
            unset($datos_dncs['nuevas_tec_competencia']);
        };
        if (array_key_exists('nuevas_tec_zoom',$datos_dncs)) {
            $datos_dncs['nuevas_tec'] = $datos_dncs['nuevas_tec']."\r\n".
            "Nuevas tecnologías (zoom).";
            unset($datos_dncs['nuevas_tec_zoom']);
        };
        if (array_key_exists('acc_institucionales_etica',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = "Ética e Integridad en el Servicio Público.";
            unset($datos_dncs['acc_institucionales_etica']);
        };
        if (array_key_exists('acc_institucionales_valores',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Valores Gubernamentales.";
            unset($datos_dncs['acc_institucionales_valores']);
        };
        if (array_key_exists('acc_institucionales_responsabilidades',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Responsabilidades Administrativas de las personas servidoras públicas.";
            unset($datos_dncs['acc_institucionales_responsabilidades']);
        };
        if (array_key_exists('acc_institucionales_metodologia',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Metodología de las 5´s.";
            unset($datos_dncs['acc_institucionales_metodologia']);
        };
        if (array_key_exists('acc_institucionales_identificacion',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Identificación Institucional.";
            unset($datos_dncs['acc_institucionales_identificacion']);
        };
        if (array_key_exists('acc_institucionales_violencia',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Violencia ver, conocer y reconocer.";
            unset($datos_dncs['acc_institucionales_violencia']);
        };
        if (array_key_exists('acc_institucionales_seguridad',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Seguridad con perspectiva de género.";
            unset($datos_dncs['acc_institucionales_seguridad']);
        };
        if (array_key_exists('acc_institucionales_norma',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Norma 025.Igualdad laboral.";
            unset($datos_dncs['acc_institucionales_norma']);
        };
        if (array_key_exists('acc_institucionales_induccion',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Inducción al Servicio Público.";
            unset($datos_dncs['acc_institucionales_induccion']);
        };
        if (array_key_exists('acc_institucionales_actualizacion',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Actualización en Gestión archivística, un camino hacia la transparencia y rendición de cuentas.";
            unset($datos_dncs['acc_institucionales_actualizacion']);
        };
        if (array_key_exists('acc_institucionales_documentos',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Documentos Administrativos.";
            unset($datos_dncs['acc_institucionales_documentos']);
        };
        if (array_key_exists('acc_institucionales_politicas',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Políticas públicas y el Ciudadano.";
            unset($datos_dncs['acc_institucionales_politicas']);
        };
        if (array_key_exists('acc_institucionales_correcto',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Correcto manejo de Información.";
            unset($datos_dncs['acc_institucionales_correcto']);
        };
        if (array_key_exists('acc_institucionales_protocolos',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Protocolos de Atención y Servicio.";
            unset($datos_dncs['acc_institucionales_protocolos']);
        };
        if (array_key_exists('acc_institucionales_vocacion',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Vocación de Servicio.";
            unset($datos_dncs['acc_institucionales_vocacion']);
        };
        if (array_key_exists('acc_institucionales_vocacion',$datos_dncs)) {
            $datos_dncs['acc_institucionales'] = $datos_dncs['acc_institucionales']. "\r\n".
            "Vocación de Servicio.";
            unset($datos_dncs['acc_institucionales_vocacion']);
        };
        if (array_key_exists('acc_des_humano_solucion',$datos_dncs)) {
            $datos_dncs['acc_des_humano'] = 
            "Solución de conflictos.";
            unset($datos_dncs['acc_des_humano_solucion']);
        };
        if (array_key_exists('acc_des_humano_como',$datos_dncs)) {
            $datos_dncs['acc_des_humano'] = $datos_dncs['acc_des_humano']. "\r\n".
            "Como afrontar las dificultades laborales.";
            unset($datos_dncs['acc_des_humano_como']);
        };
        if (array_key_exists('acc_des_humano_comunicacion',$datos_dncs)) {
            $datos_dncs['acc_des_humano'] = $datos_dncs['acc_des_humano']. "\r\n".
            "Comunicación consciente.";
            unset($datos_dncs['acc_des_humano_comunicacion']);
        };
        if (array_key_exists('acc_des_humano_importancia',$datos_dncs)) {
            $datos_dncs['acc_des_humano'] = $datos_dncs['acc_des_humano']. "\r\n".
            "La importancia de aceptarse a sí mismos.";
            unset($datos_dncs['acc_des_humano_importancia']);
        };
        if (array_key_exists('acc_des_humano_inteligencia',$datos_dncs)) {
            $datos_dncs['acc_des_humano'] = $datos_dncs['acc_des_humano']. "\r\n".
            "Inteligencia emocional.";
            unset($datos_dncs['acc_des_humano_inteligencia']);
        };
        if (array_key_exists('acc_administrativas_actualizacion',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = 
            "Actualización de procedimientos, Mejora Continua.";
            unset($datos_dncs['acc_administrativas_actualizacion']);
        };
        if (array_key_exists('acc_administrativas_cumplimiento',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Cumplimiento de objetivos y metas Institucionales.";
            unset($datos_dncs['acc_administrativas_cumplimiento']);
        };
        if (array_key_exists('acc_administrativas_administracion',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Administración efectiva del tiempo.";
            unset($datos_dncs['acc_administrativas_administracion']);
        };
        if (array_key_exists('acc_administrativas_clima',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Clima laboral.";
            unset($datos_dncs['acc_administrativas_clima']);
        };
        if (array_key_exists('acc_administrativas_modernizacion',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Modernización administrativa y diseño organizacional.";
            unset($datos_dncs['acc_administrativas_modernizacion']);
        };
        if (array_key_exists('acc_administrativas_recursos',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Administración de recursos humanos.";
            unset($datos_dncs['acc_administrativas_recursos']);
        };
        if (array_key_exists('acc_administrativas_materiales',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Administración de recursos materiales.";
            unset($datos_dncs['acc_administrativas_materiales']);
        };
        if (array_key_exists('acc_administrativas_sistema',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Sistema de calidad.";
            unset($datos_dncs['acc_administrativas_sistema']);
        };
        if (array_key_exists('acc_administrativas_otro',$datos_dncs)) {
            $datos_dncs['acc_administrativas'] = $datos_dncs['acc_administrativas']. "\r\n".
            "Otro.";
            unset($datos_dncs['acc_administrativas_otro']);
        };        
        return ($datos_dncs);
    }
    private function set_attributes()
    {
        $arreglo = $this->model->getAttributes();
        //dd( $arreglo);
        if ( strpos( $arreglo['word_int'], "Tablas en Word.") !== false) {
            $this->model->setAttribute('word_int_tablas',"1");            
        }
        if ( strpos( $arreglo['word_ava'], "Combinación de Correspondencia.") !== false) {
            $this->model->setAttribute('word_ava_correspondencia',"1");            
        }
        if ( strpos( $arreglo['excel_int'], "Gráficos en Excel.") !== false) {
            $this->model->setAttribute("excel_int_graficos","1");            
        }
        if ( strpos( $arreglo['excel_int'], "Formulas Básicas en Excel.") !== false) {
            $this->model->setAttribute("excel_int_formulas","1");            
        }
        if ( strpos( $arreglo['excel_ava'], "Herramientas de visualización de datos.") !== false) {
            $this->model->setAttribute("excel_ava_herramientas","1");            
        }
        if ( strpos( $arreglo['excel_ava'], "Funciones y herramientas avanzadas.") !== false) {
            $this->model->setAttribute("excel_ava_funciones","1");
        }
        if ( strpos( $arreglo['power_point'], "Las Cualidades de las Presentaciones.") !== false) {
            $this->model->setAttribute("power_point_cualidades","1");
        }
        if ( strpos( $arreglo['nuevas_tec'], "Competencia comunicativa a través de la competencia digital.") !== false) {
            $this->model->setAttribute("nuevas_tec_competencia","1");
        }
        if ( strpos( $arreglo['nuevas_tec'], "Nuevas tecnologías (zoom).") !== false) {
            $this->model->setAttribute("nuevas_tec_zoom","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Ética e Integridad en el Servicio Público.") !== false) {
            $this->model->setAttribute("acc_institucionales_etica","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Valores Gubernamentales.") !== false) {
            $this->model->setAttribute("acc_institucionales_valores","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Responsabilidades Administrativas de las personas servidoras públicas.") !== false) {
            $this->model->setAttribute("acc_institucionales_responsabilidades","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Metodología de las 5´s.") !== false) {
            $this->model->setAttribute("acc_institucionales_metodologia","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Identificación Institucional.") !== false) {
            $this->model->setAttribute("acc_institucionales_identificacion","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Violencia ver, conocer y reconocer.") !== false) {
            $this->model->setAttribute("acc_institucionales_violencia","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Seguridad con perspectiva de género.") !== false) {
            $this->model->setAttribute("acc_institucionales_seguridad","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Seguridad con perspectiva de género.") !== false) {
            $this->model->setAttribute("acc_institucionales_seguridad","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Norma 025.Igualdad laboral.") !== false) {
            $this->model->setAttribute("acc_institucionales_norma","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Inducción al Servicio Público.") !== false) {
            $this->model->setAttribute("acc_institucionales_induccion","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Actualización en Gestión archivística, un camino hacia la transparencia y rendición de cuentas.") !== false) {
            $this->model->setAttribute("acc_institucionales_actualizacion","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Documentos Administrativos.") !== false) {
            $this->model->setAttribute("acc_institucionales_documentos","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Políticas públicas y el Ciudadano.") !== false) {
            $this->model->setAttribute("acc_institucionales_politicas","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Correcto manejo de Información.") !== false) {
            $this->model->setAttribute("acc_institucionales_correcto","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Protocolos de Atención y Servicio.") !== false) {
            $this->model->setAttribute("acc_institucionales_protocolos","1");
        }
        if ( strpos( $arreglo['acc_institucionales'], "Vocación de Servicio.") !== false) {
            $this->model->setAttribute("acc_institucionales_vocacion","1");
        }
        if ( strpos( $arreglo['acc_des_humano'], "Solución de conflictos.") !== false) {
            $this->model->setAttribute("acc_des_humano_solucion","1");
        }
        if ( strpos( $arreglo['acc_des_humano'], "Como afrontar las dificultades laborales.") !== false) {
            $this->model->setAttribute("acc_des_humano_como","1");
        }
        if ( strpos( $arreglo['acc_des_humano'], "Comunicación consciente.") !== false) {
            $this->model->setAttribute("acc_des_humano_comunicacion","1");
        }
        if ( strpos( $arreglo['acc_des_humano'], "La importancia de aceptarse a sí mismos.") !== false) {
            $this->model->setAttribute("acc_des_humano_importancia","1");
        }
        if ( strpos( $arreglo['acc_des_humano'], "Inteligencia emocional.") !== false) {
            $this->model->setAttribute("acc_des_humano_inteligencia","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Actualización de procedimientos, Mejora Continua.") !== false) {
            $this->model->setAttribute("acc_administrativas_actualizacion","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Cumplimiento de objetivos y metas Institucionales.") !== false) {
            $this->model->setAttribute("acc_administrativas_cumplimiento","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Administración efectiva del tiempo.") !== false) {
            $this->model->setAttribute("acc_administrativas_administracion","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Clima laboral.") !== false) {
            $this->model->setAttribute("acc_administrativas_clima","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Modernización administrativa y diseño organizacional.") !== false) {
            $this->model->setAttribute("acc_administrativas_modernizacion","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Administración de recursos humanos.") !== false) {
            $this->model->setAttribute("acc_administrativas_recursos","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Administración de recursos materiales.") !== false) {
            $this->model->setAttribute("acc_administrativas_materiales","1");
        }
        if ( strpos( $arreglo['acc_administrativas'], "Sistema de calidad.") !== false) {
            $this->model->setAttribute("acc_administrativas_sistema","1");
        }        
        if ( strpos( $arreglo['acc_administrativas'], "Otro.") !== false) {
            $this->model->setAttribute("acc_administrativas_otro","1");
        }
    }
    private function editdnc( $id)
    {        
        $this->model = $this->model->FindOrFail( $id);        
        $this->set_attributes();        
        return ( $this->model );
    }
    private function save(Request $request, $id)
    {
        $campos=        $this->get_campos_val();
        $mensajes=      $this->get_mensajes_val();
        $this->validate( $request, $campos, $mensajes);
        $datos_dncs = $this->fix_datos_dncs( $request);
        //dd($datos_dncs);
        $this->model->where('id', '=', $id)->update( $datos_dncs);
    }
    private function get_campos_val()
    {
        $campos=[
            'fk_cve_periodo'=> 'required|string|max:3|min:1',
            'num_emp'=> 'required|digits_between:1,999999999999',
            'nombre_completo'=> 'required|string|max:80|min:5',
            'dep_o_ent'=> 'required|string|max:180|min:5',
            'unidad_admva'=> 'required|string|max:180|min:1',
            'area'=> 'required|string|max:180|min:5',
            'grado_est'=> 'required|string|max:80|min:5',
            'correo'=> 'required|string|max:80|min:5',
            'telefono'=> 'required|string|max:40|min:10',
            'funciones'=> 'required|string|max:500|min:20',
        ];
        return $campos;
    }
    private function get_mensajes_val()
    {
        $mensajes=[            
            'fk_cve_periodo.required'=>'El Periodo es requerido, debe contener al menos 1 caracter.',
            'fk_cve_periodo.min'=>'El Periodo debe contener al menos 1 caracter.',
            'fk_cve_periodo.max'=>'El Periodo debe contener máximo 3 caracteres.',
            'num_emp.required'=>'El Número de Empleado es requerido y debe ser numérico',
            'num_emp.min'=>'El Periodo debe ser numérico, entero y mayor que cero.',
            'num_emp.max'=>'El Periodo debe ser numérico, entero y menor o igual a 999999999.',
            'nombre_completo.required'=>'El Nombre de Empleado es requerido y debe iniciar por los apellidos',
            'nombre_completo.min'=>'El Nombre del Empleado debe tener al menos 5 caracteres.',
            'nombre_completo.max'=>'El Nombre del Empleado debe tener como máximo 80 caracteres.',
            'dep_o_ent.required'=>'La Dependencia o Entidad es requerida',
            'dep_o_ent.min'=>'La Dependencia o Entidad debe tener al menos 5 caracteres.',
            'dep_o_ent.max'=>'La Dependencia o Entidad debe tener como máximo 180 caracteres.',
            'unidad_admva.required'=>'La Unidad Administrativa es requerida',
            'unidad_admva.min'=>'La Unidad Administrativa debe tener al menos 1 caracter.',
            'unidad_admva.max'=>'La Unidad Administrativa debe tener como máximo 180 caracteres.',
            'area.required'=>'El Area es requerida',
            'area.min'=>'El Area debe tener al menos 5 caracteres.',
            'area.max'=>'El Area debe tener como máximo 180 caracteres.',
            'grado_est.required'=>'El Grado de Estudios es requerida',
            'grado_est.min'=>'El Grado de Estudios debe tener al menos 5 caracteres.',
            'grado_est.max'=>'El Grado de Estudios debe tener como máximo 80 caracteres.',
            'correo.required'=>'El Correo Electrónico es requerida',
            'correo.min'=>'El Correo Electrónico debe tener al menos 5 caracteres.',
            'correo.max'=>'El Correo Electrónico debe tener como máximo 80 caracteres.',
            'telefono.required'=>'El Número de Teléfono es requerido',
            'telefono.min'=>'El Número de Teléfono debe tener al menos 10 caracteres e inclupir el area.',
            'telefono.max'=>'El Número de Teléfono debe tener como máximo 40 caracteres e inclupir el area.',
            'funciones.required'=>'Las Funciones son requeridas',
            'funciones.min'=>'Las Funciones deben tener al menos 20 caracteres.',
            'funciones.max'=>'Las Funciones deben tener como máximo 500 caracteres.',        
        ];
        return $mensajes;
    }    
    private function importdnc(Request $request) 
    {
      $datos= $this->get_user_data();
      //dd("hey");          
      $clean = $request->clean;
      if ($clean == 'Limpiar')
      {
          DB::table('dncs')->where('id', '>', 1)->delete();
          return back()->with('success', 'Tabla de Formatos DNC limpiada, excepto el primer registro.');
      } // end if ($clean)
      else 
      {
        $this->validate($request, 
          [ 'select_file'  => 'required|mimes:xls,xlsx'   ], 
          [ 'select_file.required'=>'Se pide un archivo de Excel con extensión .xls o .xlsx' ]
        );
         $path1 = $request->file('select_file')->store('temp'); 
         $path = storage_path('app').'/'.$path1;          
         try {
          //dd($path1);
          $data = Excel::toCollection(new DncsImport, $path);          
          $existentes = 0;          
          if($data->count() > 0)
          {
           foreach($data->toArray() as $key => $value)
           {            
            foreach($value as $row)
            {
              //dd(count($row) );
              if (! (               
                isset($row['cve_plantilla']) &&
                isset($row['cve_periodo']) &&
                isset($row['num_emp']) &&
                isset($row['nombre_completo']) &&
                isset($row['dep_o_ent']) &&
                isset($row['unidad_admva']) &&
                isset($row['area']) &&
                isset($row['grado_est']) &&
                isset($row['correo']) &&
                isset($row['telefono']) &&
                isset($row['funciones']) 
                ))
              {                 
                  return back()->with('success', 
                  'Error: El archivo de Excel de Formatos de DNC debe tener las columnas siguientes : '.
                  "cve_plantilla, cve_periodo, num_emp, nombre_completo, dep_o_ent, unidad_admva, ".
                  "area, grado_est, correo, telefono, funciones. ".
                  "Alguno de ellos esta faltando. ".
                  "Vea la documentación Técnica para importar formatos DNC llenos y vacíos."
                   );                
              } // end if(!)
              //dd($row);
              $dncs = DB::table('dncs')
                ->where('num_emp', $row['num_emp'])
                ->where('fk_cve_periodo', $row['cve_periodo'])
                ->get();
              if ( $dncs->isNotEmpty()) 
              {
                $existentes= $existentes + 1;
              }
              else 
              { 
               if (isset($row['word_int'])) {
                $insert_data[] = array(                
                    'fk_id_plantillas'    => $row['cve_plantilla'],
                    'fk_cve_periodo'      => $row['cve_periodo'],
                    'num_emp'             => $row['num_emp'],
                    'nombre_completo'     => $row['nombre_completo'],
                    'dep_o_ent'           => $row['dep_o_ent'],
                    'unidad_admva'        => $row['unidad_admva'],
                    'area'                => $row['area'],
                    'grado_est'           => $row['grado_est'],
                    'correo'              => $row['correo'],
                    'telefono'            => $row['telefono'],
                    'funciones'           => $row['funciones'],
                    'word_int'            => $row['word_int'],
                    'word_ava'            => $row['word_ava'],
                    'excel_int'           => $row['excel_int'],
                    'excel_ava'           => $row['excel_ava'],
                    'power_point'         => $row['power_point'],
                    'nuevas_tec'          => $row['nuevas_tec'],
                    'acc_institucionales' => $row['acc_institucionales'],
                    'acc_des_humano'      => $row['acc_des_humano'],
                    'acc_administrativas' => $row['acc_administrativas'],
                    'otro_curso'          => $row['otro_curso'],
                    'interes_instructor'  => $row['interes_instructor'],
                    'tema'                => $row['tema']
                    );                  
               } // end if isset($row['word_int'])
               else
               {
                $insert_data[] = array(                
                    'fk_id_plantillas'    => $row['cve_plantilla'],
                    'fk_cve_periodo'      => $row['cve_periodo'],
                    'num_emp'             => $row['num_emp'],
                    'nombre_completo'     => $row['nombre_completo'],
                    'dep_o_ent'           => $row['dep_o_ent'],
                    'unidad_admva'        => $row['unidad_admva'],
                    'area'                => $row['area'],
                    'grado_est'           => $row['grado_est'],
                    'correo'              => $row['correo'],
                    'telefono'            => $row['telefono'],
                    'funciones'           => $row['funciones']);
               } //end else isset($row['word_int'])
              } // end if( $row)
            } // end foreach($value as $row)
           } // end foreach($data->toArray() as $key => $value)
           $suma = 0;
           if(!empty($insert_data))
           {
            //dd($insert_data);
            $suma = count($insert_data);
            //DB::table('dncs')->insert($insert_data);
            foreach (array_chunk($insert_data,1000) as $t) 
            {
                DB::table('dncs')->insert($t);
            }
           } // end if(!empty)
          } // end if($data)
          return back()->with('success', 
            'El archivo de Formatos DNC de Excel se subió con éxito. '.
            "Se repitieron ".$existentes." registro(s)".
            " y se subieron ".$suma. " registro(s).");         
          //$data = Excel::import(new UsersImport,$path);
          //return back()->with('success', 'El archivo de Uusarios de Excel se subió con éxito.');
        } 
        catch (\Illuminate\Database\QueryException $e) 
        {
            return back()->with('success', 'Ocurrió un error:  '.$e->errorInfo[2]);
        } // end catch
      } // end else $clean == 'Limpiar'
    } // end import function   
    private function export( $action) 
    {
        if ($action== "1") {
            return Excel::download(new UsersExport, 'usuarios.xlsx');
        }
        if ($action== "2") {
            return Excel::download(new DncsExport, 'dncs.xlsx');
        }
        if ($action== "3") {
            return Excel::download(new PlantillasExport, 'plantillas.xlsx');        
        }
        return ('Opción Inválida'); 
    }
    private function reportes( $repo) 
    {
    $periodos           = $this->periodos();    
    if ($repo == "1")
          {
            return "En proceso reporte de Usuarios";
          }
    if ($repo == "2")
          {            
            return $this->repo_dnc();
          }
    if ($repo == "3")
          {
            return "En proceso reporte de Plantillas";
         }          
    }
    private function dependencias()
    {           
        return Dncs::all()->sortBy("dep_o_ent")->unique("dep_o_ent");
    }
    private function unidades()
    {   
        return Dncs::all()->sortBy("unidad_admva")->unique("unidad_admva");
    }
    private function areas()
    {   
        return Dncs::all()->sortBy("area")->unique("area");
    }
    private function First()
    {             
        return( $this->model->First());
    }
    private function repo_dnc()
    {           
        $dncs =  $this->First();
        $dependencia = $this->dependencias();
        $unidad = $this->unidades();
        $area = $this->areas();
        $periodo = $this->periodos();            
        $periodo_ini = $periodo->First()->cve_periodo;
        $periodo_fin = $periodo->Last()->cve_periodo;    
        $dependencia_ini = $dependencia->First()->dep_o_ent;
        $dependencia_fin = $dependencia->Last()->dep_o_ent;        
        $unidad_ini = $unidad->First()->unidad_admva;
        $unidad_fin = $unidad->Last()->unidad_admva;    
        $area_ini = $area->First()->area;
        $area_fin = $area->Last()->area;
        return view('admin/Dncsrepos',
            compact('dependencia','unidad','area','dncs','periodo',
            'periodo_ini','periodo_fin',
            'dependencia_ini','dependencia_fin',
            'unidad_ini','unidad_fin',
            'area_ini','area_fin'
            ));        
    }     
    private function dncsrepodet( Request $request)
    {
        if ($request->num_emp> "0") 
        {            
            $dncs = DB::table('dncs')
            ->join('plantillas', 'plantillas.id', '=', 'dncs.fk_id_plantillas')
            ->join('periodos', 'periodos.cve_periodo', '=', 'dncs.fk_cve_periodo')            
            ->orderBy('dncs.num_emp', 'ASC')
            ->orderBy('dncs.fk_cve_periodo', 'DESC')
            ->where('dncs.fk_cve_periodo', '>=',$request->periodoini)
            ->where('dncs.fk_cve_periodo', '<=',$request->periodofin)
            ->where('dncs.dep_o_ent', '>=',$request->dependenciaini)
            ->where('dncs.dep_o_ent', '<=',$request->dependenciafin)
            ->where('dncs.unidad_admva', '>=',$request->unidadini)
            ->where('dncs.unidad_admva', '<=',$request->unidadfin)
            ->where('dncs.area', '>=',$request->areaini)
            ->where('dncs.area', '<=',$request->areafin)
            ->where('dncs.num_emp', '=',$request->num_emp)
            ->select(
                'dncs.id',
                'dncs.fk_id_plantillas',
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
                'dncs.tema',
                'dncs.activo',            
                'periodos.descripcion as periodo_descripcion')                
            ->get();
        } else 
        {                
            $dncs = DB::table('dncs')
            ->join('plantillas', 'plantillas.id', '=', 'dncs.fk_id_plantillas')
            ->join('periodos', 'periodos.cve_periodo', '=', 'dncs.fk_cve_periodo')            
            ->orderBy('dncs.num_emp', 'ASC')
            ->orderBy('dncs.fk_cve_periodo', 'DESC')
            ->where('dncs.fk_cve_periodo', '>=',$request->periodoini)
            ->where('dncs.fk_cve_periodo', '<=',$request->periodofin)
            ->where('dncs.dep_o_ent', '>=',$request->dependenciaini)
            ->where('dncs.dep_o_ent', '<=',$request->dependenciafin)
            ->where('dncs.unidad_admva', '>=',$request->unidadini)
            ->where('dncs.unidad_admva', '<=',$request->unidadfin)
            ->where('dncs.area', '>=',$request->areaini)
            ->where('dncs.area', '<=',$request->areafin)
            ->select(
                'dncs.id',
                'dncs.fk_id_plantillas',
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
                'dncs.tema',
                'dncs.activo',            
                'periodos.descripcion as periodo_descripcion')
            ->get();
        } // ENDIF
        if(count($dncs) < 1) 
        {
            $err = 'Error: No se encontró ningún Formato DNC con las condiciones: No. de empleado='. 
                $request->num_emp. 
                ", dependencia>=". $request->dependenciaini.
                " y dependencia<=".$request->dependenciafin. 
                ", UA >=".$request->unidadini.
                "y  UA <=".$request->unidadfin.
                ", area >=".$request->areani.
                "y  area <=".$request->areafin.
                ", periodo inicial >=".
                $request->periodoini." y periodo final<=".$request->periodofin;
            return back()->with('mensaje', $err);
        } else 
        {
            return view('admin/Dncsreporte',compact('dncs'));
        }
    }   
    private function dncsrepodep( Request $request)
    {
       $dncs = DB::table('dncs')
            ->where('dncs.fk_cve_periodo', '>=',$request->periodoini)
            ->where('dncs.fk_cve_periodo', '<=',$request->periodofin)
            ->where('dncs.dep_o_ent', '>=',$request->dependenciaini)
            ->where('dncs.dep_o_ent', '<=',$request->dependenciafin)
            ->where('dncs.unidad_admva', '>=',$request->unidadini)
            ->where('dncs.unidad_admva', '<=',$request->unidadfin)
            ->where('dncs.area', '>=',$request->areaini)
            ->where('dncs.area', '<=',$request->areafin)
            ->GroupBy('dep_o_ent')
            ->selectRaw('count(id) as total, dep_o_ent')
            ->get();

        if(count($dncs) < 1) 
        {
            $err = 'Error: No se encontró ningún Formato DNC con las condiciones: '.
                " dependencia>=". $request->dependenciaini.
                " y dependencia<=".$request->dependenciafin. 
                ", UA >=".$request->unidadini.
                "y  UA <=".$request->unidadfin.
                ", area >=".$request->areani.
                "y  area <=".$request->areafin.
                ", periodo inicial >=".
                $request->periodoini." y periodo final<=".$request->periodofin;
            return back()->with('mensaje', $err);
        } else 
        {
            return view('admin/Dncsrepodep',compact('dncs'));
        }
    }    
    private function plantilla()
    {
        return $this->plantilla;
    }
    private function get_model() 
    {
        return $this->model;
    }
    private function searchmain()
    {
        if ($this->search($request)=="SI" ) {
            $perfil_usuarios    = $this->perfil_usuarios();
            $usuarios           = $this->usuarios();
            $periodos           = $this->periodos();
            // pone el ultimo periodo activo registrado
            // y agrega los datos de la plabtilla
            $dncs               = $this->get_model();
            return view('admin/Dncs.createval', compact(
                'usuarios',
                'perfil_usuarios',
                'periodos',
                'dncs'
              ));
          }
          else {
            return redirect("/admin/Dncs")->with('mensaje','Empleado no localizado.');
          }
    }
    private function createval() 
    {
        // otros datos
        $perfil_usuarios    = $this->perfil_usuarios();
        $usuarios           = $this->usuarios();
        $periodos           = $this->periodos();
        $des_uper           = $this->ultimo_periodo->descripcion;
        // y agrega los datos de la plabtilla
        $dncs               = $this->get_model();
        return view('admin/Dncs.createval', compact(
            'des_uper',
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'
        ));      
    }
    private function createval2() 
    {
        // otros datos
        $perfil_usuarios    = $this->perfil_usuarios();
        $usuarios           = $this->usuarios();
        $periodos           = $this->periodos();
        $des_uper           = $this->ultimo_periodo->descripcion;
        // y agrega los datos de la plabtilla
        $dncs               = $this->get_model();
        return view('admin/Dncs.createval2', compact(
            'des_uper',
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'
        ));      
    }
    private function session_to_model()
    {
        $model = Session::get('model');
        Session::forget('model');
        $this->model->fk_cve_periodo           = $model['fk_cve_periodo'];
        $this->model->num_emp                  = $model['num_emp'];
        $this->model->nombre_completo          = $model['nombre_completo'];
        $this->model->dep_o_ent                = $model['dep_o_ent'];
        $this->model->unidad_admva             = $model['unidad_admva'];
        $this->model->area                     = $model['area'];
        $this->model->grado_est                = $model['grado_est'];
        $this->model->correo                   = $model['correo'];
        $this->model->telefono                 = $model['telefono'];
        $this->model->funciones                = trim($model['funciones']);
        $this->model->word_int_tablas          = $model['word_int_tablas'];
        $this->model->word_ava_correspondencia = $model['word_ava_correspondencia'];
        $this->model->excel_int_graficos       = $model['excel_int_graficos'];
        $this->model->excel_int_formulas       = $model['excel_int_formulas'];
        $this->model->excel_ava_herramientas   = $model['excel_ava_herramientas'];
        $this->model->excel_ava_funciones      = $model['excel_ava_funciones'];
        $this->model->power_point_cualidades   = $model['power_point_cualidades'];
        $this->model->nuevas_tec_competencia   = $model['nuevas_tec_competencia'];
        $this->model->nuevas_tec_zoom          = $model['nuevas_tec_zoom'];
        $this->model->acc_institucionales_etica = $model['acc_institucionales_etica'];
        $this->model->acc_institucionales_valores = $model['acc_institucionales_valores'];
        $this->model->acc_institucionales_responsabilidades = $model['acc_institucionales_responsabilidades'];
        $this->model->acc_institucionales_metodologia = $model['acc_institucionales_metodologia'];
        $this->model->acc_institucionales_identificacion = $model['acc_institucionales_identificacion'];        
        $this->model->acc_institucionales_violencia = $model['acc_institucionales_violencia'];
        $this->model->acc_institucionales_seguridad = $model['acc_institucionales_seguridad'];
        $this->model->acc_institucionales_norma = $model['acc_institucionales_norma'];
        $this->model->acc_institucionales_induccion = $model['acc_institucionales_induccion'];
        $this->model->acc_institucionales_actualizacion = $model['acc_institucionales_actualizacion'];
        $this->model->acc_institucionales_documentos = $model['acc_institucionales_documentos'];        
        $this->model->acc_institucionales_politicas = $model['acc_institucionales_politicas'];
        $this->model->acc_institucionales_correcto = $model['acc_institucionales_correcto'];        
        $this->model->acc_institucionales_protocolos = $model['acc_institucionales_protocolos'];
        $this->model->acc_institucionales_vocacion = $model['acc_institucionales_vocacion'];
        $this->model->acc_des_humano_solucion = $model['acc_des_humano_solucion'];
        $this->model->acc_des_humano_como = $model['acc_des_humano_como'];
        $this->model->acc_des_humano_comunicacion = $model['acc_des_humano_comunicacion'];
        $this->model->acc_des_humano_importancia = $model['acc_des_humano_importancia'];        
        $this->model->acc_des_humano_inteligencia = $model['acc_des_humano_inteligencia'];
        $this->model->acc_administrativas_actualizacion = $model['acc_administrativas_actualizacion'];
        $this->model->acc_administrativas_cumplimiento = $model['acc_administrativas_cumplimiento'];
        $this->model->acc_administrativas_administracion = $model['acc_administrativas_administracion'];
        $this->model->acc_administrativas_clima = $model['acc_administrativas_clima'];
        $this->model->acc_administrativas_modernizacion = $model['acc_administrativas_modernizacion'];
        $this->model->acc_administrativas_recursos = $model['acc_administrativas_recursos'];
        $this->model->acc_administrativas_materiales = $model['acc_administrativas_materiales'];
        $this->model->acc_administrativas_sistema = $model['acc_administrativas_sistema'];
        $this->model->acc_administrativas_otro = $model['acc_administrativas_otro'];
        $this->model->otro_curso = $model['otro_curso'];
        $this->model->interes_instructor = $model['interes_instructor'];
        $this->model->tema = $model['tema'];        
        $this->model->activa = $model['activa'];        
        $this->model->activao = $model['activao'];
        $this->model->fk_id_plantillas = $model['fk_id_plantillas']; 
        // otros datos
        $this->model->activo                   = $model['activo'];
        $this->model->word_int                 = $model['word_int'];
        $this->model->word_ava                 = $model['word_ava'];
        $this->model->excel_int                = $model['excel_int'];
        $this->model->excel_ava                = $model['excel_ava'];
        $this->model->power_point              = $model['power_point'];
        $this->model->nuevas_tec               = $model['nuevas_tec'];
        $this->model->acc_institucionales      = $model['acc_institucionales'];
        $this->model->acc_des_humano           = $model['acc_des_humano'];        
        $this->model->acc_administrativas      = $model['acc_administrativas'];                              
    }
    private function request_to_model( $request)
    {            
        $this->model->fk_cve_periodo = $request->fk_cve_periodo;
        $this->model->nombre_completo = $request->nombre_completo;
        $this->model->num_emp = $request->num_emp;
        $this->model->dep_o_ent = $request->dep_o_ent;
        $this->model->unidad_admva = $request->unidad_admva;
        $this->model->area = $request->area;
        $this->model->grado_est = $request->grado_est;
        $this->model->correo = $request->correo;
        $this->model->telefono = $request->telefono;
        $this->model->funciones = trim($request->funciones);
        $this->model->word_int_tablas = $request->word_int_tablas;
        $this->model->word_ava_correspondencia = $request->word_ava_correspondencia;
        $this->model->excel_int_graficos = $request->excel_int_graficos;
        $this->model->excel_int_formulas = $request->excel_int_formulas;
        $this->model->excel_ava_herramientas = $request->excel_ava_herramientas;
        $this->model->excel_ava_funciones = $request->excel_ava_funciones;
        $this->model->power_point_cualidades = $request->power_point_cualidades;
        $this->model->nuevas_tec_competencia = $request->nuevas_tec_competencia;
        $this->model->nuevas_tec_zoom = $request->nuevas_tec_zoom;
        $this->model->acc_institucionales_etica = $request->acc_institucionales_etica;
        $this->model->acc_institucionales_valores = $request->acc_institucionales_valores;
        $this->model->acc_institucionales_responsabilidades = $request->acc_institucionales_responsabilidades;
        $this->model->acc_institucionales_metodologia = $request->acc_institucionales_metodologia;
        $this->model->acc_institucionales_identificacion = $request->acc_institucionales_identificacion;
        $this->model->acc_institucionales_violencia = $request->acc_institucionales_violencia;
        $this->model->acc_institucionales_seguridad = $request->acc_institucionales_seguridad;
        $this->model->acc_institucionales_norma = $request->acc_institucionales_norma;
        $this->model->acc_institucionales_induccion = $request->acc_institucionales_induccion;
        $this->model->acc_institucionales_actualizacion = $request->acc_institucionales_actualizacion;
        $this->model->acc_institucionales_documentos = $request->acc_institucionales_documentos;
        $this->model->acc_institucionales_politicas = $request->acc_institucionales_politicas;
        $this->model->acc_institucionales_correcto = $request->acc_institucionales_correcto;
        $this->model->acc_institucionales_protocolos = $request->acc_institucionales_protocolos;
        $this->model->acc_institucionales_vocacion = $request->acc_institucionales_vocacion;
        $this->model->acc_des_humano_solucion = $request->acc_des_humano_solucion;
        $this->model->acc_des_humano_como = $request->acc_des_humano_como;
        $this->model->acc_des_humano_comunicacion = $request->acc_des_humano_comunicacion;
        $this->model->acc_des_humano_importancia = $request->acc_des_humano_importancia;
        $this->model->acc_des_humano_inteligencia = $request->acc_des_humano_inteligencia;
        $this->model->acc_administrativas_actualizacion = $request->acc_administrativas_actualizacion;
        $this->model->acc_administrativas_cumplimiento = $request->acc_administrativas_cumplimiento;
        $this->model->acc_administrativas_administracion = $request->acc_administrativas_administracion;
        $this->model->acc_administrativas_clima = $request->acc_administrativas_clima;
        $this->model->acc_administrativas_modernizacion = $request->acc_administrativas_modernizacion;
        $this->model->acc_administrativas_recursos = $request->acc_administrativas_recursos;
        $this->model->acc_administrativas_materiales = $request->acc_administrativas_materiales;
        $this->model->acc_administrativas_sistema = $request->acc_administrativas_sistema;
        $this->model->acc_administrativas_otro = $request->acc_administrativas_otro;
        $this->model->otro_curso = $request->otro_curso;
        $this->model->interes_instructor = $request->interes_instructor;
        $this->model->tema = $request->tema;
        $this->model->activa = $request->activa;  
        $this->model->activo = $request->activo;  
        $this->model->activao = $request->activao;
        $this->model->fk_id_plantillas = $request->fk_id_plantillas;      
    }    
    // aqui brinca el boton de grabar/agregar
    private function model_to_session()
    {
        Session::forget('model');
        Session::put('model', $this->model);
    }
    private function model_to_request()
    {
        $request = new Request();
        $request->fk_cve_periodo = $this->model->fk_cve_periodo ;
        $request->nombre_completo = $this->model->nombre_completo;
        $request->num_emp = $this->model->num_emp;
        $request->dep_o_ent = $this->model->dep_o_ent;
        $request->unidad_admva = $this->model->unidad_admva;
        $request->area = $this->model->area;
        $request->grado_est = $this->model->grado_est ;
        $request->correo = $this->model->correo ;
        $request->telefono = $this->model->telefono ;
        $request->funciones = trim($this->model->funciones) ;
        $request->word_int_tablas = $this->model->word_int_tablas ;
        $request->word_ava_correspondencia = $this->model->word_ava_correspondencia ;
        $request->excel_int_graficos = $this->model->excel_int_graficos ;
        $request->excel_int_formulas = $this->model->excel_int_formulas ;
        $request->excel_ava_herramientas = $this->model->excel_ava_herramientas ;
        $request->excel_ava_funciones = $this->model->excel_ava_funciones ;
        $request->power_point_cualidades =$this->model->power_point_cualidades  ;
        $request->nuevas_tec_competencia = $this->model->nuevas_tec_competencia;
        $request->nuevas_tec_zoom = $this->model->nuevas_tec_zoom;
        $request->acc_institucionales_etica = $this->model->acc_institucionales_etica ;
        $request->acc_institucionales_valores = $this->model->acc_institucionales_valores ;
        $request->acc_institucionales_responsabilidades = $this->model->acc_institucionales_responsabilidades ;
        $request->acc_institucionales_metodologia = $this->model->acc_institucionales_metodologia ;
        $request->acc_institucionales_identificacion = $this->model->acc_institucionales_identificacion ;
        $request->acc_institucionales_violencia = $this->model->acc_institucionales_violencia ;
        $request->acc_institucionales_seguridad = $this->model->acc_institucionales_seguridad ;
        $request->acc_institucionales_norma = $this->model->acc_institucionales_norma ;
        $request->acc_institucionales_induccion = $this->model->acc_institucionales_induccion ;
        $request->acc_institucionales_actualizacion = $this->model->acc_institucionales_actualizacion ;
        $request->acc_institucionales_documentos = $this->model->acc_institucionales_documentos ;
        $request->acc_institucionales_politicas = $this->model->acc_institucionales_politicas ;
        $request->acc_institucionales_correcto = $this->model->acc_institucionales_correcto ;
        $request->acc_institucionales_protocolos = $this->model->acc_institucionales_protocolos ;
        $request->acc_institucionales_vocacion = $this->model->acc_institucionales_vocacion ;
        $request->acc_des_humano_solucion = $this->model->acc_des_humano_solucion ;
        $request->acc_des_humano_como = $this->model->acc_des_humano_como ;
        $request->acc_des_humano_comunicacion = $this->model->acc_des_humano_comunicacion ;
        $request->acc_des_humano_importancia = $this->model->acc_des_humano_importancia ;
        $request->acc_des_humano_inteligencia = $this->model->acc_des_humano_inteligencia ;
        $request->acc_administrativas_actualizacion = $this->model->acc_administrativas_actualizacion ;
        $request->acc_administrativas_cumplimiento = $this->model->acc_administrativas_cumplimiento ;
        $request->acc_administrativas_administracion = $this->model->acc_administrativas_administracion ;
        $request->acc_administrativas_clima = $this->model->acc_administrativas_clima ;
        $request->acc_administrativas_modernizacion = $this->model->acc_administrativas_modernizacion ;
        $request->acc_administrativas_recursos = $this->model->acc_administrativas_recursos ;
        $request->acc_administrativas_materiales = $this->model->acc_administrativas_materiales ;
        $request->acc_administrativas_sistema = $this->model->acc_administrativas_sistema ;
        $request->acc_administrativas_otro = $this->model->acc_administrativas_otro ;
        $request->otro_curso = $this->model->otro_curso ;
        $request->interes_instructor = $this->model->interes_instructor ;
        $request->tema = $this->model->tema ;
        $request->activa = $this->model->activa ;
        $request->activo = $this->model->activo ;
        $request->activao = $this->model->activao ;
        $request->fk_id_plantillas = $this->model->fk_id_plantillas ; 
        return $request;
    }
    private function consideraciones()
    {
        $datos = [
            "usuario"=>Auth::user()->name,
            "cve_perfil_usuario"=>Auth::user()->fk_cve_perfil_usuario,
            "email"=>Auth::user()->email,
            "success"=>""
        ];
        $vista= view('consideraciones',$datos);
        return $vista;
    }
    private function indexblank()
    {
      $perfil_usuarios    = $this->perfil_usuarios();
      $usuarios           = $this->usuarios();
      $periodos           = $this->periodos();
      $dependencias       = $this->dependencias_de_plantillas();
      $dncs               = $this->dncs_blank();
      $dncs->fk_cve_periodo= "221";
      return view('admin/Dncs.blank', compact(
        'perfil_usuarios',
        'periodos',
        'usuarios',
        'dependencias',
        'dncs'
      ));
    }
    private function indexcrud()
    {        
        $perfil_usuarios    = $this->perfil_usuarios();
        $usuarios           = $this->usuarios();
        $periodos           = $this->periodos();
        $datos['dncs']      = $this->all(); 
        return view('admin/Dncs.index', $datos, compact(
          'perfil_usuarios',
          'periodos',
          'usuarios'));      
    }
    // aqui brinca el boton de grabar/agregar
    private function insert( Request $request)
    {
        //dd($request);
        $this->request_to_model( $request);
        $this->model_to_session();
        $campos=        $this->get_campos_val();
        $mensajes=      $this->get_mensajes_val();   
        $this->validate( $request, $campos, $mensajes);
        $dncs= $this->fix_datos_dncs( $request);
        $this->model->insert( $dncs);
    }   
}