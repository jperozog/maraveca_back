<?php

namespace App\Http\Controllers;

use App\factibilidades;
use App\factibilidades_det;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FactibilidadesController extends Controller
{
  public function index()
  {
    $fac1 = $result=DB::table('factibilidades')
    ->select('kind',
    'id_cli',
    'dni',
    'email',
    'nombre',
    'apellido',
    'direccion',
    'day_of_birth',
    'serie',
    'phone1',
    'phone2',
    'comment',
    'social',
    'factibilidades.*')
    ->join('pclientes','factibilidades.id_pot','=','pclientes.id')
    ->orderBy('factibilidades.status','ASC')
    ->orderBy('factibilidades.updated_at','DSC')
    ->get();
    $fac2 = $fac1->each(function($item, $key){
      $result_det=DB::table('factibilidades_dets') //busco los detalles
      ->select(\DB::raw('nombre, valor'))
      ->where('id_fac','=',$item->id)
      ->get();
      $result = $result_det->each(function( $item_det, $key ){ //para cada detalle
        if ($item_det->nombre == "celda" && $item_det->valor != null){ //si es celda
          $result1=DB::table('celdas')
          ->select(\DB::raw('nombre_celda'))
          ->where('id_celda', '=', $item_det->valor)
          ->get();
          $item_det->det = $result1->first()->nombre_celda;
        }elseif ($item_det->nombre == "equipo" && $item_det->valor != null){
          $result1=DB::table('equipos2')
          ->select(\DB::raw('nombre_equipo'))
          ->where('id_equipo', '=', $item_det->valor)
          ->get();
          $item_det->det = $result1->first()->nombre_equipo;
        }
      });
      $item->adicionales = $result_det;
    });
    return $fac2;
  }

  public function show2($id)
  {
    $fac1 = $result=DB::table('factibilidades')
    ->select(
      'kind',
      'id_cli',
      'dni',
      'email',
      'nombre',
      'apellido',
      'direccion',
      'day_of_birth',
      'serie',
      'phone1',
      'phone2',
      'comment',
      'social',
      'factibilidades.*')
      ->join('pclientes','factibilidades.id_pot','=','pclientes.id')
      ->where('factibilidades.id', '=', $id)
      ->get();
      $fac2 = $fac1->each(function($item, $key){
        $result_det=DB::table('factibilidades_dets') //busco los detalles
        ->select(\DB::raw('nombre, valor'))
        ->where('id_fac','=',$item->id)
        ->get();
        $result = $result_det->each(function( $item_det, $item ){ //para cada detalle
          if ($item_det->nombre == "celda" && $item_det->valor != null){ //si es celda y no es nulo
            $result1=DB::table('celdas')
            ->select(\DB::raw('nombre_celda'))
            ->where('id_celda', '=', $item_det->valor)
            ->get();
            $item_det->det = $result1->first()->nombre_celda;
          }elseif ($item_det->nombre == "equipo" && $item_det->valor != null){
            $result1=DB::table('equipos2')
            ->select(\DB::raw('nombre_equipo'))
            ->where('id_equipo', '=', $item_det->valor)
            ->get();
            $item_det->det = $result1->first()->nombre_equipo;
          }elseif ($item_det->nombre == "usuario" && $item_det->valor != null){
            $result1=DB::table('users')
            ->select(\DB::raw('nombre_user, apellido_user'))
            ->where('id_user', '=', $item_det->valor)
            ->get();
            $item_det->det = $result1->first()->nombre_user.' '.$result1->first()->apellido_user;

          }elseif ($item_det->nombre == "Usuario_act_sta" && $item_det->valor != null){
            $result1=DB::table('users')
            ->select(\DB::raw('nombre_user, apellido_user'))
            ->where('id_user', '=', $item_det->valor)
            ->get();
            $item_det->det = $result1->first()->nombre_user.' '.$result1->first()->apellido_user;
          }elseif ($item_det->nombre == "ptp" && $item_det->valor != null){
            $result1=DB::table('equipos')
            ->select(\DB::raw('name'))
            ->where('id', '=', $item_det->valor)
            ->get();
            $item_det->det = $result1->first()->name;
          }
        });
        $item->adicionales = $result_det;
      });
      $equipos=DB::table('equipos')
      ->orderBy('equipos.name','ASC')
      //->orderByRaw("CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC")
      ->get();
      $celdas=DB::table('celdas')
      ->orderBy('celdas.nombre_celda','ASC')
      //->orderByRaw("CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC")
      ->get();
      $index = collect([['factibilidad'=>$fac2],['equipos'=>$equipos],['celdas'=>$celdas]]);

      return response()->json($index); //lo devuelvo via rest los soportes de determinado usuario

    }
    public function show($id)
    {
      $fac1 = $result=DB::table('factibilidades')
      ->select(
        'kind',
        'id_cli',
        'dni',
        'email',
        'nombre',
        'apellido',
        'direccion',
        'estado',
        'municipio',
        'parroquia',
        'day_of_birth',
        'serie',
        'phone1',
        'phone2',
        'comment',
        'social',
        'factibilidades.*')
        ->join('pclientes','factibilidades.id_pot','=','pclientes.id')
        ->where('factibilidades.id_pot', '=', $id)
        ->get();
        $fac2 = $fac1->each(function($item, $key){
          $result_det=DB::table('factibilidades_dets') //busco los detalles
          ->select(\DB::raw('nombre, valor'))
          ->where('id_fac','=',$item->id)
          ->get();
          $result_det->each(function( $item_det, $item ){ //para cada detalle
            if ($item_det->nombre == "celda" && $item_det->valor != null){ //si es celda
              $result1=DB::table('celdas')
              ->select(\DB::raw('nombre_celda'))
              ->where('id_celda', '=', $item_det->valor)
              ->get();
              $item_det->det = $result1->first()->nombre_celda;
            }elseif ($item_det->nombre == "equipo" && $item_det->valor != null){
              $result1=DB::table('equipos2')
              ->select(\DB::raw('nombre_equipo'))
              ->where('id_equipo', '=', $item_det->valor)
              ->get();
              $item_det->det = $result1->first()->nombre_equipo;
            }elseif ($item_det->nombre == "usuario" && $item_det->valor != null){
              $result1=DB::table('users')
              ->select(\DB::raw('nombre_user, apellido_user'))
              ->where('id_user', '=', $item_det->valor)
              ->get();
              //$item_det->det = $result1->first()->nombre_user.' '.$result1->first()->apellido_user;
            }elseif ($item_det->nombre == "ptp" && $item_det->valor != null){
              $result1=DB::table('equipos2')
              ->select(\DB::raw('nombre_equipo'))
              ->where('id_equipo', '=', $item_det->valor)
              ->get();
              $item_det->det = $result1->first()->nombre_equipo;
            }
          });
          $item->adicionales = $result_det;
        });
        return $fac2;
      }
      public function showr($id)
      {
        $factibilidades = 0;
        $fac = 0;
        $nofac = 0;
        $fac1 = $result=DB::table('factibilidades')
        ->select('factibilidades.*')
          ->where('factibilidades.id_pot', '=', $id)
          //->where('factibilidades.factible', '=', '1')
          ->get();
          foreach ($fac1 as $item){
            $factibilidades+=1;
            if ($item->factible==1){$fac+=1;}
            if ($item->factible==2){$nofac+=1;}
            $result_det=DB::table('factibilidades_dets') //busco los detalles
            ->select(\DB::raw('nombre, valor'))
            ->where('id_fac','=',$item->id)
            ->get();
            $result_det->each(function( $item_det, $item ){ //para cada detalle
              if ($item_det->nombre == "celda" && $item_det->valor != null){ //si es celda
                $result1=DB::table('celdas')
                ->select(\DB::raw('nombre_celda'))
                ->where('id_celda', '=', $item_det->valor)
                ->get();
                $item_det->det = $result1->first()->nombre_celda;
              }elseif ($item_det->nombre == "equipo" && $item_det->valor != null){
                $result1=DB::table('equipos2')
                ->select(\DB::raw('nombre_equipo'))
                ->where('id_equipo', '=', $item_det->valor)
                ->get();
                $item_det->det = $result1->first()->nombre_equipo;
              }elseif ($item_det->nombre == "usuario" && $item_det->valor != null){
                $result1=DB::table('users')
                ->select(\DB::raw('nombre_user, apellido_user'))
                ->where('id_user', '=', $item_det->valor)
                ->get();
                $item_det->det = $result1->first()->nombre_user.' '.$result1->first()->apellido_user;
              }elseif ($item_det->nombre == "ptp" && $item_det->valor != null){
                $result1=DB::table('equipos2')
                ->select(\DB::raw('nombre_equipo'))
                ->where('id_equipo', '=', $item_det->valor)
                ->get();
                $item_det->det = $result1->first()->nombre_equipo;
              }
            });
            $item->adicionales = $result_det;
          };
          return collect(['factibilidades'=>$fac1, 'fact'=>$factibilidades, 'nofac'=>$nofac, 'fac'=> $fac]);
        }

      public function store(Request $request)
      {
        $request->status = 1;
        $pre = $request->all();
        //$pre->status = 1;
        return factibilidades::create($pre);
        //return $request->all();
      }

      public function update(Request $request, $id)
      {
        $factibilidades = factibilidades::findOrFail($id);
        $factibilidades->update($request->all());
        $pre = $request->all();
        $post = [
          'nombre'=>"celda",
          'valor'=>$request->celda,
          'id_fac'=>$id
        ];
        factibilidades_det::create($post);
        $post = [
          'nombre'=>"equipo",
          'valor'=>$request->equipo,
          'id_fac'=>$id
        ];
        factibilidades_det::create($post);
        $post = [
          'nombre'=>"usuario",
          'valor'=>$request->usuario,
          'id_fac'=>$id
        ];
        factibilidades_det::create($post);
        $post = [
          'nombre'=>"altura",
          'valor'=>$request->altura,
          'id_fac'=>$id
        ];
        factibilidades_det::create($post);
        $post = [
          'nombre'=>"ptp",
          'valor'=>$request->ptp_det,
          'id_fac'=>$id
        ];
        factibilidades_det::create($post);


        return $factibilidades;
      }

      public function update1(Request $request, $id)
      {
        $factibilidades = factibilidades::findOrFail($id);
        $factibilidades->update($request->all());
        $pre = $request->all();

          factibilidades_det::where('id_fac', $id)->where('nombre', 'celda')->update(['valor' => $request->celda]);
          factibilidades_det::where('id_fac', $id)->where('nombre', 'equipo')->update(['valor' => $request->equipo]);
          factibilidades_det::where('id_fac', $id)->where('nombre', 'altura')->update(['valor' => $request->altura]);
          factibilidades_det::where('id_fac', $id)->where('nombre', 'ptp')->update(['valor' => $request->ptp_det]);

          $adicionales = new factibilidades_det;
          $adicionales->nombre = 'Usuario_act_sta';
          $adicionales->id_fac = $id;
          $adicionales->valor = $request->usuario;
          $adicionales->save();

        return $factibilidades;
      }

      public function delete(Request $request, $id)
      {
        $factibilidades = factibilidades::findOrFail($id);
        $factibilidades->delete();

        return 204;
      }


      public function traerFactibilidades(){
        $result = DB::select("SELECT f.*,c.kind,c.dni,c.nombre,c.apellido,c.social FROM factibilidades AS f INNER JOIN pclientes AS c ON f.id_pot = c.id  ORDER BY f.status ASC, f.updated_at DESC ");

        return response()->json($result);
      }

    }
