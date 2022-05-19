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
        //dd("indexAdmin");
        return $this->dncsRepository->indexAdmin();
    }
    // Menu del Capturista
    public function index()
    {      
        return $this->dncsRepository->index();
    }
    public function create( Request $request)
    {        
        return $this->dncsRepository->create( $request);
    }    
    // aqui brinca el boton de grabar, pero tambien el de confirmar
    public function store(Request $request)
    {        
        return $this->dncsRepository->store( $request);
    }
    public function destroy(Request $request, $id)
    {     
        return $this->dncsRepository->destroy( $request, $id);
    }
    public function edit(Request $request, $id)
    {           
        return $this->dncsRepository->edit( $id);        
    }
    public function update(Request $request, $id)
    {           
        return $this->dncsRepository->update( $request, $id);         
    }
    function import(Request $request)    
    {
        return $this->dncsRepository->import( $request);      
    }
    function indeximport( Request $request)
    { 
        return $this->dncsRepository->indeximport( $request);
    }
    function repo( Request $request, $repo)
    { 
        return $this->dncsRepository->repo( $request, $repo);
    }
    function exp( Request $request, $exp)
    {
        return $this->dncsRepository->exp( $request, $exp);        
    }
    function dncsrepo( Request $request)
    {
        return $this->dncsRepository->dncsrepo( $request);
    }
    public function show( Request $request)
    {         
        return $this->dncsRepository->show( $request);
    } 
    public function search(Request $request)
    {    
        return $this->dncsRepository->search( $request);
    }
    public function searchtoo(Request $request)
    {    
        return $this->dncsRepository->searchtoo( $request);
    }
}