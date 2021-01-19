<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\ticket_history;
use App\historico;

class TicketHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $result=DB::table('ticket_histories')
        ->select('ticket_histories.*', 'users.nombre_user', 'users.apellido_user')
        ->join('users', 'ticket_histories.user_th', '=', 'users.id_user')
        ->orderBy('ticket_histories.created_at', 'DESC')
        ->where('ticket_th', '=', $id)
        ->get();

        return response()->json($result);
    }

    public function store(Request $request)
    {

        historico::create(['responsable'=>$request->user_th, 'modulo'=>'Soporte', 'detalle'=>'Actualizo el ticket '.$request->ticket_th]);

        return ticket_history::create($request->all());
    }
}
