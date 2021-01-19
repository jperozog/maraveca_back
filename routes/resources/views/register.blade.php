<!DOCTYPE html>
<html lang="en">
<head>
	<title>Maraveca Telecomunicaciones</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{asset('images/icons/favicon.ico')}}"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/animate/animate.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/main.css')}}">
<!--===============================================================================================-->
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="{{asset('images/img-01.png')}}" alt="IMG">
				</div>

				<form class="login100-form validate-form" method="POST" action="{{ route('register1') }}">
          {{ csrf_field() }}
          <span class="login100-form-title">
						Sistema Administrativo
					</span>
					<div class="wrap-input100" align="center" style="color: #f00;background-color: #fff;border-color: #fff;">
						@if($errors->any())
						{{$errors->first()}}
						@endif
					</div>
          <p class="text-center">
            Registro de Clientes
          </p>
					<div class="wrap-input100">
						<select class="input100" id="dni" type="select" class="form-control" name="kind" value="{{ old('kind') }}" required autofocus>
        <option value="V" selected="selected">Venezolano</option>
        <option value="E">Extranjero</option>
        <option value="J">Juridico</option>
        <option value="G">Gubernamental</option>
    				</select>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" id="dni" type="text" class="form-control" name="dni" required placeholder="Cedula o rif">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-id-card" aria-hidden="true"></i>
						</span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							Buscar Cliente
						</button>
					</div>


					<!--div class="text-center p-t-12">
						<span class="txt1">
							olvidaste
						</span>
						<a class="txt2" href="#">
							usuario / contraseña?
						</a>
					</div-->

					<div class="text-center p-t-100">
						<a class="txt2" href="login">
              <i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
							Volver al inicio
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>




<!--===============================================================================================-->
	<script src="{{asset('vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
    <script src="{{asset('vendor/jquerymask/jquery.mask.min.js')}}"></script>
<!--===============================================================================================-->
    
	<script src="{{asset('vendor/bootstrap/js/popper.js')}}"></script>
	<script src="{{asset('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('vendor/tilt/tilt.jquery.min.js')}}"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="{{asset('js/main.js')}}"></script>

</body>
</html>
