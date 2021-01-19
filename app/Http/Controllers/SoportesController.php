<?php

namespace App\Http\Controllers;
use App\cola_de_ejecucion;
use App\pendiente_servi;
use App\servicios;
use App\servidores;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\soportes;
use App\tipos_soporte;
use App\ticket_history;
use App\ticket_problems;
use App\instinst;
use App\oinstall;
use App\instpag;
use App\historico_cliente;
use App\historico;
use App\User;
use App\clientes;
use App\celdas;
use App\planes;
use App\inventarios;
use App\lista_ip;

class SoportesController extends Controller
{
    public function indext()
    {
        //return clientes::all()->orderBy('clientes.email','ASC');
        $result = DB::table('soportes')
            ->select('soportes.*', 'servicios.cliente_srv', 'clientes.nombre', 'clientes.apellido', 'clientes.social', 'clientes.phone1', 'clientes.phone2', 'clientes.direccion', 'users.nombre_user', 'users.apellido_user', 'servidores.nombre_srvidor')
            ->join('servicios', 'servicios.id_srv', '=', 'soportes.servicio_soporte')
            ->join('aps', 'servicios.ap_srv', '=', 'aps.id')
            ->join('celdas', 'aps.celda_ap', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'servicios.cliente_srv', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->get();

        //return $result;
        return response()->json($result);
    }

    public function indexi(Request $rq)
    {
        $zonas = []; //inicio el arrays vacios
        $pi = 0;
        $pt = 0;
        $pa = 0;
        $po = 0;
        $result = DB::table('user_zonas')->select('zona')->where('user', '=', $rq->user)->get(); //pregunto las zonas asignadas a determinado usuario
        foreach ($result as $z) {
            array_push($zonas, $z->zona); //las meto en la variable $zonas
        }
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, clientes.kind, clientes.nombre, clientes.apellido,clientes.social, clientes.phone1,clientes.phone2,clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, servidores.id_srvidor, celdas.nombre_celda'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })

            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.tipo_soporte', '1')// solo instalaciones
            ->where('soportes.status_soporte', '>=', '1')// solo instalaciones
            ->where(function ($query) use ($zonas) {
                foreach ($zonas as $zona) { //por cada zona del usuario coloco un orWhere para solo devolver los que tenga asignado el usuario
                    $query->orWhere('servidores.id_srvidor', '=', $zona);
                }
            });
        $inst = $result->get(); //envio el query y almaceno el retorno en $sportes
        foreach ($inst as $items) { // para cada instalacion

            $result = DB::table('tipos_soportes')//busco los detalles
            ->select(\DB::raw('tipos_soportes.nombre, tipos_soportes.value'))
                ->where('fac_id', '=', $items->id_soporte)
                ->get();
            if ($items->status_soporte == 1) {
                $pi += 1;
            }
            $router = 0;
            //$adicionales;
            foreach ($result as $item) {//para cada detalle
                global $router;
                if ($item->nombre == "celda") { //si es celda

                    $items->celda = 1;
                    $items->celdas = $item->value;
                } elseif ($item->nombre == "ModeloEquipo") {
                    $items->tipo_equipo = $item;;
                    $items->equipo = 1;
                    $items->equipos = $item->value;;


                }
                elseif ($item->nombre == "ipP") {
                    $items->ips = 1;
                    $items->ip = $item->value;
                } elseif ($item->nombre == "ap") {
                    $result1 = DB::select("SELECT * FROM aps AS a where a.id = ?",[$item->value]);
                    foreach ($result1 as $r) {
                        $item->value = $r->nombre_ap;
                    }    
                    $items->ap = 1;
                    $items->aps = $item;
                } elseif ($item->nombre == 'Tubo') {
                    $items->Tub0 = 1;
                    $items->Tubo = $item->value;
                } elseif ($item->nombre == 'Serial') {
                    $router += 1;
                    $items->ser1al = "1";
                    $items->serial = $item->value;
                    //unset($result[$item]);
                } elseif ($item->nombre == 'SerialEquipo') {
                    $router += 1;
                    $items->ser1al = "1";
                    $items->serial = $item->value;
                    //unset($result[$item]);

                } elseif ($item->nombre == 'plan') {
                    $items->p1an = 1;
                    $items->plan = $item->value;
                }elseif ($item->nombre == 'tipo_plan') {
                    $items->t1po_plan = 1;
                    $items->tipo_plan = $item->value;
                }

            };
            if ($router == 0) {
                $items->ser1al = "0";
                $items->serial = "Equipo Usado";
            };
            if ($item->nombre == "ModeloEquipo"){
                $result1 = DB::table('equipos2')
                    ->select(\DB::raw('tipo_equipo'))
                    ->where('id_equipo', '=', $item->value)
                    ->get();
                ///$item->value1 = $result1->first()->tipo_equipo;;
                $items->t1po_equ1po = 1;

            };
            //$items->adicionales = $result;
        };

        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servicios.cliente_srv, clientes.kind, clientes.nombre, clientes.apellido, clientes.social, clientes.phone1, clientes.phone2, clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda'))
            ->join('servicios', 'servicios.id_srv', '=', 'soportes.servicio_soporte')
            ->join('aps', 'servicios.ap_srv', '=', 'aps.id')
            ->join('celdas', 'aps.celda_ap', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'servicios.cliente_srv', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.tipo_soporte', '2')/* solo tickets*/
            ->where(function ($query) use ($zonas) {
                foreach ($zonas as $zona) { //por cada zona del usuario coloco un orWhere para solo devolver los que tenga asignado el usuario
                    $query->orWhere('servidores.id_srvidor', '=', $zona);
                }
            });
        $soportes = $result->get(); //envio el query y almaceno el retorno en $sportes
        foreach ($soportes as $item) {
            if ($item->status_soporte == 1) {
                $pt += 1;
            }
        }
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda'))
            ->join('celdas', 'soportes.servicio_soporte', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.tipo_soporte', '3')/* solo averias*/
            ->where(function ($query) use ($zonas) {
                foreach ($zonas as $zona) { //por cada zona del usuario coloco un orWhere para solo devolver los que tenga asignado el usuario
                    $query->orWhere('servidores.id_srvidor', '=', $zona);
                }
            });
        $averias = $result->get();
        foreach ($averias as $item) {
            if ($item->status_soporte == 1) {
                $pa += 1;
            }
        }
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda'))
            ->join('celdas', 'soportes.servicio_soporte', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.tipo_soporte', '4')/* otros trabajos e instalaciones*/
            ->where(function ($query) use ($zonas) {
                foreach ($zonas as $zona) { //por cada zona del usuario coloco un orWhere para solo devolver los que tenga asignado el usuario
                    $query->orWhere('servidores.id_srvidor', '=', $zona);
                }
            });
        $otrost = $result->get();
        foreach ($otrost as $item) {
            if ($item->status_soporte == 1) {
                $po += 1;
            }
        }

        $index = collect(['instalaciones' => $inst, 'soporte' => $soportes, 'averias' => $averias, 'otrost' => $otrost, 'pendingi' => $pi, 'pendingt' => $pt, 'pendinga' => $pa, 'pendingo' => $po]);

        return response()->json($index); //lo devuelvo via rest los soportes de determinado usuario

    }

    public function indexinstalls()
    {
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, clientes.kind, clientes.nombre, clientes.apellido,clientes.social, clientes.phone1,clientes.phone2,clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.tipo_soporte', '1')// solo instalaciones
        ;
        $inst = $result->get(); //envio el query y almaceno el retorno en $sportes
        foreach ($inst as $items) { // para cada instalacion

            $result = DB::table('tipos_soportes')//busco los detalles
            ->select(\DB::raw('tipos_soportes.nombre, tipos_soportes.value'))
                ->where('fac_id', '=', $items->id_soporte)
                ->get();
            $router = 0;
            //$adicionales;
            foreach ($result as $item) {//para cada detalle
                global $router;
                if ($item->nombre == "celda") { //si es celda

                    $items->ce1da = 1;
                    $items->celda = $item->value;
                } elseif ($item->nombre == "equipo") {
                    $result1 = DB::table('equipos')
                        ->select(\DB::raw('name'))
                        ->where('id', '=', $item->value)
                        ->get();
                    $item->value = $result1->first()->name;;
                    $items->equipo = 1;
                    $items->equipos = $item;
                } elseif ($item->nombre == "ip") {
                    $items->ip = 1;
                    $items->ips = $item->value;
                } elseif ($item->nombre == "ap") {
                    $result1 = DB::table('aps')
                        ->select(\DB::raw('nombre_ap'))
                        ->where('id', '=', $item->value)
                        ->get();
                    $item->value = $result1->first()->nombre_ap;;
                    $items->ap = 1;
                    $items->aps = $item;
                } elseif ($item->nombre == 'Serial') {
                    $router += 1;
                    $items->ser1al = "1";
                    $items->serial = $item->value;
                    //unset($result[$item]);
                } elseif ($item->nombre == 'SerialEquipo') {
                    $router += 1;
                    $items->ser1al = "1";
                    $items->serial = $item->value;
                    //unset($result[$item]);
                }
            };
            if ($router == 0) {
                $items->ser1al = "0";
                $items->serial = "Equipo Usado";
            };
            //$items->adicionales = $result;
        };
        return $inst;
    }

    public function show1($id)
    {
        $result = DB::table('soportes')
            ->select('soportes.*', 'celdas.nombre_celda', 'servicios.ip_srv', 'planes.name_plan', 'aps.nombre_ap', 'servicios.cliente_srv', 'clientes.nombre', 'clientes.apellido', 'clientes.social', 'clientes.phone1', 'clientes.phone2', 'clientes.direccion', 'clientes.email', 'users.nombre_user', 'users.apellido_user', 'servidores.nombre_srvidor')
            ->join('servicios', 'servicios.id_srv', '=', 'soportes.servicio_soporte')
            ->join('aps', 'servicios.ap_srv', '=', 'aps.id')
            ->join('celdas', 'aps.celda_ap', '=', 'celdas.id_celda')
            ->join('planes', 'planes.id_plan', '=', 'plan_srv')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'servicios.cliente_srv', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->where('id_soporte', '=', $id)
            ->get();

        //return $result;
        return response()->json($result);
    }


    public function show($id)
    {
        $result = DB::table('soportes')
            ->select('soportes.tipo_soporte')
            ->where('id_soporte', '=', $id)
            ->get();
        if ($result[0]->tipo_soporte == "2") {
            $result = DB::table('soportes')
                ->select('soportes.*', 'celdas.nombre_celda', 'servicios.ip_srv', 'planes.name_plan', 'aps.nombre_ap', 'servicios.cliente_srv', 'clientes.kind', 'clientes.nombre', 'clientes.apellido', 'clientes.social', 'clientes.phone1', 'clientes.phone2', 'clientes.direccion', 'clientes.email', 'users.nombre_user', 'users.apellido_user', 'servidores.nombre_srvidor')
                ->join('servicios', 'servicios.id_srv', '=', 'soportes.servicio_soporte')
                ->join('aps', 'servicios.ap_srv', '=', 'aps.id')
                ->join('celdas', 'aps.celda_ap', '=', 'celdas.id_celda')
                ->join('planes', 'planes.id_plan', '=', 'plan_srv')
                ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
                ->join('clientes', 'servicios.cliente_srv', '=', 'clientes.id')
                ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
                ->where('id_soporte', '=', $id)
                ->get();
        } elseif ($result[0]->tipo_soporte == "3") {
            $result = DB::table('soportes')//inicio el query con sus join y orders
            ->select(\DB::raw('soportes.*, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda'))
                ->join('celdas', 'soportes.servicio_soporte', '=', 'celdas.id_celda')
                ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
                ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
                ->orderBy('soportes.status_soporte', 'ASC')
                ->orderBy('soportes.updated_at', 'DSC')
                ->where('id_soporte', '=', $id)
                ->get(); //envio el query y almaceno el retorno en $sportes
        } elseif ($result[0]->tipo_soporte == "4") {
            $result = DB::table('soportes')//inicio el query con sus join y orders
            ->select(\DB::raw('soportes.*, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda'))
                ->join('celdas', 'soportes.servicio_soporte', '=', 'celdas.id_celda')
                ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
                ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
                ->orderBy('soportes.status_soporte', 'ASC')
                ->orderBy('soportes.updated_at', 'DSC')
                ->where('id_soporte', '=', $id)
                ->get(); //envio el query y almaceno el retorno en $sportes
        } elseif ($result[0]->tipo_soporte == "1") {
            $result = DB::table('soportes')//inicio el query con sus join y orders
            ->select(\DB::raw('soportes.*, clientes.nombre, clientes.apellido,clientes.social, clientes.kind, clientes.phone1,clientes.phone2,clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor'))
                ->join('tipos_soportes as celdas_ins', function ($join) {
                    $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                        ->where('celdas_ins.nombre', '=', 'celda');
                })
                ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
                ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
                ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
                ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
                ->orderBy('soportes.status_soporte', 'ASC')
                ->orderBy('soportes.updated_at', 'DSC')
                ->where('id_soporte', '=', $id)
                ->get(); //envio el query y almaceno el retorno en $sportes
            foreach ($result as $items) { // para cada instalacion

                $result2 = DB::table('tipos_soportes')//busco los detalles
                ->select(\DB::raw('tipos_soportes.nombre, tipos_soportes.value'))
                    ->where('fac_id', '=', $items->id_soporte)
                    ->get();
                $router = 0;
                //$adicionales;
                $adicionales = [];
                foreach ($result2 as $item) {//para cada detalle
                    global $router;
                    if ($item->nombre == "celda") { //si es celda
                        $result1 = DB::table('celdas')
                            ->select(\DB::raw('nombre_celda'))
                            ->where('id_celda', '=', $item->value)
                            ->get();
                        $items->celda = $item->value;
                        $item->value = $result1->first()->nombre_celda;
                        $items->celdas = $item;
                        array_push($adicionales, $item);
                    } elseif ($item->nombre == "equipo") {
                        $result1 = DB::table('equipos')
                            ->select(\DB::raw('name'))
                            ->where('id', '=', $item->value)
                            ->get();
                        $item->value = $result1->first()->name;;
                        $items->equipo = 1;
                        $items->equipos = $item;
                        array_push($adicionales, $item);
                        //unset($item);
                    } elseif ($item->nombre == "ip") {
                        $router += 1;
                        $items->lp = "1";
                        $items->ip = $item->value;
                    } elseif ($item->nombre == "plan") {
                        $router += 1;
                        $items->p1an = "1";
                        $items->plan = $item->value;
                    }elseif ($item->nombre == "ipP") {
                        $router += 1;
                        $items->lpP = "1";
                        $items->ipP = $item->value;
                    }
                    elseif ($item->nombre == "ap") {
                        $result1 = DB::table('aps')
                            ->select(\DB::raw('nombre_ap'))
                            ->where('id', '=', $item->value)
                            ->get();
                        $item->value = $result1->first()->nombre_ap;;
                        $items->ap = 1;
                        $items->aps = $item;
                        array_push($adicionales, $item);
                    } elseif ($item->nombre == 'Serial') {
                        $router += 1;
                        $items->ser1al = "1";
                        $items->serial = $item->value;
                        //unset($result2[$item]);
                    } elseif ($item->nombre == 'Router') {
                        $items->router = $item->value;
                        array_push($adicionales, $item);
                        //unset($result[$item]);
                    } elseif ($item->nombre == 'SerialEquipo') {
                        $router += 1;
                        $items->ser1al = "1";
                        $items->serial = $item->value;
                        //unset($result[$item]);
                    }
                };
                if ($router == 0) {
                    $items->ser1al = "0";
                    $items->serial = "Equipo Usado";
                };
                $items->adicionales = $adicionales;
            }
        }
        foreach ($result as $item) {
            //echo $item;
            $ticket = DB::table('ticket_histories')
                ->select('ticket_histories.*', 'users.nombre_user', 'users.apellido_user')
                ->join('users', 'ticket_histories.user_th', '=', 'users.id_user')
                ->orderBy('ticket_histories.created_at', 'DESC')
                ->where('ticket_th', '=', $item->id_soporte)
                ->get();
            $item->history = $ticket;
            $problems = DB::table('ticket_problems')
                ->select('problem_pb')
                ->where('ticket_pb', '=', $item->id_soporte)
                ->get();
            $item->problems = $problems;
        };

        return $result;
        //return response()->json($result);
    }


    public function store(Request $request)
    {
        return soportes::create($request->all());

    }

    public function store3(Request $request)
    {
        //return soportes::create($request->all());
        $insert = new tipos_soporte;
        $insert->nombre = "ap";
        $insert->fac_id = $request->id;
        $insert->value = $request->ap;
        $insert->save();
        $insert = new tipos_soporte;
        $insert->nombre = "ip";
        $insert->fac_id = $request->id;
        $insert->value = $request->ip;
        $insert->save();
        if ($request->ser1al == 0) {
            $insert = new tipos_soporte;
            $insert->nombre = "Serial";
            $insert->fac_id = $request->id;
            $insert->value = $request->serial;
            $insert->save();
        }

    }

    /**
     * @param Request $request
     * @return planes|\Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function store1(Request $request)
    {
        $responsable = $request->user_soporte;
        unset($request['responsable']);
        $valores = $request->all();
        $adicionales = $request->adicionales;

        $user = DB::table('servicios')
            ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
            //->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            //->orderBy('clientes.nombre','ASC')
            ->where('servicios.id_srv', '=', $request->servicio_soporte)
            ->first();
        //$id=soportes::create($request->all());
        $soporte = new soportes;
        $soporte->servicio_soporte = $request->servicio_soporte;
        $soporte->status_soporte = $request->status_soporte;
        $soporte->comment_soporte = $request->comment_soporte;
        $soporte->user_soporte = $request->user_soporte;
        $soporte->afectacion_soporte = $request->afectacion_soporte;
        $soporte->tipo_soporte = $request->tipo_soporte;
        $id = soportes::create($soporte->toArray());

        /*  $instal_pend = new pendiente_servi;
           $instal_pend->soporte_pd = $id->id;
           $instal_pend->cliente_pd = $request->servicio_soporte;
           $instal_pend->celda_pd = $request->celda_soporte;
           $instal_pend->plan_pd = $request->plan_srv;
           $instal_pend->ip_pd = $request->ip_srv;
           $instal_pend->status_pd = 2;
           $instal_pend->save();*/





        if ($request->tipo_soporte == '1') {
            $user = clientes::where('id', $request->servicio_soporte)->first();
            if ((strtolower($user->kind) == 'g' || strtolower($user->kind) == 'j') && (strtolower($user->social) != 'null' && $user->kind != null)) {
                $cliente = ucwords(strtolower($user->social));
            } else {
                $cliente = ucfirst($user->nombre) . " " . ucfirst($user->apellido);
            }
            historico_cliente::create(['history' => 'Instalacion nueva: ' . $cliente, 'modulo' => 'Soporte', 'cliente' => $user->id, 'responsable' => $responsable]);
            historico::create(['responsable' => $responsable, 'modulo' => 'Soporte', 'detalle' => 'Creo el ticket de instalacion ' . $id->id . ' para el cliente ' . $cliente]);

            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = "Se Agenda La Instalacion";
            $insert->save();
            /*$insert = new tipos_soporte;
            $insert->nombre = "equipo";
            $insert->fac_id = $id->id;
            $insert->value = $request->equipo_soporte;
            $insert->save();*/
            $insert = new tipos_soporte;
            $insert->nombre = "celda";
            $insert->fac_id = $id->id;
            $insert->value = $request->celda_soporte;
            $insert->save();
            $insert = new tipos_soporte;
            $insert->nombre = "ipP";
            $insert->fac_id = $id->id;
            $insert->value = $request->ip_srv;
            $insert->save();
            $insert = new tipos_soporte;
            $insert->nombre = "plan";
            $insert->fac_id = $id->id;
            $insert->value = $request-> plan_srv;
            $insert->save();
            $insert = new tipos_soporte;
            $insert->nombre = "tipo_plan";
            $insert->fac_id = $id->id;
            $insert->value = $request->tipo_plan_srv;
            $insert->save();
            $insert = new lista_ip;
            $insert->ip = $request->ip_srv;
            $insert->cliente_ip = $request->servicio_soporte;
            $insert->status_ip = 2;
            $insert->ip_servicio = 0;
            $insert->save();
            foreach ($adicionales as $req) {
                if ($req["nombre"] == "SerialEquipo") {
                    inventarios::where('serial_inventario', '=', $req["valor"])->delete();
                }
                if ($req["nombre"] == "SerialAntenna") {
                    inventarios::where('serial_inventario', '=', $req["valor"])->delete();
                }
                if ($req["nombre"] == "Router") {
                    inventarios::where('serial_inventario', '=', $req["valor"])->delete();
                }
                if ($req["nombre"] == "SerialPTP") {
                    inventarios::where('serial_inventario', '=', $req["valor"])->delete();
                }
                $insert = new tipos_soporte;
                $insert->nombre = $req["nombre"];
                $insert->fac_id = $id->id;
                $insert->value = $req["valor"];
                $insert->save();
            }
            // return 201;
        } elseif ($request->tipo_soporte == '2') {
            $user = DB::table('servicios')
                ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
                //->join('aps','aps.id','=','servicios.ap_srv')
                ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                //->orderBy('clientes.nombre','ASC')
                ->where('servicios.id_srv', '=', $request->servicio_soporte)
                ->first();

            foreach ($request->problems as $problem) {
                DB::table('ticket_problems')->insert(
                    [
                        'ticket_pb' => $id->id,
                        'problem_pb' => $problem,
                        "created_at" => \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                    ]
                );
            }
            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = "Se realiza la apertura del ticket";
            $insert->save();
            $mensaje = 'Se ha agendado un ticket para su servicio ' . $user->name_plan . ', numero de ticket ' . $id->id;
            if ((strtolower($user->kind) == 'g' || strtolower($user->kind) == 'j') && (strtolower($user->social) != 'null' && $user->kind != null)) {
                $cliente = ucwords(strtolower($user->social));
                $message = "MARAVECA: Srs. " . ucwords(strtolower($user->social)) . ", " . $mensaje;
            } else {
                $cliente = ucfirst($user->nombre) . " " . ucfirst($user->apellido);
                $message = "MARAVECA: Sr(a) " . ucfirst($user->nombre) . " " . ucfirst($user->apellido) . ", " . $mensaje;
            }
            historico_cliente::create(['history' => 'Ticket nuevo para el cliente: ' . $cliente . ' servicio: ' . $user->name_plan, 'modulo' => 'Soporte', 'cliente' => $user->cliente_srv, 'responsable' => $responsable]);
            historico::create(['responsable' => $responsable, 'modulo' => 'Soporte', 'detalle' => 'Creo el ticket ' . $id->id . ' para el cliente ' . $cliente]);

            //sendsms($user->phone1, $message);
        } elseif ($request->tipo_soporte == '3') {
            $celda = celdas::where('id_celda', $id->servicio_soporte)->first();
            historico::create(['responsable' => $responsable, 'modulo' => 'Soporte', 'detalle' => 'Creo el ticket de averia ' . $id->id . ' para la celda ' . $celda->nombre_celda]);
            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = "Se realiza la apertura del ticket";
            $insert->save();
            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = $request->comment_soporte;
            $insert->save();


        } elseif ($request->tipo_soporte == '4') {
            $celda = celdas::where('id_celda', $id->servicio_soporte)->first();
            historico::create(['responsable' => $responsable, 'modulo' => 'Soporte', 'detalle' => 'Creo el ticket de averia ' . $id->id . ' para la celda ' . $celda->nombre_celda]);
            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = "Se realiza la apertura del ticket";
            $insert->save();
            $insert = new ticket_history;
            $insert->ticket_th = $id->id;
            $insert->user_th = $request->user_soporte;
            $insert->comment = $request->comment_soporte;
            $insert->save();
        }

        /* ===============================================================================================================================================================================================  */
        $planes = planes::where('planes.id_plan', $request->plan_srv)->first();
        $clientes1= clientes::where('clientes.id', $request->servicio_soporte)->first();
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servidores.*'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.id_soporte', '=' , $id->id)
            ->first();
        if((strtolower($clientes1->kind)=='g'||strtolower($clientes1->kind)=='j')&&(strtolower($clientes1->social)!= 'null' && $clientes1->kind != null)){
            $cliente2= ucwords(strtolower($clientes1->social));
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }else {
            $cliente2= ucfirst($clientes1->nombre)." ".ucfirst($clientes1->apellido);
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }
        cola_de_ejecucion::create(['id_srv'=>$result->id_soporte."P_I",'soporte_pd'=>$result->id_soporte, 'accion'=>'a_p_i', 'contador'=>'1']);

        if ($planes->carac_plan == 1 ) {
            $parent = "Asimetricos";
        } else if ($planes->carac_plan ==2 )  {

            $parent = "none";
        }
        $status= "P_I";

        activar_ip_pd($request->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor , $planes->dmb_plan, $planes->umb_plan, $parent, $status,$result->id_soporte);
        historico_cliente::create(['history'=>'ip activada para su instalacion', 'modulo'=>'Soporte', 'cliente'=>$request->servicio_soporte, 'responsable'=>$responsable]);
        historico::create(['responsable'=>$responsable, 'modulo'=>'Soporte', 'detalle'=>'ip activa asignada para el cliente: '.$cliente]);
        /*$adicionales=servicios::where('id_srv', $id);
        $adicionales->update(['stat_srv'=>1]);*/
        /*   =============================================================================================================================================================================================== */
        return $planes;
    }
    public function retirar_ip_mk(Request $request, $id){

        $soporte = soportes::where('id_soporte', $id)->first();
        $clientes1= clientes::where('clientes.id', $soporte->servicio_soporte)->first();
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servidores.*'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.id_soporte', '=' , $id)
            ->first();
        if((strtolower($clientes1->kind)=='g'||strtolower($clientes1->kind)=='j')&&(strtolower($clientes1->social)!= 'null' && $clientes1->kind != null)){
            $cliente2= ucwords(strtolower($clientes1->social));
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }else {
            $cliente2= ucfirst($clientes1->nombre)." ".ucfirst($clientes1->apellido);
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }
        cola_de_ejecucion::create(['id_srv'=>$result->id_soporte."P_I", 'soporte_pd'=>$result->id_soporte, 'accion'=>'r_p_i', 'contador'=>'1']);


        $status= "P_I";

        retirar_ip_pd($request->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor ,$status,$result->id_soporte);

    }

    public function updateinstall(Request $request, $id)
    {
        $valores = $request->all();
        $adicionales = $request->adicionales;
        $soporte = new soportes;
        $soporte->servicio_soporte = $request->servicio_soporte;
        $soporte->status_soporte = $request->status_soporte;
        $soporte->comment_soporte = $request->comment_soporte;
        $soporte->user_soporte = $request->user_soporte;
        $soporte->afectacion_soporte = $request->afectacion_soporte;
        $soporte->tipo_soporte = $request->tipo_soporte;
        $ticket = soportes::where('id_soporte', '=', $id);
        $ticket = update($soporte);

        $adicion = tipo_soporte::where('fac_id', '=', $id)
            ->delete();
        $insert = new ticket_history;
        $insert->ticket_th = $id->id;
        $insert->user_th = $request->user_soporte;
        $insert->comment = "Datos De Instalacion Modificados";
        $insert->save();
        if (isset($request->equipo_soporte) && $request->equipo_soporte != null) {
            $insert = new tipos_soporte;
            $insert->nombre = "equipo";
            $insert->fac_id = $id->id;
            $insert->value = $request->equipo_soporte;
            $insert->save();
        }
        if (isset($request->celda_soporte) && $request->celda_soporte != null) {
            $insert = new tipos_soporte;
            $insert->nombre = "celda";
            $insert->fac_id = $id->id;
            $insert->value = $request->celda_soporte;
            $insert->save();
        }
        foreach ($adicionales as $req) {
            $insert = new tipos_soporte;
            $insert->nombre = $req["nombre"];
            $insert->fac_id = $id->id;
            $insert->value = $req["valor"];
            $insert->save();
        }

        return 200;
    }

    public function update(Request $request, $id)
    {
        $clientes = soportes::where('id_soporte', '=', $id);
        $clientes->update($request->all());

        tipos_soporte::where('fac_id', $id)->where('nombre', 'ipP')->update(['nombre' => 'ip']);
        return 200;
        //return 200;

    }
    /*public function update_tp(Request $request, $id)
       {

           tipos_soporte::where('fac_id', $id)->where('nombre', 'ipP')->update(['nombre' => 'ip']);
           return 200;
       }*/

    public function closeinstall(Request $request, $id)
    {
        
        $list_ip = tipos_soporte :: where('fac_id', $id)->where('nombre', 'ipP')->get();
        $install = soportes::where('id_soporte', '=', $id);
        $installer = User::where('id_user', '=', $request->installer);
        $tmp = $install->first();
        $cliente = DB::table('clientes')
            ->orderByRaw(
                "CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC"
            )
            ->where('id', '=', $tmp->servicio_soporte)
            ->first();

        $instalacion = new instinst;
        if (strtolower($cliente->kind) == 'v' || strtolower($cliente->kind) == 'e') {
            $instalacion->ncliente = $cliente->nombre . " " . $cliente->apellido;
        } else {
            $instalacion->ncliente = ucwords($cliente->social);
        }
        $instalacion->ticket = $tmp->id_soporte;
        $instalacion->installer = $request->installer;
        $instalacion->stat = '1';
        $instalacion->save();

        $install->update(["status_soporte" => '2']);
        $installer->update(['installs' => $tmp->installs + 1]);
        //  if (count($ints_pen)<=0) {
        $instal_pend = new pendiente_servi;
        $instal_pend->soporte_pd = $id;
        $instal_pend->cliente_pd = $request->servicio_soporte;
        $instal_pend->celda_pd = $request->celda;
        $instal_pend->plan_pd = $request->plan;
        $instal_pend->ip_pd = $request->ip;
        $instal_pend->status_pd = 2;
        $instal_pend->save();
        //}
        if (strlen($request->serial) >= 2) {
            if ($request->ser1al == 1) {
                tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialEquipo')->update(['value' => $request->serial]);
            } else {
                $adicionales = new tipos_soporte;
                $adicionales->nombre = 'SerialEquipo';
                $adicionales->fac_id = $id;
                $adicionales->value = $request->serial;
                $adicionales->save();
            }
        }

        if (count($list_ip)<=0){
            $adicionales = new tipos_soporte;
            $adicionales->nombre = 'ipP';
            $adicionales->fac_id = $id;
            $adicionales->value = $request->ip;
            $adicionales->save();
        }
        $adicionales = new tipos_soporte;
        $adicionales->nombre = 'ap';
        $adicionales->fac_id = $id;
        $adicionales->value = $request->ap;
        $adicionales->save();
        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user;
        $insert->comment = "Se Cierra el ticket";
        $insert->save();
        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user;
        $comment = "Se usaron " . $request->conectores . " conectores y " . $request->cable . " metros de cable";
        $insert->comment = $comment;
        $insert->save();
        
        return response()->json($request);

    }

    public function closeoinstall(Request $request, $id)
    {
        // return $id;
        $install = soportes::where('id_soporte', '=', $id);
        $installer = User::where('id_user', '=', $request->installer);
        $tmp = $install->first();

        $instalacion = new oinstall;

        $instalacion->ticket = $tmp->id_soporte;
        $instalacion->installer = $request->installer;
        $instalacion->status_pgo = '1';
        $instalacion->comment_tr = $tmp->comment_soporte;
        $instalacion->save();

        $install->update(["status_soporte" => '2']);
        $installer->update(['installs' => $tmp->installs + 1]);

        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user;
        $insert->comment = "Se Cierra el ticket";
        $insert->save();
        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user;
        $comment = $tmp->comment_soporte;
        $insert->comment = $comment;
        $insert->save();
        return 200;
    }

    public function delete(Request $request, $id)
    {
        $clientes = clientes::findOrFail($id);
        $clientes->delete();

        return 204;
    }

    public function verificar(Request $req)
    {

        $serial = $req->serial;
        $verify = tipos_soporte::where('value', '=', $serial)->get();

        $verify1 = inventarios::where('serial_inventario', '=', $serial)->get();
        if (isset($verify->first()->value) && $verify->first()->value == $serial || isset($verify1->first()->serial_inventario) && $verify1->fisrt()->serial_inventario == $serial) {
            $r = '1';
            return $r;
        } else {
            $r = '0';
            return $r;
        }


    }

    public function showip()
    {
        //return servicios::all();
        $result1 = DB::table('servicios')
            ->select(\DB::raw('cliente_srv, clientes.kind, clientes.nombre, clientes.apellido,clientes.social,clientes.dni, ip_srv ,stat_srv '))
            ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
            ->orderByRaw(
                "CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC"
            )
            ->get();

        $result2=DB::table('soportes')  //inicio el query con sus join y orders
        ->select(\DB::raw( 'clientes.id as id_cliente, clientes.kind, clientes.nombre, clientes.apellido,clientes.social,clientes.dni, clientes.phone1,clientes.phone2,clientes.direccion,ip_pendientes.value as ipP'))
            ->join('tipos_soportes as ip_pendientes', function($join) {
                $join->on('ip_pendientes.fac_id','=','soportes.id_soporte')
                    ->where('ip_pendientes.nombre', '=', 'ipP');
            })
            ->join('clientes','soportes.servicio_soporte','=','clientes.id');
        $inst = $result2->get(); //envio el query y almaceno el retorno en $ins

        $index = collect(['ip_asignadas' => $result1, 'ip_pendientes' => $inst]);

        return response()->json($index); //lo devuelvo via rest los soportes de determinado usuario

    }
    public function showtickets(Request $request)
    {
        if(isset($request->year)&&$request->year!=''){
            $year=$request->year;
        }else{
            $year=date('Y');
        }
        if(isset($request->month)&&$request->month!=''){
            $month=$request->month;
        }else{
            $month=date('n');
        }
        $yr=$year;
        $ms = $month;
        //return user::select(DB::raw('*, (select count(*) from ticket_histories where ticket_histories.user_th = users.id_user and ticket_histories.comment = "Se Cierra el ticket" and users.installer= 1) as installs'))

        $result  = DB::table('users')
            ->select(array(/*'ticket_histories.*',*/ DB::raw('*, (select count(*) from ticket_histories join soportes on soportes.id_soporte= ticket_histories.ticket_th  
    where ticket_histories.user_th = users.id_user and ticket_histories.comment = "Se Cierra el ticket" and soportes.tipo_soporte = 2 and MONTH(ticket_histories.created_at) = '.$ms.' and YEAR(ticket_histories.created_at) = '.$yr.'  ) as installs ')))
            ->join('ticket_histories', 'users.id_user', '=', 'ticket_histories.user_th')

            ->groupBy('users.id_user')
            ->orderBy('installs', 'DSC')
            ->get();
        //   // return user::select(DB::raw('*, (select count(*) from ticket_histories join soportes on soportes.id_soporte= ticket_histories.ticket_th  where ticket_histories.user_th = users.id_user and ticket_histories.comment = "Se Cierra el ticket"   and soportes.tipo_soporte = 2  ) as installs'))/*->where('installer', '=', '1')*/->where("MONTH(users.created_at) = $month and YEAR(users.created_at) = $year")->get();
        return response()->json($result);
        /*  return user::select(DB::raw('*, (select count(*) from ticket_histories join soportes on soportes.id_soporte= ticket_histories.ticket_th
          where ticket_histories.user_th = users.id_user and ticket_histories.comment = "Se Cierra el ticket" and soportes.tipo_soporte = 2 and MONTH(ticket_histories.created_at) = '.$ms.' and YEAR(ticket_histories.created_at) = '.$yr.'  ) as installs ')->get();*/

    }

    public function show_tickets_user(Request $request, $id){
        if(isset($request->year)&&$request->year!=''){
            $year=$request->year;
        }else{
            $year=date('Y');
        }
        if(isset($request->month)&&$request->month!=''){
            $month=$request->month;
        }else{
            $month=date('n');
        }
        $yr=$year;
        $ms = $month;

        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servicios.cliente_srv, clientes.kind, clientes.nombre, clientes.apellido, clientes.social, clientes.phone1, clientes.phone2, clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor, celdas.nombre_celda, ticket_histories.created_at as fecha_cierre'))
            ->join('servicios', 'servicios.id_srv', '=', 'soportes.servicio_soporte')
            ->join('aps', 'servicios.ap_srv', '=', 'aps.id')
            ->join('celdas', 'aps.celda_ap', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'servicios.cliente_srv', '=', 'clientes.id')
            ->join('ticket_histories', 'soportes.id_soporte', '=', 'ticket_histories.ticket_th')
            ->join('users', 'users.id_user', '=', 'ticket_histories.user_th')


            ->where('soportes.tipo_soporte', '2')/* solo tickets*/
            // ->where('soportes.status_soporte', '2') /*cerrados*/
            ->where('ticket_histories.user_th', $id) /* id del usuario*/
            ->whereRaw("MONTH(ticket_histories.created_at) = $ms and YEAR(ticket_histories.created_at) = $yr")
            ->where('ticket_histories.comment', 'Se Cierra el ticket') /* tickets cerrados por el usuario*/
            ->orderBy('ticket_histories.created_at', 'DSC')
            // ->orderBy('soportes.updated_at', 'DSC')
            ->get();
        return response()->json($result);

    }

    public function editar_install(Request $request, $id){
        $adicionales = $request->adicionales;

        $ip_v= tipos_soporte::where('fac_id', $id)->where('nombre', 'ipP')->first()->value;


        if($request->EN == false){ //En caso de que no sea equipo usado se actualizara o agregara el modelo del equipo y su serial

            $modelo_equip = tipos_soporte::where('fac_id', $id)->where('nombre', 'ModeloEquipo')->first();
            $serial_equip = tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialEquipo')->first();

            if (isset($modelo_equip)){ //equipo

                tipos_soporte::where('fac_id', $id)->where('nombre', 'ModeloEquipo')->update(['value' => $request->equipo_soporte]);
            }
            else{
                $insert = new tipos_soporte;
                $insert->nombre = "ModeloEquipo";
                $insert->fac_id = $id;
                $insert->value = $request->equipo_soporte;
                $insert->save();

            } if(isset($serial_equip)) { //serial

                tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialEquipo')->update(['value' => $request->seriale]);
            }
            else{
                $insert = new tipos_soporte;
                $insert->nombre = "SerialEquipo";
                $insert->fac_id = $id;
                $insert->value = $request->seriale;
                $insert->save();
            }
        } else{ //En caso de que sea equipo usado se procedera a eliminar el modelo del equipo y su serial
            tipos_soporte::where('fac_id', $id)->where('nombre', 'ModeloEquipo')->delete();

            tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialEquipo')->delete();


        }






        tipos_soporte::where('fac_id', $id)->where('nombre', 'tipo_plan')->update(['value' => $request->tipo_plan_srv]);
        tipos_soporte::where('fac_id', $id)->where('nombre', 'plan')->update(['value' => $request->plan_srv]);
        tipos_soporte::where('fac_id', $id)->where('nombre', 'ipP')->update(['value' => $request->ip_srv]);
        tipos_soporte::where('fac_id', $id)->where('nombre', 'celda')->update(['value' => $request->celda_soporte]);



        foreach ($adicionales as $req) {

            $ant = tipos_soporte::where('fac_id', $id)->where('nombre', 'Antena')->first();
            $router = tipos_soporte::where('fac_id', $id)->where('nombre', 'Router')->first();
            $ptp = tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialPTP')->first();
            $tubo = tipos_soporte::where('fac_id', $id)->where('nombre', 'Tubo')->first();
            if ($req["nombre"] == "Antena") {
                if (isset($ant)){
                    tipos_soporte::where('fac_id', $id)->where('nombre', 'Antena')->update(['value' =>$req["valor"]]);
                }
                else{
                    $insert = new tipos_soporte;
                    $insert->nombre = "Antena";
                    $insert->fac_id = $id;
                    $insert->value = $req["valor"];
                    $insert->save();

                }

            }

            if ($req["nombre"] == "Router") {
                if (isset($router)){
                    tipos_soporte::where('fac_id', $id)->where('nombre', 'Router')->update(['value' =>$req["valor"]]);
                }
                else{
                    $insert = new tipos_soporte;
                    $insert->nombre = "Router";
                    $insert->fac_id = $id;
                    $insert->value = $req["valor"];
                    $insert->save();

                }
            }
            if ($req["nombre"] == "SerialPTP") {
                if (isset($ptp)){
                    tipos_soporte::where('fac_id', $id)->where('nombre', 'SerialPTP')->update(['value' =>$req["valor"]]);
                }
                else{
                    $insert = new tipos_soporte;
                    $insert->nombre = "SerialPTP";
                    $insert->fac_id = $id;
                    $insert->value = $req["valor"];
                    $insert->save();

                }
            }
            if ($req["nombre"] == "Tubo") {
                if (isset($tubo)){
                    tipos_soporte::where('fac_id', $id)->where('nombre', 'Tubo')->update(['value' =>$req["valor"]]);
                }
                else{
                    $insert = new tipos_soporte;
                    $insert->nombre = "Tubo";
                    $insert->fac_id = $id;
                    $insert->value = $req["valor"];
                    $insert->save();

                }


            }
        }

//comienza el proceso de retirar la ip anterior y agregar la nueva en el mk en caso de que haya actualizacion de ip
       /* ===============================================retirar ip mk========================================================================*/
$router = servidores::where ('id_srvidor',$request->id_servidor )->first();
        $soporte = soportes::where('id_soporte', $id)->first();
        $clientes1= clientes::where('clientes.id', $request->servicio_soporte)->first();
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servidores.*'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.id_soporte', '=' , $id)
            ->first();
        if((strtolower($clientes1->kind)=='g'||strtolower($clientes1->kind)=='j')&&(strtolower($clientes1->social)!= 'null' && $clientes1->kind != null)){
            $cliente2= ucwords(strtolower($clientes1->social));
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }else {
            $cliente2= ucfirst($clientes1->nombre)." ".ucfirst($clientes1->apellido);
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }
        cola_de_ejecucion::create(['id_srv'=>$result->id_soporte."P_I", 'soporte_pd'=>$result->id_soporte, 'accion'=>'r_p_i', 'contador'=>'1']);


        $status= "P_I";

        retirar_ip_pd( $ip_v, $cliente, $router->ip_srvidor, $router->user_srvidor, $router->password_srvidor ,$status,$result->id_soporte);
        /* ===================================================================================================================================*/

        /* ===============================================ingresar ip mk con actualizaciones========================================================================*/
        $planes = planes::where('planes.id_plan', $request->plan_srv)->first();
        $clientes1= clientes::where('clientes.id', $request->servicio_soporte)->first();
        $result = DB::table('soportes')//inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, servidores.*'))
            ->join('tipos_soportes as celdas_ins', function ($join) {
                $join->on('celdas_ins.fac_id', '=', 'soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas', 'celdas_ins.value', '=', 'celdas.id_celda')
            ->join('servidores', 'servidores.id_srvidor', '=', 'celdas.servidor_celda')
            ->join('clientes', 'soportes.servicio_soporte', '=', 'clientes.id')
            ->join('users', 'users.id_user', '=', 'soportes.user_soporte')
            ->orderBy('soportes.status_soporte', 'ASC')
            ->orderBy('soportes.updated_at', 'DSC')
            ->where('soportes.id_soporte', '=' , $id)
            ->first();
        if((strtolower($clientes1->kind)=='g'||strtolower($clientes1->kind)=='j')&&(strtolower($clientes1->social)!= 'null' && $clientes1->kind != null)){
            $cliente2= ucwords(strtolower($clientes1->social));
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }else {
            $cliente2= ucfirst($clientes1->nombre)." ".ucfirst($clientes1->apellido);
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }
        cola_de_ejecucion::create(['id_srv'=>$result->id_soporte."P_I",'soporte_pd'=>$result->id_soporte, 'accion'=>'a_p_i', 'contador'=>'1']);

        if ($planes->carac_plan == 1 ) {
            $parent = "Asimetricos";
        } else if ($planes->carac_plan ==2 )  {

            $parent = "none";
        }
        $status= "P_I";

        activar_ip_pd($request->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor , $planes->dmb_plan, $planes->umb_plan, $parent, $status,$result->id_soporte);

        historico::create(['responsable'=>$request->user_soporte, 'modulo'=>'Soporte', 'detalle'=>'Edicion de ip para la instalacion del cliente : '.$cliente.' de la '.$ip_v.' a la '.$request->ip_srv]);
        /*$adicionales=servicios::where('id_srv', $id);
        $adicionales->update(['stat_srv'=>1]);*/
        /* ===================================================================================================================================*/
        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user_soporte;
        $insert->comment = "Instalacion Editada";
        $insert->save();

        return 200;
    }

    public function anular_install(Request $request, $id){

        soportes::where('id_soporte', $id)->update(['status_soporte' => '3']);

        //una vez anulado se procede a elimnar del mk

        /* ===============================================retirar ip mk========================================================================*/
        $ip_v= tipos_soporte::where('fac_id', $id)->where('nombre', 'ipP')->first()->value;
        $router = servidores::where ('id_srvidor',$request->id_servidor )->first();
        $clientes1= clientes::where('clientes.id', $request->servicio_soporte)->first();

        if((strtolower($clientes1->kind)=='g'||strtolower($clientes1->kind)=='j')&&(strtolower($clientes1->social)!= 'null' && $clientes1->kind != null)){
            $cliente2= ucwords(strtolower($clientes1->social));
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }else {
            $cliente2= ucfirst($clientes1->nombre)." ".ucfirst($clientes1->apellido);
            $remp_cliente= array('ñ', 'Ñ');
            $correct_cliente= array('n', 'N');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
        }
        cola_de_ejecucion::create(['id_srv'=>$id."P_I", 'soporte_pd'=>$id, 'accion'=>'r_p_i', 'contador'=>'1']);


        $status= "P_I";
        retirar_ip_pd(  $ip_v, $cliente, $router->ip_srvidor, $router->user_srvidor, $router->password_srvidor ,$status,$id);

        historico::create(['responsable'=>$request->user_soporte, 'modulo'=>'Soporte', 'detalle'=>'Anulacion de insalacion del cliente: '.$cliente]);
        $insert = new ticket_history;
        $insert->ticket_th = $id;
        $insert->user_th = $request->user_soporte;
        $insert->comment = "Instalacion anulada";
        $insert->save();
        /* ===================================================================================================================================*/


return 200;


    }




}
