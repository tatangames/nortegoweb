<?php

namespace App\Http\Controllers\Backend\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{

    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function indexRedireccionamiento(){

        $user = Auth::user();

        // ADMINISTRADOR
        if($user->hasRole('admin')){
            $ruta = 'admin.roles.index';
        }
        else if($user->hasRole('admin-redvial')){
            $ruta = 'admin.solicitud.redvial.activa.index';
        }
        else if($user->hasRole('admin-electrico')){
            $ruta = 'admin.solicitud.alumbrado.activa.index';
        }
        else if($user->hasRole('admin-desechos')){
            $ruta = 'admin.solicitud.desechos.activa.index';
        }
        else if($user->hasRole('admin-tala')){
            $ruta = 'admin.solicitud.tala.arbol';
        }
        else if($user->hasRole('admin-catastro')){
            $ruta = 'admin.catastro.activas';
        }
        else{
            $ruta = 'no.permisos.index';
        }

        return view('backend.index', compact( 'ruta', 'user'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }


}
