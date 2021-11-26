<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Perfilusers;

class UsuariosRepository extends Controller
{
    private $model;
    public function __construct()
    {        
        $this->model = New User();
    }
    public function perfil_usuarios()
    {
        return( Perfilusers::all()->SortBy('cve_perfil_usuario'));
    }
    public function usuarios_blank(){                    
        $usuarios = User::FindOrFail(1);
        $usuarios->fk_cve_perfil_usuario = "U";
        $usuarios->descripcion = "";
        $usuarios->activo = true;
        return ( $usuarios);
    }
     public function all()
    {                      
        return( $this->model->orderBy('email', 'asc')->paginate(5));
    }
    public function edit( $id)
    {     
        //dd($id);
        $this->model = $this->model->FindOrFail( $id);
        //dd( $this->model);
        return ( $this->model );
    }
    public function delete( $id)
    {        
        $this->model->destroy( $id);
    }
    public function save(Request $request, $id)
    {                 
        $campos=[
            'fk_cve_perfil_usuario'=> 'required|string|max:1|min:1',
            'name'=> 'required|string|max:80|min:1',
            'email'=> 'required|string|max:80|min:1',
            'password'=> 'required|string|max:80|min:1',
        ];
        $mensaje=[            
            'fk_cve_perfil_usuario.required'=>'La Clave del Perfil de Usuario es requerido, al menos 1 caracter.',
            'name.required'=> 'El Nombre del Usuario es requerido.',
            'name.min'=> 'El Nombre del Usuario debe tener 1 caracteres como mínimo.',
            'name.max'=> 'El Nombre del Usuario debe tener 80 caracteres como máximo.',
            'email.required'=> 'El Correo del Usuario es requerido.',
            'email.min'=> 'El Correo del Usuario debe tener 1 caracteres como mínimo.',
            'email.max'=> 'El Correo del Usuario debe tener 80 caracteres como máximo.',
            'password.required'=> 'La Contraseña del Usuario es requerido.',
            'password.min'=> 'La Contraseña del Usuario debe tener 1 caracteres como mínimo.',
            'password.max'=> 'La Contraseña del Usuario debe tener 80 caracteres como máximo.',
        ];
        $this->validate( $request, $campos, $mensaje);        
        $datos_usuarios = $this->fix_datos_usuarios( $request);
        //dd($datos_usuarios);
        $this->model->where('id', '=', $id)->update( $datos_usuarios);
    }
    private function fix_datos_usuarios( $request) 
    {
        // elimina la variables _token , _method, y activao
        $datos_usuarios = request()->except('_token', '_method', "activao","activa");
        $datos_usuarios['activo'] = filter_var($request->activao, FILTER_VALIDATE_BOOLEAN);    
        $datos_usuarios['password'] = Hash::make($datos_usuarios['password']);
        return ($datos_usuarios);
    }
    public function insert( Request $request)
    {   
        $campos=[
            'fk_cve_perfil_usuario'=> 'required|string|max:1|min:1',
            'name'=> 'required|string|max:80|min:1',
            'email'=> 'required|string|unique:users|max:80|min:1',
            'password'=> 'required|string|max:80|min:1',
        ];
        $mensaje=[            
            'fk_cve_perfil_usuario.required'=>'La Clave del Perfil de Usuario es requerido, al menos 1 caracter.',
            'name.required'=> 'El Nombre del Usuario es requerido.',
            'name.min'=> 'El Nombre del Usuario debe tener 1 caracteres como mínimo.',
            'name.max'=> 'El Nombre del Usuario debe tener 80 caracteres como máximo.',
            'email.required'=> 'El Correo del Usuario es requerido.',
            'email.unique'=> 'El Correo del Usuario ya existe.',
            'email.min'=> 'El Correo del Usuario debe tener 1 caracteres como mínimo.',
            'email.max'=> 'El Correo del Usuario debe tener 80 caracteres como máximo.',
            'password.required'=> 'La Contraseña del Usuario es requerido.',
            'password.min'=> 'La Contraseña del Usuario debe tener 1 caracteres como mínimo.',
            'password.max'=> 'La Contraseña del Usuario debe tener 80 caracteres como máximo.',
        ];   
        //dd($request);
        $this->validate( $request, $campos, $mensaje);
        $datos_usuarios= $this->fix_datos_usuarios( $request);
        //dd($datos_usuarios);
        $this->model->insert( $datos_usuarios);
    }
}