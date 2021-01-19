<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
  <style>

          body{
            font-family: 'Roboto', sans-serif;
          }
          .segundo{
            margin-top:2%;
            margin-bottom:2%;
          }
          .primero{
            margin-bottom:2%;
            margin-top:2%
          }
          .saludo{
            font-size:20px;
          }
          .cliente{
            font-size:40px;
          }
          .mes{
            color:#2B7DC6;
          }
          .precio{
            color:#fff;
            font-size:50px;
          }
          .plan{
            color:#fff;
            font-size:40px;
          }
          .plannombre{
            background-Color:#2B7DC6;
            padding:5%;
            text-align:center;
          }
          .planmonto{
            background-Color:#2B7DC6;
            padding:5%;
            text-align:center;
          }
  </style>
</head>

<body>
<div class="container">
  <div class="row">
    <div class="col-sm-8"><a href="https://maraveca.com/mi-ventana/"><img src="{{ $message->embed(public_path() . '/images/img.jpeg') }}" /></a></div>
  </div>
  <div class="row primero">
    <div class="col-sm-1"></div>
    <div class="col-sm-9"><h1 class="cliente">Estimado, {{$cliente}}</h1></div>
    <div class="col-sm2"></div>
  </div>
  <div class="row segundo">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
    <p class="saludo">
    Porque nos gusta estar siempre comunicados contigo, cumplimos con informarte que se ha generado un nueva factura.
    Con las siguientes caracteristicas: 
    </p> 
    
    </div>
    <div class="col-sm1"></div>
  </div>
  <div class="row">
    <div class="col-sm-10">
    <div
    class="plannombre">
    <h1 class="plan">{{$plan}}     {{$precio}} $</h1>
    </div>
    </div>
    <!--
    <div class="col-sm-2">
    <div
    class="planmonto">
    <h1 class="precio">{{$precio}} $</h1>
    </div>
    -->
    </div>
    <div class="col-sm2"></div>
  </div>
  <div class="row segundo">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
    <p class="saludo">Puedes reportar tu pago a traves del siguiente <a href="https://maraveca.com/mi-ventana/">LINK</a>
     </p>
     </div>
    <div class="col-sm-1"> </div>
  </div>

  <div class="row primero">
    <div class="col-sm-1"></div>
    <div class="col-sm-9"><h1 class="cliente">¡Somos tu Ventana al Universo!</h1></div>
    <div class="col-sm2"></div>
  </div>
  <div class="row">
    <div class="col-sm-8"><img src="{{ $message->embed(public_path() . '/images/footer.png')}}" /></div>
  </div>
</div>

 
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>