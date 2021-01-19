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
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
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

        <form class="form-horizontal" method="POST" action="{{ route('restablecer') }}">

        <span class="login100-form-title">
          Recuperar<br>Contraseña
        </span>
        <div class="wrap-input100 alert-danger"  align="center">
          @if($errors->any())
          {{$errors->first()}}
          @endif
        </div>
        <div class="wrap-input100">
          @if((strtolower($search->kind)=='g'||strtolower($search->kind)=='j')&&(strtolower($search->social)))
        <p class="blue bold">
        Bienvenidos Señores:<br><span class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;font-size: 20px;color: #2096c5;">{{ucwords(strtolower($search->social))}}</span><br>
        Ingrese su correo electronico:
        </p>
        @else
        <p class="blue bold">
        Bienvenido(a) Señor(a):<br><span class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;font-size: 20px;color: #2096c5;">{{ucwords(strtolower($search->nombre." ".$search->apellido))}}</span><br>
        Ingrese su correo electronico:
        </p>
        @endif
        @if((strtolower($search->kind)=='g'||strtolower($search->kind)=='j')&&(strtolower($search->social)))
        <p class="blue bold">
          Rif:{{ucfirst($search->kind).$search->dni}}
        </p>
        @endif
        </div>

        <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz" style="margin-bottom: 20px !important;">
          <input class="input100" id="email" type="email" class="form-control" name="email" required placeholder="Correo">
          <span class="focus-input100"></span>
          <span class="symbol-input100">
            <i class="fas fa-envelope" aria-hidden="true"></i>
          </span>
        </div>

          <input type="hidden" id="cliente" name="id" value="{{$search->id}}">
          <button type="submit" class="login100-form-btn">
          Restablecer Contraseña
          </button>
           <div class="wrap-input100"  align="center" style="color: #f00;background-color: #fff;border-color: #fff;">
            @if($errors->any())
            {{$errors->first()}}
            @endif
          </div>
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
