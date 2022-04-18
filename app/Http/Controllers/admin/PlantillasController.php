<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\PlantillasRepository;

class PlantillasController extends Controller
{
    private $plantillasRepository; 
    public function __construct( PlantillasRepository $plantillasRepository)
    {
        $this->plantillasRepository = $plantillasRepository;
        $this->middleware('auth');
    }    
    public function index()
    {
        //dd("index cap");
        return  $this->plantillasRepository->indexplantillas();
    }
    public function create()
    {
        return $this->plantillasRepository->create();
    }  
    public function store(Request $request)
    {        
        return $this->plantillasRepository->store( $request);        
    }
    public function destroy( $id)
    {        
        return $this->plantillasRepository->destroy( $id);                
    }
    public function edit( $id)
    {        
        return $this->plantillasRepository->editplantillas( $id);
    }
    public function update(Request $request, $id)
    {   
        return $this->plantillasRepository->update( $request, $id);        
    }
    function import(Request $request)    
    {      
        return  $this->plantillasRepository->importplantillas();
    }
    function indeximport()
    {
       return $this->plantillasRepository->indeximport();
    }
    public function Show()
    { 
        dd("show");
        return $this->plantillasRepository->Show();
    } 
}