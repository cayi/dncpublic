<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\PerfiluserRepository;

class PerfiluserController extends Controller
{
    private $perfil_userRepository; 
    public function __construct( PerfiluserRepository $perfiluserRepository)
    {
        $this->perfiluserRepository = $perfiluserRepository;
        $this->middleware('auth');
    }
    public function index()
    {   
        //dd("hey");
        $datos['Perfiluser'] = $this->perfiluserRepository->All();
        return view('admin/Perfiluser.index', $datos);
    }
    public function create()
    {                
        $perfiluser = $this->perfil_user->Perfiluser_blank();
        return view('admin/Perfiluser.create', compact('perfiluser'));
    }
    public function store(Request $request)
    {        
        $this->perfiluserRepository->insert( $request);
        return redirect("/admin/Perfiluser")->with('mensaje','Nuevo Perfil de Usuario Agergado.');
    }
    public function destroy( $id)
    {        
        $this->perfiluserRepository->delete( $id);
        return redirect("/admin/Perfiluser")->with('mensaje','Perfil de Usuario Borrado.');
    }
    public function edit($id)
    {                
        $perfiluser = $this->perfiluserRepository->edit( $id);
        return view('admin/Perfiluser.edit', compact('perfiluser'));
    }
    public function update(Request $request, $id)
    {   
        $this->perfiluserRepository->save( $request, $id); 
        return redirect("/admin/Perfiluser")->with('mensaje','Perfil de Usuario Actualizado.');
    }
}