<html>
<head>
  <style>
    .center-justified {
      text-align: justify;
      -moz-text-align-last: center;
      text-align-last: center;
    }
    table.fac, th.fac, td.fac {
      border:1px solid black;
      /*font-size: 16px;*/
    }

    .blue{
      color: rgb(28, 61, 125);
    }
    .right{
      text-align:right;
      margin-right: 120px;
    }
    .left{
      text-align:left;
    }
    .bold{
      font-weight: bold;
    }
    .topb{
      border-bottom: 1px solid black
    }
    .sangria{
      margin-left: 150px;
    }
    .topN{
      margin-top: 80px;
    }
    .topM{
      margin-top: -40px;
    }
    .contenido{
      margin: 0px;
     background-repeat: no-repeat;
      min-width:100%;
      max-width:100%;
      width:100%;
      height:100%;
      min-height:100%;
      max-height:100%;
      background-size: contain;
    background-image: url(<?php echo e(URL::asset('img/presu2.jpeg')); ?>);
    }
    @page{
      /*p { page-break-after: always; }
      p:last-child { page-break-after: never; }*/
    }
    .page-break {
      page-break-after: always;
    }
  </style>
</head>
<body class="contenido">
<div id="firstpage">
  <br />
  <br />
  <br />
  <p class="blue left bold sangria topN">
    Presupuesto N. 
  </p>
  <p class="blue right bold topM">
    Maracaibo,
  </p>
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  
    <p class="blue left bold ">
      Señor(a):
    </p>
 
  <p class="blue bold">
   
  </p>
 
  <br />
  <br />
  
    <p class="center-justified">
      Mediante la presente queremos ofrecerles nuestro servicio de Internet Dedicado Corporativo para comunicación de datos en alta velocidad bajo tecnología Inalámbrica con el propósito de cumplir sus necesidades organizacionales.
    </p>
    <br />
    <p class="center-justified">
      En la misma se presentan las tarifas y condiciones de nuestro servicio para someterlo a su consideración y análisis.
    </p>
    <br />
  
</div>
<div class="page-break"></div>


</body>
</html>
