<html>
<head>
<style>
  .slogan{
    font-style: italic;
    font-weight: bold;
  }
</style>
</head>
<body>
  @if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null))
  <b>Estimados {{ucwords(strtolower($cliente->social))}}</b><br />
  @else
  <b>Estimado(a) {{ucwords(strtolower($cliente->nombre." ".$cliente->apellido))}}</b><br />
  @endif
<br />
  Reciba un cordial saludo…
  <br />

  Para completar su registro en el portal haga clic en el siguiente link:<a href='http://45.190.168.9:81//maraveca/public/index.php/registro/{{$cliente->id}}{{$cliente->password}}O'> Click Aqui!! </a>
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
