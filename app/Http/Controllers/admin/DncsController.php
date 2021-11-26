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
    public function index()
    {   
        //dd("hey");
        $perfil_usuarios    = $this->dncsRepository->perfil_usuarios();
        $usuarios           = $this->dncsRepository->usuarios();
        $periodos           = $this->dncsRepository->periodos();
        $datos['dncs']      = $this->dncsRepository->all(); 
        //dd($periodos);
        return view('admin/Dncs.index', $datos, compact(
            'perfil_usuarios',
            'periodos',
            'usuarios'
        ));
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
    public function store(Request $request)
    {        
        $this->dncsRepository->insert( $request);
        return redirect("/admin/Dncs")->with('mensaje','Nuevo Formato DNC Agergado.');
    }
    public function destroy( $id)
    {        
        $this->dncsRepository->delete( $id);        
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Borrado.');
    }
    public function edit( $id)
    {
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
        $this->dncsRepository->save( $request, $id); 
        return redirect("/admin/Dncs")->with('mensaje','Formato DNC Actualizado.');
    }
}