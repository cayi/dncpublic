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
        return $this->dncsRepository->indexAdmin();
    }
    public function index()
    { 
        return $this->dncsRepository->indexdnc();      
    }
    public function create()
    {
        return $this->dncsRepository->create();
    }    
    // aqui brinca el boton de grabar
    public function store(Request $request)
    {         
        return $this->dncsRepository->store( $request);
    }
    public function destroy( $id)
    {     
        return $this->dncsRepository->destroydnc( $id);
    }
    public function edit( $id)
    {   
        //dd("edit");     
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
    function indeximport()
    { 
        return $this->dncsRepository->indeximport();
    }
    function repo( $repo)
    { 
        return $this->dncsRepository->repo( $repo);
    }
    function exp( $exp)
    {
        return $this->dncsRepository->exp( $exp);        
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