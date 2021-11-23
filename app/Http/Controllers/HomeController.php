<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        //dd("aqui");
        if (Auth::user()->fk_cve_perfil_usuario == "A") 
        {  
           //dd("Home Admin");
           return view('homeadmin');
        }
        else
        {
            //dd(Auth::user());
            return view('home');
        }      
    }
}