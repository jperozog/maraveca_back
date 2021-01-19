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
     background-repeat: no-repeat;
      min-width:100%;
      max-width:100%;
      width:100%;
      height:100%;
      min-height:100%;
      max-height:100%;
      background-size: contain;
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
  @if($solicitud->planes=='d' || $solicitud->planes=='h' || $solicitud->planes=='d2' || $solicitud->planes=='h2' ||$solicitud->planes=='d3' || $solicitud->planes=='h3' || $solicitud->planes=='h6'|| $solicitud->planes=='h7')
    <p class="center-justified">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Dedicado Corporativo para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
  @if($solicitud->planes=='c' ||$solicitud->planes=='c2'||$solicitud->planes=='c3' ||$solicitud->planes=='c6'||$solicitud->planes=='c7')
    <p class="center-justified">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Asimétrico Comercial para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif @if($solicitud->planes=='r'||$solicitud->planes=='r2'||$solicitud->planes=='r3' ||$solicitud->planes=='r6'||$solicitud->planes=='r7')
    <p class="center-justified">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Asimétrico Residencial para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades residenciales.
    </p>
    <br />
    <p class="center-justified">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
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
          {{$plan->taza}}US$.
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
  <p>
    <b class="blue bold">Facturación y forma de pago:</b> El pago se realizará a través de un depósito bancario, transferencia electrónica o efectivo en nuestras oficinas a nombre de Maraveca Telecomunicaciones, C.A.
  </p>

  <br />

    <ul>
      <li>
        Se cancela el 100% de la instalación.
      </li>
      <li>
        El pago del servicio se realizará bajo facturación mensual, el servicio se cancela por adelantado, por lo que deberá cancelar los primeros CINCO (05) días de cada mes, de lo contrario, el servicio será desconectado el día 15.
      </li>
      <li>
        Los precios no incluyen el Impuesto al Valor Agregado (IVA.). El mismo será cobrado al momento de su facturación.
      </li>
      <li>
        El tiempo de entrega e instalación por punto, una vez confirmada la factibilidad del servicio y la firma del contrato es de 10 días hábiles en la ciudad y en zonas rurales.
      </li>
      <li>
        Los precios ofertados están calculados sobre la base de los aranceles vigentes y sobre las tasas e impuestos de importación vigentes. Cualquier cambio en dichas tasas e impuestos y cualquier impuesto nuevo adicional o cambios del valor de la mano de obra modificará los precios ofertados sin previo aviso.
      </li>
      <li>
        Esta oferta tiene una duración de 5 días calendario.
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
  <br />
  <br />

  <p class="blue bold">
    Importante:
  </p>

  <!-- dedicados -->
  @if($solicitud->planes=='d' || $solicitud->planes=='h' || $solicitud->planes=='d2' || $solicitud->planes=='h2' ||$solicitud->planes=='d3' || $solicitud->planes=='h3')
    <ul>
      <li>
        La velocidad ofrecida para el servicio Dedicado Corporativo está garantizada 100% las 24 horas.
      </li>
      <li>
        La conexión dedicada a internet permite a nuestros clientes el acceso a todos los recursos y aplicaciones de la red las 24 horas del día, los 7 días a la semana.
      </li>
      <li>
        Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación. El pago de la mensualidad, se facturará los primeros 5 días de cada mes.
      </li>
      <li>
        El tiempo de espera para la instalación es de 10 días hábiles, una vez aprobada la propuesta.
      </li>
      <li>
        Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
      </li>
      <li>
        La antena es propiedad del cliente y posee 06 meses de garantía directamente con Maraveca.
      </li>
    </ul>
  @endif
<!--asimetricos-->
  @if( $solicitud->planes=='r'||$solicitud->planes=='r2'||$solicitud->planes=='r3'||$solicitud->planes=='c' ||$solicitud->planes=='c2'||$solicitud->planes=='c3')
    <ul>
      @if($solicitud->planes=='c' ||$solicitud->planes=='c2'||$solicitud->planes=='c3')
      <li>
        Los planes de Internet Asimétrico Comercial la velocidad no es garantizada, puede presentar ciertas fluctuaciones durante el día.
      </li>
      @endif
        @if($solicitud->planes=='r'||$solicitud->planes=='r2'||$solicitud->planes=='r3')
          <li>
            Los planes de Internet Asimétrico Residencial la velocidad no es garantizada, puede presentar ciertas fluctuaciones durante el día.
          </li>
        @endif
      <li>
        Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación. El pago de la mensualidad, se facturará los primeros 5 días de cada mes.
      </li>
      <li>
        El tiempo de espera para la instalación es de 10 días hábiles, una vez aprobada la propuesta.
      </li>
      <li>
        Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
      </li>
      <li>
        La antena es propiedad del cliente y posee 06 meses de garantía directamente con Maraveca.
      </li>
    </ul>

  @endif

  <br />
  <br />
  <br />
  <p >
    <h class="blue bold">Nota:</h> El servicio de internet e interconexión  posee las siguientes características:
  </p>
  <br />
  <ul>
    <li>
      Futura ampliación en megas.
    </li>
    <li>
      Alta capacidad para interconexión entre sedes.
    </li>
    @if($solicitud->planes=='d' || $solicitud->planes=='h' || $solicitud->planes=='d2' || $solicitud->planes=='h2' ||$solicitud->planes=='d3' || $solicitud->planes=='h3')
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
    @if($solicitud->planes=='d' || $solicitud->planes=='h' || $solicitud->planes=='d2' || $solicitud->planes=='h2' ||$solicitud->planes=='d3' || $solicitud->planes=='h3')
      <li>
        El ancho de banda es 100% garantizado.
      </li>
    @endif
    <li>
      Contamos con respaldos eléctricos en todas nuestras celdas.
    </li>
  </ul>
</div>
  <div class="page-break"></div>
  <div id="fourpage">
    <br />
    <br />
    <br />
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
      Instalación y configuración de redes VPN.
    </li>
    <li>
      Redes de voz, datos y video sobre IP.
    </li>
    <li>
      Servicio de radio comunicaciones UHF y VHF.
    </li>
    <li>
      Venta, instalación de torres para Telecomunicaciones.
    </li>
    <li>
      Venta, instalación de centrales telefónicas (IP, HÍBRIDAS).
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
<p id="fivepage">
{{-- <br />
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
 </ul>--}}
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
<p class="blue bold">Sección Legal:</p>
<p> </p>
<p>Ambas partes se comprometen con el cumplimiento de los acuerdos descritos anteriormente, de forma ética y profesional. Garantizando la confidencialidad de toda la información suministrada por ambas partes.</p>
<p><strong> </strong></p>
<p><strong>Por el cliente:          </strong>                                              </p>
<p> </p>
<p>Nombre: __________________________                  </p>
<p> </p>
<p>Cargo: ____________________________</p>
<p> </p>
<p>Firma: _____________________________</p>
<p> </p>
<p>Fecha:_____________________________</p>

<p><h class="blue bold">Por Maraveca Telecomunicaciones</h><br />

  <img src="{{asset('images/firma_presupuesto_maraveca.png')}}" alt="firma"><br />

<strong>Dra. Ana Michelle Reyes de Velázquez<br />
    Directora Comercial </strong></p>
<img src="{{asset('images/sello_presupuesto_maraveca.jpg')}}" alt="sello">
</div>
</body>
</html>
