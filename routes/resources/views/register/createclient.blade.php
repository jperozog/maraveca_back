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
				<div class="login100-pic js-tilt" data-tilt style="padding-top: 0px; padding-bottom: 4em;">
					<img src="{{asset('images/img-01.png')}}" alt="IMG">
				</div>
        <div class="login100-form">
  @if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)))
  <p class="blue bold" style="margin-bottom: 10px;">
          Bienvenidos Señores:<br><span class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;font-size: 20px;color: #2096c5;">{{ucwords(strtolower($cliente->social))}}</span><br>
  Ingrese su nueva contraseña:
  </p>
  @else
  <p class="blue bold" style="margin-bottom: 10px;">
          Bienvenido(a) Señor(a):<br><span class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;font-size: 20px;color: #2096c5;">
   {{ucwords(strtolower($cliente->nombre." ".$cliente->apellido))}}</span>
  </p>
  @endif
  @if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)))
  <p class="blue bold" style="margin-bottom: 10px;">
          Rif:<br><span class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;font-size: 20px;color: #2096c5;">
    {{ucfirst($cliente->kind).$cliente->dni}}</span>
  </p>
  @endif

  <form class="form-horizontal" method="POST" action="{{ route('changepassword') }}">
   <div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" id="password" type="password" name="password" required placeholder="Contraseña">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
	<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" id="cpassword" type="password" name="cpassword" required placeholder="Confirmar Contraseña">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
    <label for=" Password" class="col-md-4 control-label" style="display: none;">Password</label>
    <label for=" Cpassword" class="col-md-4 control-label" style="display: none;">Confirmar Password</label>
    <input type="hidden" id="id" name="id" value="{{$id}}">
    <button type="submit" class="login100-form-btn">
    Registrarse
    </button>

  </form>

        </div>

        </div>


			</div>
		</div>
	</div>



<!--===============================================================================================-->
	<script src="{{asset('vendor/jquery/jquery-3.2.1.min.js')}}"></script>
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