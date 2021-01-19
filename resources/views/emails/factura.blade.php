<html>
<head>
  <style>
  tr.hover:hover{
    background-color: rgb(80, 108, 181)
  }

  table.fac, th.fac, td.fac {
    border:1px solid black;
    font-size: 16px;
  }
  table.facf, th.facf, td.facf {
    border:1px solid black;
    font-size: 15px;
  }
  table.fact, th.fact, td.fact {
    font-size: 18px;
    border-bottom: 1px solid black;

  }
  table.fontprod, th.fontprod, td.fontprod {
    font-size: 14px
  }
  .boto {
    font-size: 18px;
    border-top: 1px solid black;
  }
  .bobo {
    border-bottom: 1px solid black
  }



  .sidenav{
    min-width: 30%;
    border-radius: 2px;
    border-color: grey;
    background-color: rgb(186, 252, 200);
  }

  .pie{
    position: fixed;
    bottom: 170px;
    width: 100%;
  }

  .submenu{
    max-width:100vw;
    padding-top: 60px;
  }

  .contenido{
    background-color: rgba(255, 255, 255, 0.6);
    align:center;
    overflow-y: auto;
    max-height: 70%
  }

  .example-form {
    min-width: 150px;
    max-width: 500px;
    width: 100%;
  }

  .example-full-width {
    width: 100%;
  }
  .example-half-width {
    width: 100%;
  }

  .example-spacer {
    flex: 1 1 auto;
  }
  md2-select-header {
    position: relative;
    display: block;
    height: 48px;
    padding-left: 10px;
    border-bottom: 1px solid rgba(black, 0.12);

    input {
      border: none;
      outline: none;
      height: 100%;
      width: 100%;
      padding: 0;
    }
  }



  $primary: #106cc8 !default;
  $warn: #f44336 !default;
  $md2-select-trigger-height: 30px !default;
  $md2-select-trigger-min-width: 112px !default;
  $md2-select-arrow-size: 5px !default;
  $md2-select-arrow-margin: 4px !default;
  $md2-select-panel-max-height: 256px !default;
  $md2-select-trigger-font-size: 16px !default;


  &.md2-floating-placeholder {
    top: -22px;
    left: -2px;
    text-align: left;
    transform: scale(0.75);
  }

  [dir='rtl'] & {
    transform-origin: right top;

    &.md2-floating-placeholder {
      left: 2px;
      text-align: right;
    }
  }

  [aria-required=true] &::after {
    content: '*';
  }
}

.cdk-overlay-container {
  position: fixed;
  z-index: 1000;
}

.cdk-overlay-pane {
  position: absolute;
  pointer-events: auto;
  box-sizing: border-box;
  z-index: 1000;
}

.cdk-overlay-backdrop {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  pointer-events: auto;
  transition: opacity 400ms cubic-bezier(0.25, 0.8, 0.25, 1);
  opacity: 0;
}

.cdk-overlay-transparent-backdrop {
  background: none;
}

.contenido{
  margin: 0px;
  /*background-repeat: no-repeat;*/
  background-size: content;
  min-width:100%;
  max-width:100%;
  width:100%;
  height:100%;
  min-height:100%;
  max-height:100%;
  background-image: url({{URL::asset('img/factura.jpg')}});
}

.cdk-overlay-backdrop.cdk-overlay-backdrop-showing {
  opacity: 0.48;
}
</style>
</head>
<body class="contenido">
  <!--img src="{{URL::asset('img/image1.jpg')}}" alt="Maraveca" width="100%"-->
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <table style="width:100%">
    <thead>
      <tr>
        <td class="fac" colspan="4">
          Cliente: {{ucwords($factura->cliente) }}
        </td>
        @if(isset($factura->fac_num))

        @if($cliente->serie == '1')

        <td class="fac" colspan="2" style="text-align: center">
          Recibo: {{$factura->fac_num}}
        </td>

        @else

        <td class="fac" style="text-align: center">
          Recibo: {{$factura->fac_num}}
        </td>

        @endif

        @else

        @if($cliente->serie == '1')

        <td class="fac" colspan="2" style="text-align: center">
          Recibo: {{$factura->id}}
        </td>

        @else

        <td class="fac" style="text-align: center">
          Recibo: {{$factura->id}}
        </td>

        @endif

        @endif
        <td class="facf" colspan="2" style="text-align: center">
          Emisión: {{date('d-m-Y', strtotime($factura->created_at))}}
        </td>
      </tr>
      <tr>
        @if(ucwords(strtolower($cliente->kind))=='V'||ucwords(strtolower($cliente->kind))=='E')
        <td  colspan="4" class="fac">
          Cedula:  {{ucwords(strtolower($factura->dni))}}
        </td>
        @else
        <td colspan="4" class="fac">
          R.I.F.:  {{ucwords(strtolower($factura->dni))}}
        </td>
        @endif

        @if($cliente->serie == '1')

        <td class="fac" style="text-align: center" colspan="4">
          Direccion de entrega (domicilio fiscal)
        </td>

        @else

        <td class="fac" style="text-align: center" colspan="3">
          Direccion de entrega (domicilio fiscal)
        </td>

        @endif

      </tr>
      <tr>
        <td *ngIf=" client " colspan="4" class="fac">
          Correo electrónico: {{$factura->email}}
        </td>

        @if($cliente->serie == '1')

        <td rowspan="2" colspan="4" class="fac" style="text-align:center; vertical-align: text-center">
          {{$factura->address}}
        </td>

        @else

        <td rowspan="2" colspan="3" class="fac" style="text-align:center; vertical-align: text-center">
          {{$factura->address}}
        </td>

        @endif
      </tr>
      <tr>
        <td  class="fac" colspan="4">
          Teléfonos: {{$factura->phone}}
        </td>
      </tr>
      <tr class="fact">
        <th  class="fact fac" style="font-weight: bold; text-align: center">Codigo</th>
        <th  colspan="2" class="fact fac" style="font-weight: bold; text-align: center; min-width: 30%; width:30%; max-width:30%;">Nombre del articulo</th>
        <th  class="fact fac" style="font-weight: bold; text-align: center; min-width: 20%; width:20%; max-width:20%;">Comentario</th>
        @if ($cliente->serie == '1')
        <th  class="fact fac" style="font-weight: bold; text-align: center">% IVA</th>
        @endif
        <th  class="fact fac" style="font-weight: bold; text-align: center">Precio unitario</th>
        <th  class="fact fac" style="font-weight: bold; text-align: center">Cantidad</th>
        <th  class="fact fac" style="font-weight: bold; text-align: center">Total neto</th>
      </tr>
    </thead>
    <tbody >
      @foreach($productos as $producto)
      <tr>
        <td class="fontprod" style="text-align:left">
          {{$producto->codigo_articulo}}
        </td>
        <td colspan="2" class="fontprod" style="text-align:center;  min-width: 30%; width:30%; max-width:30%;">
          {{ucwords($producto->nombre_articulo)}}
        </td>
        @if ($producto->comment_articulo != null || $producto->comment_articulo != 'null')
        <td class="fontprod" style="text-align:center;  min-width: 20%; width:20%; max-width:20%;">
         
        </td>
        @endif
        @if ($cliente->serie == '1')
        <td class="fontprod">
          {{$producto->IVA}}%
        </td>
        <td class="fontprod" style="text-align:right">
          {{number_format((($producto->precio_articulo / ($producto->IVA+100)) * 100)/$producto->cantidad)}} {{$factura->denominacion}}
        </td>
        @endif
        @if($cliente->serie == '0')
        <td class="fontprod" style="text-align:right">
          {{number_format($producto->precio_articulo/$producto->cantidad)}} {{$factura->denominacion}}
        </td>
        @endif
        <td class="fontprod" style="text-align:right" >{{$producto->cantidad}}</td>
        @if ($cliente->serie == '1')
        <td class="fontprod" style="text-align:right">
          {{number_format(($producto->precio_articulo / ($producto->IVA+100)) * 100)}} {{$factura->denominacion}}
        </td>
        @endif
        @if($cliente->serie == '0')
        <td class="fontprod" style="text-align:right">
          {{number_format($producto->precio_articulo)}} {{$factura->denominacion}}
        </td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>
  <br />
  <br />
  <br />
  <br />
  <br />
  <table style="width:100%" class="pie">
    <tfoot>
      <tr style="border-top: 1px solid black; border-bottom: 1px solid black;" class="boto">
        <td class="boto bobo">
          Base Imponible
        </td>
        <td class="boto bobo">
          {{number_format($montosi)}} {{$factura->denominacion}}
        </td>
        @if($cliente->serie == '1')
        <td class="bobo boto" >
          Total I.V.A. {{$iva}}%
        </td>
        <td class="bobo boto" >
          {{number_format($impuesto)}} {{$factura->denominacion}}
        </td>
        @endif
        <td class="bobo boto">
          Total General
        </td>
        <td class="bobo boto">
          {{number_format($monto)}} {{$factura->denominacion}}
        </td>

      </tr>
    </tfoot>
  </table>
</body>
</html>
