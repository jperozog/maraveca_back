<html>
<head>
  <link rel="stylesheets" href="{{asset('css/presupuesto.css')}}"/>
  <style>
  .slogan{
    font-style: italic;
    font-weight: bold;
  }
</style>
</head>
<body>
  @if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null))
  <b>Estimados {{$cliente}}</b><br />
  @else
  <b>Estimado(a) {{$cliente}}</b><br />
  @endif
  <br />
  Reciba un cordial saludo, mediante la presente le notificamos que:
  <br />
  <br />
  <br />
  @if($observaciones->option==2)
  Estimado cliente la referencia suministrada en relación al pago, esta errada o no ha sido reflejada en nuestro estado de cuenta, por favor solicitamos su colaboración enviado el capture de la misma o en su defecto los datos de forma correcta y completa:
    <br />
    <br />
    <br />
    <ul>
    <li>
    Banco Emisor de Pago
    </li>
    <li>
    Cedula de Titular de la Cuenta
    </li>
    <li>
    Banco Receptor
    </li>
    <li>
    Referencia
    </li>
    <li>
    Fecha de Transacción
    </li>
    <li>
    Monto
    </li>
    </ul>
    <br />
    <br />
    <br />
    En espera de su respuesta
  @endif
  @if($observaciones->option==3)
  Estimado cliente la cuenta a la que realizo el pago, esta errada o no ha sido reflejada en nuestro estado de cuenta, por favor solicitamos su colaboración enviado el capture de la misma o en su defecto los datos de forma correcta y completa:
      <br />
      <br />
      <br />
      <ul>
      <li>
      Banco Emisor de Pago
      </li>
      <li>
      Cedula de Titular de la Cuenta
      </li>
      <li>
      Banco Receptor
      </li>
      <li>
      Referencia
      </li>
      <li>
      Fecha de Transacción
      </li>
      <li>
      Monto

      </li>
      </ul>
      <br />
      <br />
      <br />
      En espera de su respuesta

  @endif
  @if($observaciones->option==4)
  Estimado cliente debido a que los datos están incompletos, no se ha podido verificar el pago que nos ha suministrado, por favor solicitamos su colaboración enviado el capture de la misma o en su defecto los datos de forma correcta y completa:
    <br />
    <br />
    <br />
    <ul>
    <li>
    Banco Emisor de Pago

    </li>
    <li>
    Cedula de Titular de la Cuenta

    </li>
    <li>
    Banco Receptor

    </li>
    <li>
    Referencia

    </li>
    <li>
    Fecha de Transacción

    </li>
    <li>
    Monto

    </li>
    </ul>
    <br />
    <br />
    <br />
    En espera de su respuesta
  @endif
  @if($observaciones->option==1)

  <p>
    {{$mensaje}}
  </p>

  @endif

  @if(isset($observaciones) && isset($observaciones->obs) && strlen($observaciones->obs)>5)
  <p style="white-space: pre-wrap;">
    {{$observaciones->obs}}
  </p>
  @endif
  <br />
  <br />
  <br />

  Esperamos que siga disfrutando de nuestros servicios.
  <br />
  <br />

  <span class="slogan">Maraveca Telecomunicaciones, Tu ventana al Universo…</span>
  <br />
  <br />

  Por favor no responda a este correo. Esta dirección es automática e impersonal por lo que no se le podrá contar con ningún tipo de ayuda o solicitud. En caso que requiera contactarnos puede hacerlo llamando al 0261-7725180 o visitarnos en nuestra página www.maraveca.com
</body>
</html>
