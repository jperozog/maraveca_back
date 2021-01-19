<!DOCTYPE html>
<html lang="en">
<head>
	<title>Maraveca Telecomunicaciones</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="<?php echo e(asset('images/icons/favicon.ico')); ?>"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css')); ?>">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/animate/animate.css')); ?>">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/css-hamburgers/hamburgers.min.css')); ?>">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/select2/select2.min.css')); ?>">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/util.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/main.css')); ?>">
<!--===============================================================================================-->
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt style="padding-top: 0px; padding-bottom: 4em;">
					<img src="<?php echo e(asset('images/img-01.png')); ?>" alt="IMG">
				</div>
        <div class="login100-form">
<span class="login100-form-title">
						Sistema Administrativo
					</span>
        <div class="text-center alert alert-danger" role="alert">
          <?php echo e($respuesta); ?>

					<br />
					<a class="txt2" href="javascript:history.back()">
						<i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
						Volver a ingresar el correo
					</a>
        </div>

        </div>


			</div>
		</div>
	</div>



<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/jquery/jquery-3.2.1.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/bootstrap/js/popper.js')); ?>"></script>
	<script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/select2/select2.min.js')); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/tilt/tilt.jquery.min.js')); ?>"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('js/main.js')); ?>"></script>

</body>
</html>
