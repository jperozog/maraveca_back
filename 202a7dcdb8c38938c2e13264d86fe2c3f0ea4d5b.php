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
  <?php if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)): ?>
  <b>Estimados <?php echo e(ucwords(strtolower($cliente->social))); ?></b><br />
  <?php else: ?>
  <b>Estimado(a) <?php echo e(ucwords(strtolower($cliente->nombre." ".$cliente->apellido))); ?></b><br />
  <?php endif; ?>
<br />
  Su pago ha sido recibido, actualmente se encuentra en cola para ser verificado, este proceso no demora mas de 72Hrs, una vez que sea verificado será cargado a su estado de cuenta.<br/>

  Una vez sea verificado el pago o exista alguna inconsistencia con la informacion del mismo se le estará haciendo saber por esta via y por SMS<br />
  <br />
  <span class="slogan">Le invitamos a revisar su estado de cuentra a traves de nuestra pagina web, en la seccion MI VENTANA.</span> <br />
  <br />
  Agradecemos que sea parte de esta Gran Familia!<br />
  <br />
  <span class="slogan">Feliz Dia.</span>
  <br />
  <br />
  <span class="slogan">Maraveca Telecomunicaciones, Tu ventana al Universo…</span>

  <br />
  <br />
  Por favor no responda a este correo. Esta dirección es automática e impersonal por lo que no se le podrá contar con ningún tipo de ayuda o solicitud. En caso que requiera contactarnos puede hacerlo llamando al 0261-7725180 o visitarnos en nuestra página www.maraveca.com
</body>
</html>
