<html>
<head>
  <!--===============================================================================================-->
  <link rel="icon" type="image/png" href="{{asset('images/icons/favicon.ico')}}"/>
   <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta charset="utf-8">
    <title>Factura maraveca</title>

    <style>
    #invoice{
    padding: 30px;
}

.invoice {
    position: relative;
    background-color: #FFF;
    min-height: 680px;
    padding: 15px
}

.invoice header {
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #3989c6
}

.invoice .company-details {
    text-align: right
}

.invoice .company-details .name {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .contacts {
    margin-bottom: 20px
}

.invoice .invoice-to {
    text-align: left
}

.invoice .invoice-to .to {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .invoice-details {
    text-align: right
}

.invoice .invoice-details .invoice-id {
    margin-top: 0;
    color: #3989c6
}

.invoice main {
    padding-bottom: 50px
}

.invoice main .thanks {
    margin-top: -100px;
    font-size: 2em;
    margin-bottom: 50px
}

.invoice main .notices {
    padding-left: 6px;
    border-left: 6px solid #3989c6
}

.invoice main .notices .notice {
    font-size: 1.2em
}

.invoice table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 20px
}

.invoice table td,.invoice table th {
    padding: 15px;
    background: #eee;
    border-bottom: 1px solid #fff
}

.invoice table th {
    white-space: nowrap;
    font-weight: 400;
    font-size: 16px
}

.invoice table td h3 {
    margin: 0;
    font-weight: 400;
    color: #3989c6;
    font-size: 1.2em
}
.login100-form-btn {
    font-family: Montserrat-Bold;
    font-size: 15px;
    line-height: 1.5;
    color: #fff;
    text-transform: uppercase;
    width: 100%;
    height: 50px;
    border-radius: 25px;
    background: #2096C5;
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 25px;
    -webkit-transition: all 0.4s;
    -o-transition: all 0.4s;
    -moz-transition: all 0.4s;
    transition: all 0.4s;
}

.invoice table .qty,.invoice table .total,.invoice table .unit {
    text-align: right;
    font-size: 1.2em
}

.invoice table .no {
    color: #fff;
    font-size: 1.6em;
    background: #3989c6
}

.invoice table .unit {
    background: #ddd
}

.invoice table .total {
    background: #3989c6;
    color: #fff
}

.invoice table tbody tr:last-child td {
    border: none
}

.invoice table tfoot td {
    background: 0 0;
    border-bottom: none;
    white-space: nowrap;
    text-align: right;
    padding: 10px 20px;
    font-size: 1.2em;
    border-top: 1px solid #aaa
}

.invoice table tfoot tr:first-child td {
    border-top: none
}

.invoice table tfoot tr:last-child td {
    color: #3989c6;
    font-size: 1.4em;
    border-top: 1px solid #3989c6
}

.invoice table tfoot tr td:first-child {
    border: none
}

.invoice footer {
    width: 100%;
    text-align: center;
    color: #777;
    border-top: 1px solid #aaa;
    padding: 8px 0
}

  @media print {
    div.hidden {
      display: none;
    }
}

/* @media print {
    .invoice {
        font-size: 11px!important;
        overflow: hidden!important
    }

    .invoice footer {
        position: absolute;
        bottom: 10px;
        page-break-after: always
    }

    div.toolbar hidden-print {
      display: none;
    }

    .invoice>div:last-child {
        page-break-before: always
    }
} */
    </style>
</head>

<body>
    <div id="invoice">

    <div id="hidden" class="toolbar hidden-print">
        <div class="text-right">
            <button id="printInvoice" class="btn btn-info" style="font-family: montserrat;font-size: 15px;display: block;position: absolute;line-height: 1.5;color: #fff;text-transform: uppercase;background-color: #2096c5;border-radius: 25px;justify-content: center;align-items: center;padding: 10px 20px;transition: all 0.4s;"><i class="fa fa-print"></i> Imprimir</button>
            <script>
           $('#printInvoice').click(function(){
            Popup($('.invoice')[0].outerHTML);
            function Popup(data)
            {
                window.print();
                return true;
            }
        });
        </script>
      <form class="form-right" method="POST" action="{{ route('downloadPDF') }}">
        <input type="hidden" id="id" name="factura" value="{{json_encode($factura->id)}}">
        <button class="btn btn-info" style="font-family: montserrat;font-size: 15px;line-height: 1.5;color: #fff;text-transform: uppercase;background-color: #2096c5;border-radius: 25px;justify-content: center;align-items: center;padding: 10px 20px;transition: all 0.4s;" type="submit"><i class="fas fa-file-download"></i> Guardar PDF</button>
      </form>
        </div>
        <hr>
    </div>
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col">
                        <a target="_blank" href="https://maraveca.com">
                            <img src="https://maraveca.com/wp-content/uploads/2019/02/logoconrif.jpg" width="468" height="110" alt=""/> </a>
                    </div>
                    <div class="col company-details">
                        <h3 class="name">
                            <a target="_blank" href="https://maraveca.com" style="color: #2096d0;">
                            Maraveca Telecomunicaciones C.A.
                            </a>
                        </h3>
                        <div> Av. 13a con Calle 69 Nº 69-28, Sector Tierra Negra - <span style="color: #2096c5;font-weight: bold;">Maracaibo</span><br>Av. Manaure con Av. Josefa Camejo Edificio Anna Cris - <span style="color: #2096c5;font-weight: bold;">Coro</span></div>
                        <div>+58 261 772 5180  <span style="color: #2096c5;font-weight: bold;">//</span>  +58 268 775 5100</div>
                        <div>info@maraveca.com</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light" style="font-weight: bold;">FACTURA DE:</div>
                        <h2 class="to" style="color: #2096C5;">{{ucwords($factura->cliente) }}</h2>
                        <div class="address">@if($cliente->serie == '1')
                        {{$factura->address}}
                        @else
                        {{$factura->address}}
                        @endif</div>
                        <div class="email">{{$factura->email}}</div>
                    </div>
                    <div class="col invoice-details">
                        <h1 class="invoice-id" style="font-family: montserrat;color: #2096c5;">@if(isset($factura->fac_num))
        @if($cliente->serie == '1')
                       Recibo: {{$factura->fac_num}}
                       @else
                       Recibo: {{$factura->fac_num}}
                       @endif
                       @else
        @if($cliente->serie == '1')
                       Recibo: {{$factura->id}}
                       @else
                       Recibo: {{$factura->id}}
                       @endif
                       @endif</h1>
                        <div class="date">Emisión: {{date('d-m-Y', strtotime($factura->created_at))}}</div>
                        <div class="date">@if(ucwords(strtolower($cliente->kind))=='V'||ucwords(strtolower($cliente->kind))=='E')
                        {{ucwords(strtolower($factura->dni))}}
                        @else
                        {{ucwords(strtolower($factura->dni))}}
                        @endif</div>
                    </div>
                </div>
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">NOMBRE DEL PLAN</th>
                            <th class="text-left">COMENTARIO</th>
                            @if ($cliente->serie == '1')
                            <th class="text-left">IVA</th>
                            @endif
                            <th class="text-right">PRECIO UNITARIO</th>
                            <th class="text-right">CANTIDAD</th>
                            <th class="text-right">TOTAL NETO</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($productos as $producto)
                        <tr>
                            <td class="no" style="background-color: #2096c5;font-family: montserrat;">{{$producto->codigo_articulo}}</td>
                            <td class="text-left"><h3>{{ucwords($producto->nombre_articulo)}}</h3></td>
                            @if ($producto->comment_articulo != null || $producto->comment_articulo != 'null')
                            <td class="text-left">{{ucwords($producto->comment_articulo)}}</td>
                            @endif
                            @if ($cliente->serie == '1')
                            <td class="unit">{{$producto->IVA}}%</td>
                            <td class="qty">{{number_format((($producto->precio_articulo / ($producto->IVA+100)) * 100)/$producto->cantidad)}} {{$factura->denominacion}}</td>
                            @endif
                            @if($cliente->serie == '0')
        					<td class="qty">
         					 {{number_format($producto->precio_articulo/$producto->cantidad)}} {{$factura->denominacion}}
       						</td>
        					@endif
                            <td class="unit">{{$producto->cantidad}}</td>
                            @if ($cliente->serie == '1')
        					<td class="total" style="background-color: #2096c5;font-family: montserrat;">
          					{{number_format(($producto->precio_articulo / ($producto->IVA+100)) * 100)}} {{$factura->denominacion}}
        					</td>
                            @endif
                            @if($cliente->serie == '0')
        					<td class="total" style="background-color: #2096c5;font-family: montserrat;">
          					{{number_format($producto->precio_articulo)}} {{$factura->denominacion}}
        					</td>
        					@endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="3">SUBTOTAL</td>
                            <td>{{number_format($montosi)}} {{$factura->denominacion}}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            @if($cliente->serie == '1')
                            <td colspan="3">IVA {{$iva}}%</td>
                            <td>{{number_format($impuesto)}} {{$factura->denominacion}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="3" style="color: #2096c5;border-top: 1px solid #2096c5;">TOTAL GENERAL</td>
                            <td style="color: #2096c5;border-top: 1px solid #2096c5;font-family: montserrat;">{{number_format($monto)}} {{$factura->denominacion}}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="thanks">Gracias!</div>
                @if(count($pagosfac)>0)
                <div id="notices" class="notices">
                    <div>Historial de pagos:</div>
                    <div class="notice">

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <td>
            Tipo de Pago
          </td>
          <td>
            Referencia
          </td>
          <td>
            Monto
          </td>
          <td>
            Fecha
          </td>
        </tr>
      </thead>
      <tbody>
        @foreach($pagosfac as $pagosfac)
        <tr>
          <td>
            @if($pagosfac->pag_tip == '1')
            Transferencia BOD
            @elseif($pagosfac->pag_tip == '2')
            Transferencia Banesco
            @elseif($pagosfac->pag_tip == '3')
            Transferencia Venezuela
            @elseif($pagosfac->pag_tip == '6')
            Transferencia Bicentenario
            @elseif($pagosfac->pag_tip == '4')
            Retencion ISLR
            @elseif($pagosfac->pag_tip == '5')
            Retencion IVA
            @elseif($pagosfac->pag_tip == '7')
            Exonerado
            @endif
          </td>
          <td>
            {{$pagosfac->pag_comment}}
          </td>
          <td>
            {{number_format($pagosfac->pag_monto).""}} Bs.S.
          </td>
          <td>
            {{date('d-m-Y', strtotime($pagosfac->created_at))}}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  No existen pagos cargados
@endif
                    </div>
                </div>
            </main>
            <footer>
                Factura creada en computadora y es valida sin firma y sello.
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>
</body>
</html>
