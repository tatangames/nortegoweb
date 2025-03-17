<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\ConteoIngresoCodigo;
use App\Models\Informacion;
use App\Models\ReintentoSms;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use App\Services\TwilioService;

class ApiLoginController extends Controller
{

    // CENTRAL PARA ENVIO DEL SMS A UN NUMERO DE TELEFONO
    // ES FIJO A EL SALVADOR - EXTENSION +503
    private function sendSms($to, $codigo)
    {
        $message = "Tu código para NorteGo es: " . $codigo;
        $ext = "+503" . $to;

        try {
            $twilio = new TwilioService();
            $result = $twilio->sendMessage($ext, $message);

            if ($result) {
                return ['success' => true, 'message' => 'Mensaje enviado correctamente'];
            } else {
                return ['success' => false, 'error' => 'No se pudo enviar el mensaje'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'No se pudo enviar el mensaje: ' . $e->getMessage()];
        }
    }


    public function verificacionTelefono(Request $request)
    {
        $rules = array(
            'telefono' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // TIEMPO QUE DEBE ESPERAR EL USUARIO PARA REENVIAR CODIGO SMS
            $limiteSegundosSMS = 60;

            // QUITAR ESPACIOS QUE VIENEN DEL NUMERO
            $telefono = str_replace(' ', '', $request->telefono);

            // GENERAR CODIGO DE 6 DIGITOS
            $codigo = '123456';
             /*for($i = 0; $i < 6; $i++) {
                 $codigo .= mt_rand(0, 9);
             }*/



            // -------------- BLOQUEO TEMPORAL ------------

            $infoExtra = Informacion::where('id', 1)->first();
            if($infoExtra->endesarrollo == 1){
                return ['success' => 100];
            }


            // -------------- END - BLOQUEO TEMPORAL ---------------


            if($infoUsuario = Usuario::where('telefono', $telefono)->first()) {


                // USUARIO BLOQUEADO
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // FECHA DEL SERVIDOR
                $currentDate = Carbon::now('America/El_Salvador');

                // DIFERENCIA EN SEGUNDOS ENTRE LA FECHA ACTUAL DEL SERVIDOR Y LA FECHA DEL ULTIMO INTENTO SMS
                $secondsSinceLastAttempt = $currentDate->diffInSeconds($infoUsuario->fechareintento);

                // VERIFICAR SI HAN PASADO AL MENOS X SEGUNDOS
                $puedeReenviarSMS = 0;
                $secondsToWait = 0;

                if($secondsSinceLastAttempt >= $limiteSegundosSMS){
                    $puedeReenviarSMS = 1;
                }else{
                    // CALCULAR EL TIEMPO RESTANTE (CRONOMETRO), SI AUN NO SE PUEDE REENVIAR SMS
                    $secondsToWait = $limiteSegundosSMS - $secondsSinceLastAttempt;
                }

                // CERO, SE SETEA AL TIEMPO X DE ESPERA DE SEGUNDOS PARA EL CRONOMETRO EN LA APP
                if($secondsToWait <= 0){
                    $secondsToWait = $limiteSegundosSMS;
                }

                // YA SE PUEDE REENVIAR SMS Y SE HACE EL REENVIO, SE ACTUALIZA LA FECHA
                if ($puedeReenviarSMS == 1) {



                    //******* AQUI SE ENVIA SMS ***********

                    // Si falla el envio, se hace un return de error


                    // Llamar a la función sendSms
                    /*$resultadoSMS = $this->sendSms($telefono, $codigo);

                    if (!$resultadoSMS['success']) {
                        Log::info("ERROR SMS: " . $resultadoSMS['error']);
                        return ['success' => 2];
                    }*/


                    //******* AQUI SE FINALIZA ENVIO SMS ***********



                    $detaRe = new ReintentoSms();
                    $detaRe->id_usuarios = $infoUsuario->id;
                    $detaRe->fecha = $currentDate;
                    $detaRe->tipo = 1;
                    $detaRe->save();

                    // ACTUALIZAR LA FECHA DE REINTENTO SMS DEL USUARIO
                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'codigo' => $codigo,
                            'fechareintento' => $currentDate
                        ]);
                }

                DB::commit();
                return ['success' => 3, 'canretry' => $puedeReenviarSMS, 'segundos' => $secondsToWait];
            } else {

                // CUANDO EL TELEFONO A REGISTRAR ES NUEVO, SI ES UN NUMERO ERRONEO, NO GUARDARA NADA
                // EN EXCEPCION DE ENVIO SMS

                $currentDate = Carbon::now('America/El_Salvador');

                $registro = new Usuario();
                $registro->telefono = $telefono;
                $registro->codigo = $codigo;
                $registro->fecha = $currentDate;
                $registro->fechareintento = $currentDate;
                $registro->onesignal = null;
                $registro->activo = 1;
                $registro->verificado = 0;
                $registro->fecha_verificado = null;
                $registro->save();


                //******* AQUI SE ENVIA SMS ***********


                /* $resultadoSMS = $this->sendSms($telefono, $codigo);

                if (!$resultadoSMS['success']) {
                    Log::info("ERROR SMS: " . $resultadoSMS['error']);
                    return ['success' => 2];
                }*/



                //******* AQUI SE FINALIZA ENVIO SMS ***********

                $detaRe = new ReintentoSms();
                $detaRe->id_usuarios = $registro->id;
                $detaRe->fecha = $currentDate;
                $detaRe->tipo = 2;
                $detaRe->save();

                //************************************

                DB::commit();
                return ['success' => 3, 'canretry' => 1, 'segundos' => $limiteSegundosSMS];

            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // SOLICITUD DE CODIGO DE CONFIRMACION
    public function reintentoSMS(Request $request){

        $rules = array(
            'telefono' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // EN ANDROID O IPHONE PUEDE VENIR CON ESPACIOS, AQUI SE QUITARAN
            $telefono = str_replace(' ', '', $request->telefono);

            if($infoUsuario = Usuario::where('telefono', $telefono)->first()){

                // Usuario inactivo
                if($infoUsuario->activo == 0){
                    return ['success' => 1];
                }

                // FECHA DEL SERVIDOR
                $fechaServidor = Carbon::now('America/El_Salvador');

                //******* AQUI SE ENVIA SMS ***********
                // EL CODIGO NO SE ACTUALIZA EN ESTA PARTE, SOLO ES AL VERIFICAR NUMERO QUE GENERA CODIGO

                 /*$resultadoSMS = $this->sendSms($telefono, $infoUsuario->codigo);

                  if (!$resultadoSMS['success']) {
                      Log::info("ERROR SMS: " . $resultadoSMS['error']);
                      return ['success' => 2];
                  }*/

                //*************************************


                // BITACORA DE REGISTROS, CUANTOS INTENTOS A REALIZADO

                $detaRe = new ReintentoSms();
                $detaRe->id_usuarios = $infoUsuario->id;
                $detaRe->fecha = $fechaServidor;
                $detaRe->tipo = 3;
                $detaRe->save();

                // Ultima fecha para que pueda reintentar usuario
                Usuario::where('id', $infoUsuario->id)
                    ->update([
                        'fechareintento' => $fechaServidor
                    ]);

                // AL REINICIAR POR DEFECTO HAY 60 SEGUNDOS EN LA APP

                DB::commit();
                return ['success' => 3];

            }else{
                // telefono no encontrado
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function verificarCodigo(Request $request){

        $rules = array(
            'telefono' => 'required',
            'codigo' => 'required'
        );

        // idonesignal

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // EN ANDROID O IPHONE EL TELEFONO O CODIGO PUEDE VENIR CON ESPACIOS, SE DEBEN QUITAR

            $telefono = str_replace(' ', '', $request->telefono);
            $codigo = str_replace(' ', '', $request->codigo);
            $fechaActual = Carbon::now('America/El_Salvador');

            if($infoUsuario = Usuario::where('telefono', $telefono)
                ->where('codigo', $codigo)
                ->first()){

                // SE LLEVA REGISTRO CUANDO EL NUMERO FUE VERIFICADO
                if($infoUsuario->verificado == 0){
                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'verificado' => 1,
                            'fecha_verificado' => $fechaActual
                        ]);
                }

                // CREAR TOKEN DE ACCESO
                $token = JWTAuth::fromUser($infoUsuario);

                // actualizar id notificacion
                $idOneSignal = $request->idonesignal;

                // ACTUALIZAR ID ONE SIGNAL, EVITAR VACIOS
                if(!empty($idOneSignal)){
                    // Actualizar
                    Usuario::where('id', $infoUsuario->id)
                        ->update([
                            'onesignal' => $idOneSignal,
                        ]);
                }

                DB::commit();
                return ['success' => 1, 'token' => $token, 'id' => strval($infoUsuario->id)];
            }else{
                // codigo incorrecto

                // LLEVAR UN REGISTRO CUANTAS VECES SE HA EQUIVOCADO
                if($infoUsuario = Usuario::where('telefono', $telefono)->first()) {
                    $registro = new ConteoIngresoCodigo();
                    $registro->id_usuarios = $infoUsuario->id;
                    $registro->fecha = $fechaActual;
                    $registro->save();
                    DB::commit();
                }

                return ['success' => 2];
            }
        }catch(\Throwable $e){
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }
}
