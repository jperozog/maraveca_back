<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\User_Privilegios;
use App\User_zona;
use App\Permissions;

class UsersController extends Controller
{
  public function index()
  {
      return user::where('id_user', '!=', '0')->get();
  }

  public function indexi()
  {
      return user::where('installer', '=', '1')->get();
  }

  public function login(Request $request, $username, $password)
  {
    $result=DB::table('users')
    ->where('username','=',$username)
    ->where('password','=',$password)
    ->get();
    return response()->json($result);
  }

  public function show(Request $request, $id)
  {
    //return $request;
    $result=DB::table('users')
    ->where('id_user','=',$id)
    ->get();
    return response()->json($result);
  }

  public function update_permissions(Request $request)
  {
    $user = permissions::where('user','=', $request->usuario);
    $user->delete();
    foreach ($request->permisos as $key) {
      $post=collect(['user'=>$request->usuario, 'perm'=>$key]);
     $a= permissions::create($post->all());
    }
return $a;
  }

  public function dash (Request $request)
  {
    $uid = $request->uid;
    $permissions=DB::table('permissions')
    ->where('user','=',$uid)
    ->get();
    $dash=collect();
    foreach ($permissions as $perm) {
      if($perm->perm == 'factibilidades'){
        $fact=DB::table('factibilidades')
        ->where('status','1')
        ->get();
        $dash->put('factibilidades', count($fact));
      }elseif ($perm->perm == 'facturacion') {
          $resp = DB::table('balance_clientes')
              ->where('bal_stat', '2')
              ->get();
          $dash->put('balance', count($resp));

          $resp=DB::table('balance_clientes_ins')
              ->where('bal_stat_in','2')
              ->get();
          $dash->put('balance_in', count($resp));
      }elseif ($perm->perm == 'soporte'){
        $resp=DB::table('soportes')
        ->where('status_soporte','1')
        ->where('tipo_soporte','1')
        ->get();
        $dash->put('instalaciones', count($resp));
        $resp=DB::table('soportes')
        ->where('status_soporte','1')
        ->where('tipo_soporte','2')
        ->get();
        $dash->put('tickets', count($resp));
        $resp=DB::table('soportes')
        ->where('status_soporte','1')
        ->where('tipo_soporte','3')
        ->get();
        $dash->put('averias', count($resp));
        $resp=DB::table('soportes')
            ->where('status_soporte','1')
            ->where('tipo_soporte','4')
            ->get();
          $dash->put('otrost', count($resp));
      }
    }
    $t=0;
    foreach ($dash as $d => $val) {
      $t+=$val;
    }
    $dash->put('total', $t);
    return $dash;
  }

  public function p_zona(Request $request)
  {
    $users = User_zona::where('user','=',$request->usuario);
    $users->delete();
    foreach ($request->zonas as $zona) {
      $post = [
        'user'=>$request->usuario,
        'zona'=>$zona
      ];
      User_zona::create($post);
    }
  }

  public function g_permissions(Request $request, $user)
  {
    $result=DB::table('permissions')
    ->where('user','=',$user)
    ->get();
    return response()->json($result);
  }
  public function d_permissions(Request $request, $user)
  {
    $user = permissions::where('user','=',$user);
    $user->delete();

    return 204;
  }
  public function p_permissions(Request $request)
  {
    return permissions::create($request->all());
  }
  public function g_zona(Request $request, $id)
  {
    $result=DB::table('user_zonas')
    ->where('user','=',$id)
    ->join('servidores','servidores.id_srvidor','=','user_zonas.zona')
    ->get();
    return response()->json($result);
  }

  public function store(Request $request)
  {
      return user::create($request->all());
  }

  public function update(Request $request, $id)
  {
      $responsable = $request->responsable;
      unset($request['responsable']);
      unset($request['confirm']);
      //return $request;
      $request=$request->all();
      $user = user::where('id_user','=',$id);
      $user->update($request);

      //return $user;
  }

  public function delete(Request $request, $id)
  {
      $user = user::where('id_user','=',$id);
      $user->delete();

      return 204;
  }
}
