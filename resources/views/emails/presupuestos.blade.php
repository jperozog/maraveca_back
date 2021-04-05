<html>
<head>
  <style>

 
    .center-justified {
      text-align: justify;
      -moz-text-align-last: center;
      text-align-last: center;
     

    }

    .margenes{
      margin-left: 40px;
      margin-right: 40px
    }

    table.fac, th.fac, td.fac {
      border:1px solid black;
      justify-content:center;
      align-items:center;
      text-align:center;
      min-height: 30px
      /*font-size: 16px;*/
    }

    table.fac2, th.fac2, td.fac2 {
      border:1px solid black;
      justify-content:center;
      align-items:center;
      background-color:#57AADD;
      text-align:center
      /*font-size: 16px;*/
    }

    ul{
      list-style:none
    }
    ul li{
      text-decoration:none;
    }

    .blue{
      color: rgb(28, 61, 125);
    }
    .blue2{
      color: rgb(74, 119, 205);
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
    <p class="blue margenes bold">
      Señores:
    </p>
  @else
    <p class="blue margenes bold">
      Señor(a):
    </p>
  @endif
  <p class="blue margenes bold">
    {{$cliente}}
  </p>
  @if((strtolower($detalle->kind)=='g'||strtolower($detalle->kind)=='j')&&(strtolower($detalle->social)))
    <p class="blue margenes bold">
      Rif:{{ucfirst($detalle->kind).$detalle->dni}}
    </p>
  @endif
  <br />
  <br />
  @if($solicitud->servicio== 5)
    <p class="center-justified margenes blue">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Dedicado Corporativo para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified margenes blue">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
  @if($solicitud->servicio == 4)
    <p class="center-justified margenes blue">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Asimétrico Comercial para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified margenes blue">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
  @if($solicitud->servicio== 3)
    <p class="center-justified margenes blue">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Asimétrico Residencial para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades residenciales.
    </p>
    <br />
    <p class="center-justified margenes blue">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
  @if($solicitud->servicio== 1)
    <p class="center-justified margenes blue">
    Mediante la presente queremos ofrecerles nuestro servicio de Internet Residencial por fibra óptica tecnología usada para transmitir información en forma de pulsos de luz mediante hilos de fibra de vidrio o plástico, a través de largas distancia con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified margenes blue">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  @endif
  @if($solicitud->servicio== 2)
    <p class="center-justified margenes blue">
    Mediante la presente queremos ofrecerles nuestro servicio de Internet Comercial por fibra óptica tecnología usada para transmitir información en forma de pulsos de luz mediante hilos de fibra de vidrio o plástico, a través de largas distancia con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified margenes blue">
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
  <br>
  <p class="blue bold blue">
   Datos Financieros
  </p>
  <p class="blue bold blue">
    Servicio de Internet:
  </p>
  <table style="width:100%;">
    <tr>
      <td class="blue bold fac2">
        Instalacion del Servicio - Configuracion
      </td>
      @foreach($planes as $plan)
        <td class="blue bold fac2">
          {{$plan->name_plan}}
        </td>
      @endforeach
      
    </tr>
    <tr>
      <td class=" blue bold fac">
        {{$solicitud->costo}}{{$solicitud->moneda}}
      </td>
      @foreach($planes as $plan)
        <td class=" blue bold fac">
          {{$plan->taza}}US$.
      </td>
    @endforeach
    </tr>
  </table>
  @if($solicitud->servicio== 5)
  <p class="bold blue">
  El servicio Dedicado se entrega con una IP Publica.
  </p>
  @endif
  @if($solicitud->servicio== 5 || $solicitud->servicio== 4 || $solicitud->servicio== 3)
  <p class="bold blue">
    No se incluye torre: Tubo 6 metros
  </p>
  @endif
  <p class="bold blue">
    Los precios no incluyen IVA.
  </p>
  <br>
  <p class="blue">
    <b class="blue bold">Facturación y forma de pago:</b> El pago se realizará a través de un depósito bancario, transferencia electrónica o efectivo en nuestras oficinas a nombre de Maraveca Telecomunicaciones, C.A.
  </p>

    <ul>
      <li>
       <p class="blue">- Se cancela el 100% de la instalación.</p>
      </li>
      <li>
      <p class="blue">- El pago del servicio se realizará bajo facturación mensual, el servicio se cancela por adelantado, por lo que deberá cancelar los primeros CINCO (05) días de cada mes, de lo contrario, el servicio será desconectado el día 15.</p>
      </li>
      <li>
      <p class="blue">- Los precios no incluyen el Impuesto al Valor Agregado (IVA.). El mismo será cobrado al momento de su facturación. </p>
      </li>
      <li>
      <p class="blue">- El tiempo de entrega e instalación por punto, una vez confirmada la factibilidad del servicio y la firma del contrato es de 10 días hábiles en la ciudad y en zonas rurales.</p>
      </li>
      <li>
      <p class="blue">- Los precios ofertados están calculados sobre la base de los aranceles vigentes y sobre las tasas e impuestos de importación vigentes. Cualquier cambio en dichas tasas e impuestos y cualquier impuesto nuevo adicional o cambios del valor de la mano de obra modificará los precios ofertados sin previo aviso.</p>
      </li>
      <li>
      <p class="blue">- Esta oferta tiene una duración de 5 días calendario.</p>
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
  @if($solicitud->servicio == 5 )
    <ul>
      <li>
      <p class="blue">
        - La velocidad ofrecida para el servicio Dedicado Corporativo está garantizada 100% las 24 horas.
      </p>
      </li>
      <li>
      <p class="blue">
        - La conexión dedicada a internet permite a nuestros clientes el acceso a todos los recursos y aplicaciones de la red las 24 horas del día, los 7 días a la semana.
        </p>
      </li>
      <li>
      <p class="blue">
        - Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación. El pago de la mensualidad, se facturará los primeros 5 días de cada mes.
        </p>
      </li>
      <li>
      <p class="blue">
        - El tiempo de espera para la instalación es de 10 días hábiles, una vez aprobada la propuesta.
        </p>
      </li>
      <li>
      <p class="blue">
        - Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
        </p>
      </li>
      <li>
      <p class="blue">
        - La antena es propiedad del cliente y posee 06 meses de garantía directamente con Maraveca.
        </p>
      </li>
    </ul>
  @endif
<!--asimetricos-->
  @if( $solicitud->servicio == 3 || $solicitud->servicio == 4 )
    <ul>
      @if($solicitud->servicio == 4)
      <li>
      <p class="blue">
        - Los planes de Internet Asimétrico Comercial la velocidad no es garantizada, puede presentar ciertas fluctuaciones durante el día.
        </p>
      </li>
      @endif
        @if($solicitud->servicio == 3)
          <li>
          <p class="blue">
          	- Los planes de Internet Asimétrico Residencial la velocidad se garantiza en un 75%, puede presentar ciertas fluctuaciones durante el día. 
          </p>
          </li>
        @endif
      <li>
      <p class="blue">
        - Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación. El pago de la mensualidad, se facturará los primeros 5 días de cada mes.
        </p>
      </li>
      <li>
      <p class="blue">
        - El tiempo de espera para la instalación es de 10 días hábiles, una vez aprobada la propuesta.
        </p>
      </li>
      <li>
      <p class="blue">
        - Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
        </p>
      </li>
      <li>
      <p class="blue">
        - La antena es propiedad del cliente y posee 06 meses de garantía directamente con Maraveca.
        </p>
      </li>
    </ul>

  @endif

  <!-- Fibra Optica -->
  @if($solicitud->servicio == 1 || $solicitud->servicio == 2 )
    <ul>
      <li>
      <p class="blue">
      	- La velocidad ofrecida para el servicio Residencial por Fibra Óptica Se garantiza de un 80% a un 96% de la velocidad contratada, con un pin de 70 a 100 está garantizada las 24 horas. 
        </p>
      </li>
      <li>
      <p class="blue">
      	- La conexión a internet por fibra óptica permite a nuestros clientes el acceso a todos los recursos y aplicaciones de la red las 24 horas del día, los 7 días a la semana.
        </p>
      </li>
      <li>
      <p class="blue">
       - Con la aprobación de la oferta cancelará el monto correspondiente al pago de instalación. El pago de la mensualidad, se facturará los primeros 5 días de cada mes. 
       </p>
      </li>
      <li>
      <p class="blue">
       - El tiempo de espera para la instalación es de 10 días hábiles, una vez aprobada la propuesta. 
       </p>
      </li>
      <li>
      <p class="blue">
      	- Este es un presupuesto base, puede haber modificaciones después de realizar factibilidad de enlace o visita técnica.
        </p>
      </li>
      <li>
      <p class="blue">
      - La ONU (Unidad de Red Óptica) es propiedad del cliente y posee 01 mes de garantía directamente con Maraveca por algún desperfecto de fábrica.
      </p>
      </li>
    </ul>
  @endif

  <br />
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
    <br />
    <br />
    <br />
  <p class="blue" >
    <h class="blue bold">Nota:</h> El SERVICIO DE INTERNET E INTERCONEXION  posee las siguientes características:
  </p>
  @if($solicitud->servicio == 5 || $solicitud->servicio == 4 || $solicitud->servicio == 3)
  <ul>
    <li>
    <p class="blue">
      - Futura ampliación en megas.
      </p>
    </li>
    <li>
    <p class="blue">
      - Alta capacidad para interconexión entre sedes.
      </p>
    </li>
    @if($solicitud->servicio == 5)
      <li>
      <p class="blue">
        - Conexión dedicada las 24 horas del día.
        </p>
      </li>
    @endif
    <li>
    <p class="blue">
      - Tarifa plana mensual.
      </p>
    </li>
    <li>
    <p class="blue">
      - Conexión Ilimitada.
      </p>
    </li>
    @if($solicitud->servicio == 5)
      <li>
      <p class="blue">
        - El ancho de banda es 100% garantizado.
        </p>
      </li>
    @endif
    <li>
    <p class="blue">
      - Contamos con respaldos eléctricos en todas nuestras celdas.
      </p>
    </li>
    <li>
    <p class="blue">
    	- Soporte técnico de lunes a viernes hasta las 9:00 Pm y Fines de semana hasta las 4:00Pm
      </p>
    </li>
  </ul>
  @endif


  @if($solicitud->servicio == 1 || $solicitud->servicio == 2)
  <ul>
    <li>
    <p class="blue">
      - Futura ampliación en megas.
      </p>
    </li>
    <li>
    <p class="blue">
    	- Transmisión de datos de baja latencia.
      </p>
    </li>
      <li>
      <p class="blue">
      - Mejor calidad en imagen y sonido.
        </p>
      </li>
      <li>
    <p class="blue">
    	- Capacidad para interconexión entre sedes.
      </p>
    </li>
    
    <li>
    <p class="blue">
      - Tarifa plana mensual.
      </p>
    </li>
    <li>
    <p class="blue">
      - Conexión Ilimitada.
      </p>
    </li>
      <li>
      <p class="blue">
        - El ancho de banda es 100% garantizado.
        </p>
      </li>
    <li>
    <p class="blue">
      - Contamos con respaldos eléctricos en todas nuestras celdas.
      </p>
    </li>
    <li>
    <p class="blue">
    	- Soporte técnico de lunes a viernes hasta las 9:00 Pm y Fines de semana hasta las 4:00Pm
      </p>
    </li>
  </ul>
  @endif
  </div>
  <div class="page-break"></div>
  <div id="fivepage">
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
    <br />
    <br />
    <br />
  <p class="blue">
    A continuación, se presentará algunos de los servicios adicionales que ofrece <h class="blue bold">Maraveca Telecomunicaciones</h>, a fin cumplir con las necesidades organizacionales y tecnológicas de nuestros clientes:
  </p>
  <ul>
    <li>
    <p class="blue">
      - Servicio de interconexión.
      </p>
    </li>
    <li>
    <p class="blue">
      - Instalación y configuración de redes VPN.
      </p>
    </li>
    <li>
    <p class="blue">
      - Redes de voz, datos y video sobre IP.
      </p>
    </li>
    <li>
    <p class="blue">
      - Servicio de radio comunicaciones UHF y VHF.
      </p>
    </li>
    <li>
    <p class="blue">
      - Venta, instalación de torres para Telecomunicaciones.
      </p>
    </li>
    <li>
    <p class="blue">
      - Venta, instalación de centrales telefónicas (IP, HÍBRIDAS).
      </p>
    </li>
    <li>
    <p class="blue">
      - Optimizaciones físicas en torres de telecomunicaciones.
      </p>
    </li>
    <li>
    <p class="blue">
      - Drive test y post-procesamiento de datos.
      </p>
    </li>
  </ul>


</div>
<div class="page-break"></div>
<p id="sixpage">

  <br />
  <br />
  <br />
  <br />
    <br />
    <br />
    <br />
    <br />
  
<p class="blue bold">Sección Legal:</p>
<p> </p>
<p  class="blue">Ambas partes se comprometen con el cumplimiento de los acuerdos descritos anteriormente, de forma ética y profesional. Garantizando la confidencialidad de toda la información suministrada por ambas partes.</p>
<p><strong> </strong></p>
<p class="blue bold"><strong>Por el cliente:          </strong>                                              </p>
<p> </p>
<p class="blue ">Nombre: __________________________                  </p>
<p> </p>
<p class="blue ">Cargo: ____________________________</p>
<p> </p>
<p class="blue ">Firma: _____________________________</p>
<p> </p>
<p class="blue ">Fecha:_____________________________</p>

<p><h class="blue bold">Por Maraveca Telecomunicaciones</h><br />

  <img src="{{asset('images/firma_presupuesto_maraveca.png')}}" alt="firma"><br />

<strong class="blue bold">Dra. Ana Michelle Reyes de Velázquez<br />
    Directora Comercial </strong></p>
<img src="{{asset('images/sello_presupuesto_maraveca.jpg')}}" alt="sello">
</div>
</body>
</html>
