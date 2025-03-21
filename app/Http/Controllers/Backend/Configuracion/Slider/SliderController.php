<?php

namespace App\Http\Controllers\Backend\Configuracion\Slider;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\NumeroMotorista;
use App\Models\Servicios;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin');
    }

    public function indexSlider(){
        return view('backend.admin.configuracion.slider.vistaslider');
    }


    public function tablaSlider(){
        $listado = Slider::orderBy('posicion', 'ASC')->get();

        return view('backend.admin.configuracion.slider.tablaslider',compact('listado'));
    }


    public function nuevoSlider(Request $request){

        // imagen, nombre

        if ($request->hasFile('imagen')) {

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre . strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

            if ($upload) {

                if ($info = Slider::orderBy('posicion', 'DESC')->first()) {
                    $nuevaPosicion = $info->posicion + 1;
                } else {
                    $nuevaPosicion = 1;
                }

                $registro = new Slider();
                $registro->nombre = $request->nombre;
                $registro->activo = 1;
                $registro->posicion = $nuevaPosicion;
                $registro->imagen = $nombreFoto;
                $registro->save();

                return ['success' => 1];
            }
            else {
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }

    public function informacionSlider(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Slider::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarPosicionSlider(Request $request){

        $tasks = Slider::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    public function editarSlider(Request $request){

        // id, nombre, imagen, toggle

        $rules = array(
            'id' => 'required',
            'toggle' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        if ($request->hasFile('imagen')) {

            $infoDato = Slider::where('id', $request->id)->first();

            $imagenOld = $infoDato->imagen;

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre . strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

            if ($upload) {

                Slider::where('id', $request->id)
                    ->update([
                        'nombre' => $request->nombre,
                        'activo' => $request->toggle,
                        'imagen' => $nombreFoto,
                    ]);

                if(Storage::disk('archivos')->exists($imagenOld)){
                    Storage::disk('archivos')->delete($imagenOld);
                }

                return ['success' => 1];
            } else {
                // error al subir imagen
                return ['success' => 99];
            }
        } else {
            Slider::where('id', $request->id)
                ->update([
                    'nombre' => $request->nombre,
                    'activo' => $request->toggle,
                ]);

            return ['success' => 1];
        }
    }




    // eliminar un slider
    public function borrarSlider(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        if($infoSlider = Slider::where('id', $request->id)->first()){

            // MINIMO HABRA 1 SLIDER
            $conteo = Slider::count();
            if($conteo == 1){
                return ['success' => 1];
            }


            if($infoSlider->imagen != null){
                if(Storage::disk('archivos')->exists($infoSlider->imagen)){
                    Storage::disk('archivos')->delete($infoSlider->imagen);
                }
            }

            Slider::where('id', $request->id)->delete();

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }





    //*************************************************************************************************


    public function indexNumMotorista()
    {
        return view('backend.admin.configuracion.motoristas.numeromotorista.vistanumeromotorista');
    }


    public function tablaNumMotorista()
    {
        $listado = NumeroMotorista::orderBy('numero', 'ASC')->get();

        return view('backend.admin.configuracion.motoristas.numeromotorista.tablanumeromotorista', compact('listado'));
    }

    public function nuevoNumMotorista(Request $request)
    {
        $rules = array(
            'numero' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        // numero repetido
        if(NumeroMotorista::where('numero', $request->numero)->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {
            $registro = new NumeroMotorista();
            $registro->numero = $request->numero;
            $registro->cambios = 0;
            $registro->fecha_registro = null;
            $registro->registrado = 0;
            $registro->save();

            DB::commit();
            return ['success' => 2];
        } catch (\Throwable $e) {
            Log::info("error" . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function informacionNumMotorista(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = NumeroMotorista::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarNumMotorista(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'numero' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        NumeroMotorista::where('id', $request->id)
            ->update([
                'numero' => $request->numero,
                'fecha_registro' => $request->fecha,
                'cambios' => $request->togglecambios,
                'registrado' => $request->toggleregistrado,
            ]);

        return ['success' => 1];
    }


    public function borrarNumMotorista(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        NumeroMotorista::where('id', $request->id)->delete();

        return ['success' => 1];
    }





    //******************* AGENDA *******************************************************

    public function indexAgenda(){

        return view('backend.admin.agenda.vistaagenda');
    }


    public function tablaAgenda(){

        $listado = Agenda::orderBy('posicion', 'ASC')->get();

        return view('backend.admin.agenda.tablaagenda',compact('listado'));
    }


    public function nuevoAgenda(Request $request){

        $regla = array(
            'nombre' => 'required',
            'telefono' => 'required',
        );

        // imagen

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if ($request->hasFile('imagen')) {

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre . strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('archivos')->put($nombreFoto, \File::get($avatar));

            if ($upload) {

                if ($info = Agenda::orderBy('posicion', 'DESC')->first()) {
                    $nuevaPosicion = $info->posicion + 1;
                } else {
                    $nuevaPosicion = 1;
                }

                DB::beginTransaction();

                try {
                    $registro = new Agenda();
                    $registro->nombre = $request->nombre;
                    $registro->telefono = $request->telefono;
                    $registro->posicion = $nuevaPosicion;
                    $registro->imagen = $nombreFoto;
                    $registro->save();

                    DB::commit();
                    return ['success' => 1];
                }catch(\Throwable $e){
                    Log::info("error" . $e);
                    DB::rollback();
                    return ['success' => 99];
                }
            }
            else {
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }

    public function borrarAgenda(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Agenda::where('id', $request->id)->first()){

            Agenda::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarPosicionAgenda(Request $request){

        $tasks = Agenda::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }









}
