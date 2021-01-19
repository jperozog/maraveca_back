<html>
<head>
  <style>
  .center-justified {
    text-align: justify;
    -moz-text-align-last: center;
    text-align-last: center;
  }
  table.fac, th.fac, td.fac {
    border:1px solid black;
    /*font-size: 16px;*/
  }

  .blue{
    color: rgb(28, 61, 125);
  }
  .right{
    text-align:right;
    margin-right: 120px;
  }
  .left{
    text-align:left;
  }
  .bold{
    font-weight: bold;
  }
  .topb{
    border-bottom: 1px solid black
  }
  .sangria{
    margin-left: 150px;
  }
  .topN{
    margin-top: 80px;
  }
  .topM{
    margin-top: -40px;
  }
  .contenido{
    margin: 0px;
    /*background-repeat: no-repeat;*/
    min-width:100%;
    max-width:100%;
    width:100%;
    height:100%;
    min-height:100%;
    max-height:100%;
    background-size: content;
    background-image: url({{URL::asset('img/presu.jpg')}});
  }
  @page{
    /*p { page-break-after: always; }
    p:last-child { page-break-after: never; }*/
  }
  .page-break {
    page-break-after: always;
  }
  </style>
</head>
<body class="contenido">
  <div id="firstpage">
    <br />
    <br />
    <p class="blue left bold sangria topN">
       Presupuesto N. {{$idp->id}}
    </p>
    <p class="blue right bold topM">
      Maracaibo, {{$fecha}}
    </p>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    @if((strtolower($detalle->kind)=='g'||strtolower($detalle->kind)=='j')&&(strtolower($detalle->social)))
    <p class="blue bold">
      Señores:
    </p>
    @else
    <p class="blue bold">
      Señor(a):
    </p>
    @endif
    <p class="blue bold">
      {{$cliente}}
    </p>
    @if((strtolower($detalle->kind)=='g'||strtolower($detalle->kind)=='j')&&(strtolower($detalle->social)))
    <p class="blue bold">
      Rif:{{ucfirst($detalle->kind).$detalle->dni}}
    </p>
    @endif
    <br />
    <br />
    <p class="center-justified">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet para comunicación de datos a alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
    <p class="right bold blue">
      Atentamente. _________________________
    </p>
  </div>
  <div class="page-break"></div>
  <div id="secondpage">
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <p class="blue bold">
      SERVICIO DE INTERNET:
    </p>
    <table style="width:100%;">
      <tr>
        <td class="fac">
          Instalacion
        </td>
        <td class="fac">
          {{$solicitud->instalacion.$solicitud->moneda}}
        </td>
      </tr>
      <tr>
        <td style="color:white;" class="topb">
          ___
        </td>
        <td class="topb">

        </td>
      </tr>
      <tr>
        <td class="blue bold">
          Planes
        </td>
        <td class="blue bold">
          Precio
        </td>
      </tr>
      @foreach($planes as $plan)
      <tr>
        <td class="fac">
          {{$plan->name_plan}}
        </td>
        <td class="fac">
          {{$plan->cost_plan}}Bs.S
        </td>
      </tr>
      @endforeach
    </table>
    @if(isset($solicitud->factibi))
    <p class="bold">
      Se requiere un mastil de {{$solicitud->factibi}} metros no incluido en este presupuesto.
    </p>
    @else<p class="bold">
      No se incluye torre.
    </p>
    @endif
    <br />
    <br />


    <p class="blue bold">
      Importante:
    </p>

    <!-- dedicados -->
    @if($solicitud->planes=='d' || $solicitud->planes=='h')
    <ul>
      <li>
        La velocidad ofrecida para este servicio está 100% garantizada 24 horas.
      </li>
      <li>
        La conexión dedicada a Internet permite a nuestros clientes el acceso a todos los recursos y aplicaciones de la red las 24 horas del día, los 7 días a la semana.
      </li>
      <li>
        Una vez realizada la visita técnica y aprobada la oferta, solo se cancelará el monto correspondiente a el pago de instalación y primera mensualidad, luego se facturará los primeros 5 días de cada mes.
      </li>
      <li>
        En caso de requerir mayor cantidad de megas, por favor contáctenos.
      </li>
    </ul>
    @endif
    <!--asimetricos-->
    @if($solicitud->planes=='r' || $solicitud->planes=='c')
    <ul>
      <li>
        Para los planes de Internet Residencial y/o Internet Comercial la velocidad no es garantizada, puede presentar ciertas fluctuaciones durante el día.
      </li>
      <li>
        Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación y primera mensualidad, luego se facturará los primeros 5 días de cada mes.
      </li>
      <li>
        El tiempo de espera para la instalación es de 20 días hábiles, una vez aprobada la propuesta.
      </li>
      <li>
        Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
      </li>
    </ul>
    @endif

  </div>
  <div class="page-break"></div>
  <div id="thirdpage">
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <p >
      <h class="blue bold">Nota:</h> El SERVICIO DE INTERNET E INTERCONEXION tiene las siguientes características
    </p>
    <ul>
      <li>
        Futura ampliación en megas.
      </li>
      <li>
        Alta capacidad para interconexión entre sedes.
      </li>
      @if($solicitud->planes=='d' || $solicitud->planes=='h')
      <li>
        Conexión dedicada las 24 horas del día.
      </li>
      @endif
      <li>
        Tarifa plana mensual.
      </li>
      <li>
        Conexión Ilimitada.
      </li>
      @if($solicitud->planes=='d' || $solicitud->planes=='h')
      <li>
        El ancho de banda es 100% garantizado.
      </li>
      @endif
      <li>
        Los precios aquí presentados no incluyen el I.V.A
      </li>
    </ul>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <p>
      A continuación, se presentará algunos de los servicios adicionales que ofrece <h class="blue bold">Maraveca Telecomunicaciones</h>, a fin cumplir con las necesidades organizacionales y tecnológicas de nuestros clientes:
    </p>
    <ul>
      <li>
        Servicio de interconexión.
      </li>
      <li>
        Redes de voz, datos y video sobre IP.
      </li>
      <li>
        Servicio de radio comunicaciones UHF y VHF
      </li>
      <li>
        Venta, instalación de torres para Telecomunicaciones.
      </li>
      <li>
        Optimizaciones físicas en torres de telecomunicaciones.
      </li>
      <li>
        Drive test y post-procesamiento de datos.
      </li>
    </ul>

  </div>
  <div class="page-break"></div>
  <div id="thirdpage">
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <p class="bold">
      Condiciones Comerciales
    </p>
    <ul>
      <li>
        Forma de Pago:
        <li>
          Se cancela el 100% de la instalación, en un lapso de a 20 días hábiles se realiza la instalación.
        </li>
        <li>
          El pago del servicio se realizará bajo facturación mensual, el servicio se cancela por adelantado, por lo que deberá cancelar los primeros CINCO (05) días de cada mes, de lo contrario, el servicio será desconectado el día 09.
        </li>
        <li>
          Los precios no incluyen el Impuesto al Valor Agregado (IVA.). El mismo será cobrado al momento de su facturación.
        </li>
        <li>
          El tiempo de entrega e instalación por punto, una vez confirmada la factibilidad del servicio y la firma del contrato es de 20 días hábiles en la ciudad, en zonas rurales 9 días hábiles.
        </li>
        <li>
          Los precios ofertados están calculados sobre la base de los aranceles vigentes y sobre las tasas e impuestos de importación vigentes. Cualquier cambio en dichas tasas e impuestos y cualquier impuesto nuevo adicional o cambios del valor de la mano de obra modificará los precios ofertados sin previo aviso.
        </li>
        <li>
          Esta oferta tiene una duración de 10 días calendario.
        </li>
      </li>
    </ul>
  </div>
</body>
</html>
