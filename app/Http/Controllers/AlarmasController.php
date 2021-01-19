<?php

namespace App\Http\Controllers;

use App\alarmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Mail;
class AlarmasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = DB::select("SELECT * FROM alarmas INNER JOIN users ON alarmas.responsable = users.id_user ORDER BY id_alarma DESC");

        return response()->json($result);
    }

    public function notificacion()
    {
        $result = DB::select("SELECT * FROM alarmas WHERE estatus = 1  ORDER BY id_alarma DESC ");

        return response()->json($result);
    }


    public function traerDatosMk(){

        $result= DB::select("SELECT * FROM servidores");

        return response()->json($result);
    }

    public function traerDatosCeldas(){

        $result= DB::select("SELECT * FROM celdas");

        return response()->json($result);
    }

    public function traerDatosAps(){

        $result= DB::select("SELECT * FROM aps");

        return response()->json($result);
    }

    public function guardarAlarma(Request $request){

        $id=$request->id;
        $equipo=$request->equipo; 
        if($request->id == 2){
            $equipo= "Celda ".$request->equipo;
        }
        if($request->id == 4){
            $equipo= "Proveedor Airtek";
        }
        $comentario = $request->comentario;
        $user=$request->id_user;
        $fecha = date("Y-m-d H:i:s");

        $result = DB::insert("INSERT INTO alarmas(equipo,responsable,comentario,estatus,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$equipo,$user,$comentario,1,$fecha,$fecha]);

        
        return response()->json($result);
    }


    public function cambiarStatusP(Request $request){

        $result = DB::select('UPDATE alarmas SET estatus = 1 WHERE id_alarma = ? ',[$request->id]);

        return response()->json($result);
    }
    public function cambiarStatusN(Request $request){

        $result = DB::select('UPDATE alarmas SET estatus = 0 WHERE id_alarma = ? ',[$request->id]);

        return response()->json($result);
    }

    public function pruebaCorreo(){
        $result = DB::select('SELECT * FROM users');

        foreach ($result as $r) {
                $nombre_usuario =$r->nombre_user;
                $correo = 'perozo64@gmail.com';
                Mail::send('emails.correoPrueba', ['usuario'=>$nombre_usuario],function ($message) use ($correo)
                {
                //$data = '%PDF-1.2 6 0 obj << /S /GoTo /D (chapter.1) >>';
                    $message->from('info@maraveca.com', 'Maraveca Telecomunicaciones');
                    $message->subject('Presupuesto');
                    $message->to($correo);
                    //$message->bcc('karen.arino@maraveca.com');
                    //$message->bcc($user->email_user);
                    //$message->attach(public_path('ppt/Brochure_Maraveca_Telecomunicaciones.pps'));
                    //$message->attachData($data, 'Presupuesto-'.$cliente.'-'.$date.'.pdf');
                    //echo public_path('ppt/Brochure_Maraveca_Telecomunicaciones.pps');
                });

        }

         return response()->json($result);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\alarmas  $alarmas
     * @return \Illuminate\Http\Response
     */
    public function show(alarmas $alarmas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\alarmas  $alarmas
     * @return \Illuminate\Http\Response
     */
    public function edit(alarmas $alarmas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\alarmas  $alarmas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, alarmas $alarmas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\alarmas  $alarmas
     * @return \Illuminate\Http\Response
     */
    public function destroy(alarmas $alarmas)
    {
        //
    }
}
