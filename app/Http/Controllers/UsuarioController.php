<?php

namespace App\Http\Controllers;

use App\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RouterosAPI;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = DB::select("SELECT * FROM users");

        return response()->json($usuarios);
    }

    public function traerHistorialUsuario($id){
            $historico = DB::select("SELECT * FROM historicos WHERE responsable = ? ORDER BY id DESC",[$id]);

            $historicoCliente = DB::select("SELECT * FROM historico_clientes AS h INNER JOIN clientes AS c ON h.cliente = c.id WHERE responsable = ? ORDER BY h.id DESC",[$id]);

            $historial=collect(['historico'=>$historico, 'historicoCliente'=>$historicoCliente]);

            return response()->json($historial);
    }

    public function actualizarPermisosMk(Request $request){
        $zonas = $request->zonas;
        $id_user = $request->id_user;
        $permiso = $request->permiso;

        
        foreach ($zonas as $zona) {
                $mk = DB::select("SELECT * FROM servidores WHERE id_srvidor = ?",[$zona])["0"];
                $usuario = DB::select("SELECT * FROM users WHERE id_user = ?",[$id_user])["0"];

                permisosMK($mk->ip_srvidor,$mk->user_srvidor,$mk->password_srvidor,$usuario->username,$usuario->password,$permiso);
                
        }
            $insertarPermiso = DB::insert("INSERT INTO user_mk(user,permiso) VALUES (?,?)",[$id_user,$permiso]);
       
        return response()->json($request);
    }

    public function traerPermisoMK($id){
        $permiso = DB::select("SELECT * FROM user_mk WHERE user = ? ORDER BY id_user_mk DESC LIMIT 1",[$id]);

        return response()->json($permiso);
    }

}
