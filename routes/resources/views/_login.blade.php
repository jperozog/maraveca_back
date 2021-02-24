<!--form class="form-horizontal" >


    <label for=" Cedula" class="col-md-4 control-label">Cedula/RIF</label>
    <input id="dni" type="text" class="form-control" name="dni" value="{{ old('dni') }}" required autofocus>

    <label for="password" class="col-md-4 control-label">Password</label>
    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required autofocus>

    <button type="submit" class="btn btn-primary">
        Login
    </button>

</form>

<a href="register">
   <button>Registro</button>
</a-->

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
				
				</div>

				<form class="login100-form validate-form" method="POST" action="{{ route('loginprocess') }}">
          {{ csrf_field() }}
          <span class="login100-form-title">
						Sistema Administrativo
					</span>
					<div class="wrap-input100 alert-danger" role= "alert" align="center" style="background-color: #fff;color: ##FF0004;border-color: #fff;">
						@if($errors->any())
						{{$errors->first()}}
						@endif
					</div>
					<div class="wrap-input100 alert-success" role= "alert" align="center" style="background-color: #fff;color: #155724;border-color: #fff;">
						@if(session('success'))
	    			{{session('success')}}
						@endif
					</div>
					<div class="wrap-input100">
				<select class="input100" id="dni" type="select" class="form-control" name="kind" required autofocus>
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

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" id="password" type="password" name="password" required placeholder="Contraseña">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<!-- <div class="wrap-input100 alert-danger" align="center">
						@if($errors->any())
						{{$errors->first()}}
						@endif
					</div> -->

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							Iniciar sesión
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
						<a class="txt2" href="register">
							Crear una cuenta
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>

						<a class="txt2" href="chpassword">
							Olvido su contraseña?
							<!-- <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i> -->
						</a>

					</div>


				</form>
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
