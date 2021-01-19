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
{{--  Reciba un cordial saludo, mediante la presente le notificamos que:--}}
  <br />
  <img src="{{ $message->embed(public_path() . '/images/TARJETA_NAVIDAD_2019.jpg') }}" />
  <br />
  <br />
  <p style="white-space: pre-wrap;">
    {{$mensaje}}
  </p>


  
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
