<html>
<head>
<link rel="stylesheets" href="<?php echo e(asset('css/presupuesto.css')); ?>"/>
<style>
  .slogan{
    font-style: italic;
    font-weight: bold;
  }
</style>
</head>
<body>
  <?php if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)): ?>
  <b>Estimados <?php echo e($cliente); ?></b><br />
  <?php else: ?>
  <b>Estimado(a) <?php echo e($cliente); ?></b><br />
  <?php endif; ?>
<br />
  Reciba un cordial saludo…
  <br />

  Adjunto enviamos el soporte de su recibo de pago generado el <?php echo e($fecha); ?>

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
