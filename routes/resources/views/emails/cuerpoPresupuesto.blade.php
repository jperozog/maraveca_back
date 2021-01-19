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
  <b>Hola {{$cliente}}</b><br />
  @endif
<br />
  <p>
  Recibe un cordial saludo de parte de todo el equipo Maraveca Telecomunicaciones.
</p>
<p>
  Adjunto encontraras el presupuesto solicitado, junto con el Brochure de la empresa, para que conozcas un poco más de Nosotros y de nuestros clientes.
</p>
<p>
  Por favor confírmame la recepción de este correo, y en caso de tener alguna duda me puedes contactar enseguida y con mucho gusto te atenderé.
</p>
<br />
<p>
  Seguimos en comunicación y me despido Muy cordialmente.
</p>
<br />
<p>
  Feliz Día…
</p>
<br />
<br />
<br />
<br />
<br />
<br />
<b>
    Maraveca Telecomunicaciones, Tu ventana al Universo…
</b>
<b>
  Números Master:  0261-7725180  /  0268-7755100  /  0269-7755001
</b>
</body>
</html>
