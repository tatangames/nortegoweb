<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\NumeroMotorista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiMotoristaController extends Controller
{

    // SE DEVUELVE LISTADO DE TELEFONOS PERMITIDOS PARA INICIAR SESION EN APP
    // PARA MOTORISTAS
    public function numerosMotoristas(Request $request)
    {
        $rules = array(
            'device' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $lista = NumeroMotorista::orderBy('numero', 'ASC')->get();

        return ['success' => 1, 'lista' => $lista];
    }


    public function infoMotoristas(Request $request){

        $rules = array(
            'telefono' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($lista = NumeroMotorista::where('numero', $request->telefono)->first()){
            return ['success' => 1,
                'cambios' => $lista->cambios,
                'registrado' => $lista->registrado];
        }else{
            return ['success' => 2];
        }
    }

}
