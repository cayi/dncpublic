<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\DncsRepository;

class DncsController extends Controller
{
    private $dncsRepository; 
    public function __construct( DncsRepository $dncsRepository)
    {
        $this->dncsRepository = $dncsRepository;
        $this->middleware('auth');
    }
    
    // Menu de Administrador
    public function indexAdmin()
    {   
        return ( $this->dncsRepository->indexAdmin());
    }

    public function indexcrud()
    {        
        $perfil_usuarios    = $this->dncsRepository->perfil_usuarios();
        $usuarios           = $this->dncsRepository->usuarios();
        $periodos           = $this->dncsRepository->periodos();
        $datos['dncs']      = $this->dncsRepository->all(); 
        //dd($dependencias);
        return view('admin/Dncs.index', $datos, compact(
          'perfil_usuarios',
          'periodos',
          'usuarios'));      
    }
    public function indexblank()
    {
      $perfil_usuarios    = $this->dncsRepository->perfil_usuarios();
      $usuarios           = $this->dncsRepository->usuarios();
      $periodos           = $this->dncsRepository->periodos();
      $dependencias       = $this->dncsRepository->dependencias_de_plantillas();
      $dncs               = $this->dncsRepository->dncs_blank();
      $dncs->fk_cve_periodo= "221";
      //$dncs->num_emp
      //print_r("num_emp".$dncs->num_emp);
      //dd($dependencias);
      return view('admin/Dncs.blank', compact(
        'perfil_usuarios',
        'periodos',
        'usuarios',
        'dependencias',
        'dncs'
      ));
    }
    public function index()
    { 
      //dd($this->dncsRepository->es_administrador());
      if ( $this->dncsRepository->es_administrador() == "Si") 
      { 
        return $this->indexcrud();        
      }
      else 
      {
        return $this->indexblank();
      }       
    }
    public function create()
    {
        $perfil_usuarios    = $this->dncsRepository->perfil_usuarios();
        $usuarios           = $this->dncsRepository->usuarios();
        $periodos           = $this->dncsRepository->periodos();
        $dncs               = $this->dncsRepository->dncs_blank();
        return view('admin/Dncs.create', compact(
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'));
    }    
    // aqui brinca el boton de grabar
    public function store(Request $request)
    {    
        //dd($request);
        $this->dncsRepository->insert( $request);
        return redirect("/admin/Dncs")->with('mensaje','Nuevo Formato DNC Agergado.');
    }
    public function destroy( $id)
    {
        //dd("del");        
        $this->dncsRepository->delete( $id);        
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Borrado.');
    }
    public function edit( $id)
    {
        //dd("he");
        $perfil_usuarios    = $this->dncsRepository->perfil_usuarios();
        $usuarios           = $this->dncsRepository->usuarios();
        $periodos           = $this->dncsRepository->periodos();
        $dncs               = $this->dncsRepository->edit( $id);
        return view('admin/Dncs.edit', compact(
            'usuarios',
            'perfil_usuarios',
            'periodos',
            'dncs'));
    }
    public function update(Request $request, $id)
    {   
        //dd("he");
        $this->dncsRepository->save( $request, $id); 
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Actualizado.');
    }
    function import(Request $request)    
    {
      if ( $this->dncsRepository->es_administrador() == "Si") 
      {
        return $this->dncsRepository->import( $request);
      }
      else
      {        
        return $this->dncsRepository->get_user_data();
      }
    }
    function indeximport()
    { 
      if ( $this->dncsRepository->es_administrador() == "Si") 
      {
          $periodos           = $this->dncsRepository->periodos();
          $dncs               = $this->dncsRepository->all();          
          return view('/admin/Dncs/Import', compact('dncs','periodos'));
      }
      else
      {        
        return $this->dncsRepository->get_user_data();        
      }
    }
    function repo( $repo)
    { 
      if ( $this->dncsRepository->es_administrador() == "Si") 
      {
          return $this->dncsRepository->reportes( $repo);
      }
      else
      {        
        return $this->dncsRepository->get_user_data();        
      }
    }
    function exp( $exp)
    {
        if ( $this->dncsRepository->es_administrador() == "Si") 
        {
          return $this->dncsRepository->export( $exp);
        }
        else
        {        
          return $this->dncsRepository->get_user_data();
        }
    }
    function dncsrepo( Request $request)
    {
        return $this->dncsRepository->dncsrepo( $request);
    }
    public function Show(Request $request)
    { 
      return $this->dncsRepository->Show( $request);      
    } 
    public function search(Request $request)
    {
      return $this->dncsRepository->search( $request);
    }
}