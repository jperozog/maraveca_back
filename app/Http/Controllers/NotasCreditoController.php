<?php

namespace App\Http\Controllers;

use App\notasCredito;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NotasCreditoController extends Controller
{
    
    public function store(Request $request)
    {
        //
    }

    public function traerNotasDeCredito($id){
        $notas = DB::select("SELECT c.social,c.phone1,c.direccion,c.email,c.kind,c.dni,f.*,n.*, f.created_at as fecha_factura, 
                                 (SELECT round(SUM(fp.precio_articulo), 2) from  fac_products AS fp where f.id = fp.codigo_factura) as monto, 
                                 (SELECT round(SUM(p.pago_monto), 2) from nota_pagos AS p where n.id_nota = p.nota) as pagado
                                 FROM notas_credito AS n  
                                INNER JOIN clientes AS c ON n.id_cliente = c.id
                                INNER JOIN fac_controls AS f ON n.id_factura = f.id
                                    WHERE n.id_cliente = ? ORDER BY n.id_nota DESC",[$id]);

        foreach ($notas as $n) {
            $acumuludor = 0;
            $productos = DB::select("SELECT * FROM nota_productos WHERE codigo_nota = ?",[$n->id_nota]);
            $pagos = DB::select("SELECT * FROM nota_pagos WHERE nota = ?",[$n->id_nota]);
            foreach ($productos as $p) {
                $acumuludor = floatval($p->precio) + $acumuludor;
            }
            $n->subTotal = $acumuludor;
            $n->iva = $acumuludor * 0.16; 
            $n->productos = $productos;
            $n->pagos = $pagos;
            $n->total = $acumuludor + $n->iva;

        }


        return response()->json($notas);
    }

    public function traerProductosNota($id){
        $notas = DB::select("SELECT * FROM fac_products WHERE codigo_factura = ?",[$id]);
        return response()->json($notas);
    }

    public function guardarNota(Request $request){
        
        $fecha = date("Y-m-d H:i:s");
        $guardar = DB::insert("INSERT INTO notas_credito(tipo_nota,id_cliente,id_factura,monto_nota,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$request->tipo_nota,$request->cliente,$request->factura,$request->nota,$fecha,$fecha]);
        $traerNota = DB::select("SELECT * FROM notas_credito ORDER BY id_nota DESC LIMIT 1")["0"];
        
        $buscarProducto = DB::select("SELECT * FROM fac_products WHERE codigo_factura = ?",[$traerNota->id_factura])["0"];

        $insertaProductoNota = DB::insert("INSERT INTO nota_productos(codigo_nota,nombre_nota,precio, iva, comentario_nota, created_at, updated_at) VALUES (?,?,?,?,?,?,?)",[$traerNota->id_nota,$buscarProducto->nombre_articulo,$request->nota,$buscarProducto->IVA,$buscarProducto->comment_articulo,$fecha,$fecha]);
        
        //$result = DB::select("SELECT * FROM fac_products WHERE codigo_factura = ?",[$request->factura]);

        return response()->json($request);
    }


    public function guardarPagoNota(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $datos = $request["datos"];

        $guardarPagoNota = DB::insert("INSERT INTO nota_pagos(nota,tipo_pago,pago_monto,fecha_pago,referencia,created_at, updated_at) VALUES (?,?,?,?,?,?,?)",[$request["id_nota"],$datos["metodo_pago"],$datos["monto"],$datos["fecha"],$datos["Referencia"],$fecha,$fecha]);

        return response()->json($guardarPagoNota);
    }
   
}
