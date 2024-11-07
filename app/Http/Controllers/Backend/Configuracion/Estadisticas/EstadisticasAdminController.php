<?php

namespace App\Http\Controllers\Backend\Configuracion\Estadisticas;

use App\Http\Controllers\Controller;
use App\Models\Informacion;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class EstadisticasAdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function indexEstadisticaAdmin(){

        $conteoUsuario = Usuario::count();
        $conteoVerificado = Usuario::where('verificado', 1)->count();
        $conteoSms = ReintentoSms::count();


        return view('backend.admin.configuracion.estadisticasadmin.vistaestadisticasadmin', compact('conteoUsuario',
            'conteoVerificado', 'conteoSms'));
    }


    public function indexInformacion(){

        $info = Informacion::where('id', 1)->first();

        $desarrollo = $info->endesarrollo;

        return view('backend.admin.configuracion.informacionapp.informacionapp', compact('desarrollo'));
    }

    public function bloqueoAplicacion(Request $request){

        $regla = array(
            'toggle' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Informacion::where('id', 1)->update([
            'endesarrollo' => $request->toggle
        ]);

        return ['success' => 1];
    }

}
