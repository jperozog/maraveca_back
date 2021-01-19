<!DOCTYPE html>
<html lang="en">
<head>
	<title>Maraveca Telecomunicaciones</title>
	<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{asset('images/icons/favicon.ico')}}"/>
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}">
	<!--===============================================================================================-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
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
	<link rel="stylesheet" type="text/css" href="{{asset('css/normalize.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{asset('css/demo.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{asset('css/tabs.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{asset('css/tabstyles.css')}}" />
  		<script src="{{asset('js/modernizr.custom.js')}}"></script>
	<style>
	#exTab3 .nav-pills > li > a {
	  border-radius: 4px 4px 0 0 ;
	}
	</style>
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-content100">
				<svg class="hidden">
			<defs>
				<path id="tabshape" d="M80,60C34,53.5,64.417,0,0,0v60H80z"/>
			</defs>
		</svg>
			<section style="width: 100%;">
				<div class="tabs tabs-style-shape">
					<nav>
						<ul>
							<li>
								<a href="#tab1">
									<svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
									<span style="font-family: Montserrat-Bold;">Informacion de Contacto</span>
								</a>
							</li>
							<li>
								<a href="#tab2">
									<svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
									<svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
									<span style="font-family: Montserrat-Bold;">Tus Facturas</span>
								</a>
							</li>
							<li>
							  <a href="#tab3">
									<svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
							  <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
								  <span style="font-family: Montserrat-Bold;">Movimientos</span>
							  </a>
							</li>
							<li>
								<a href="#tab4">
									<svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
									<span style="font-family: Montserrat-Bold;">Reportar Pagos</span>
								</a>
							</li>
						</ul>
					</nav>
					<div class="content-wrap">
						<section id="tab1"><div class="columns-l">@if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)))
								<div class="blue" style="text-align: left !important;">
								<span class="font-weight-bold" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
									{{ucwords(strtolower($cliente->social))}}
								</div>
								@else
								<div class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
									{{ucwords(strtolower($cliente->nombre." ".$cliente->apellido))}}
								</div>
								<p class="blue">
									<span class="font-weight-bold">Cedula:</span><span style="color: #74777b;"> {{ucfirst($cliente->kind." ".$cliente->dni)}}</span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Correo:</span> <span style="color: #74777b;">{{ucfirst($cliente->email)}}</span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Telefono:</span> <span style="color: #74777b;">{{ucfirst($cliente->phone1)}}</span>
								</p>
								@if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2))
								<p class="blue">
									<span class="font-weight-bold">Telefono 2:</span> <span style="color: #74777b;">{{ucfirst($cliente->phone2)}}</span>
								</p>
								@endif
								<p class="blue">
									<span class="font-weight-bold">Direccion:</span> <span style="color: #74777b;">{{ucfirst(strtolower($cliente->direccion))}}</span>
								</p>
								@endif
								@if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)))
								<p class="blue">
									<span class="font-weight-bold">Rif:</span> <span style="color: #74777b;">{{ucfirst($cliente->kind).$cliente->dni}}</span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Persona de contacto:</span> <span style="color: #74777b;">{{ucfirst($cliente->nombre." ".$cliente->apellido)}}</span>
									<span class="font-weight-bold">Telefono:</span> <span style="color: #74777b;">{{ucfirst($cliente->phone1)}}</span>
								</p>
								@if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2))
								<p class="blue">
									<span class="font-weight-bold">Telefono 2:</span> <span style="color: #74777b;">{{ucfirst($cliente->phone2)}}</span>
								</p>
								@endif
								<p class="blue">
									<span class="font-weight-bold">Correo:</span> <span style="color: #74777b;">{{ucfirst($cliente->email)}}</span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Direccion:</span> <span style="color: #74777b;">{{ucfirst($cliente->direccion)}}</span>
								</p>
					  @endif</div>

					  <div class="columns-r">
					  <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">

  <!-- Accordion card -->
  <div class="card">

    <!-- Card header -->
    <div class="card-header" role="tab" id="headingOne1">
      <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne1" aria-expanded="true"
        aria-controls="collapseOne1">
        <h5 class="mb-0" style="font-size: 14px;">Tus Planes <i class="fa fa-angle-down rotate-icon"></i>
        </h5>
      </a>
    </div>

    <!-- Card body -->
    <div id="collapseOne1" class="collapse" role="tabpanel" aria-labelledby="headingOne1" data-parent="#accordionEx">
      <div class="card-body">
        <div class="table-responsive">
									<table class="table table-bordered">						@foreach($servicios as $plan)
	<tr>
		<td class="tituloplanes" colspan="3">
		   <h6>
			{{$plan->name_plan}}
		   </h6>
		</td>
	</tr>
	<tr>
		<td class="costoplanes" colspan="2">
		  <h6>
			{{number_format($plan->cost_plan)." "}}Bs.S.
		  </h6>
		</td>
		@if($plan->comment_srv != "" && $plan->comment_srv != null)
		<td class="lugarplanes" colspan="2">
		  <h6>
			{{$plan->comment_srv}}
		  </h6>
		</td>
		@endif
	</tr>
	@endforeach
</table>
								</div>
								<p class="text text-center">
El monto total de su renta basica y/o servicios es de: <a class="font-weight-bold">{{number_format($total_mensual)." "}}Bs.S.</a>
								</p>
      </div>
    </div>

  </div>
  <!-- Accordion card -->

  <!-- Accordion card -->
  <div class="card">

    <!-- Card header -->
    <div class="card-header" role="tab" id="headingTwo2">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx" href="#collapseTwo2"
        aria-expanded="false" aria-controls="collapseTwo2">
        <h5 class="mb-0" style="font-size: 14px;">Tickets <i class="fa fa-angle-down rotate-icon"></i>
        </h5>
      </a>
    </div>

    <!-- Card body -->
    <div id="collapseTwo2" class="collapse" role="tabpanel" aria-labelledby="headingTwo2" data-parent="#accordionEx">
      <div class="card-body">
        			@if(count($soportes)>0)
						<div class="table-responsive">
						    <table class="table table-sm">
  <thead style="font-size: 15px;color: #2096c5;">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Estado</th>
      <th scope="col">Motivo</th>

      <th scope="col">Apertura</th><th scope="col">Cierre</th>
    </tr>
  </thead>

  <tbody style="font-size: 15px;">
   @foreach($soportes as $ticket)
    <tr>
      <th scope="row" style="color: #2096c5;">{{$ticket->id_soporte}}</th>
      <td>Cerrado</td>
      <td>{{ucwords($ticket->problems)}}</td>
      <td>{{date('d/m/Y', strtotime($ticket->created_at))}}</td>
      <td>{{date('d/m/Y', strtotime($ticket->updated_at))}}</td>
    </tr>
    @endforeach
  </tbody>
</table>

						</div>
						@else
						<div class="text text-center">
							No posee tickets que mostrar
						</div>
						@endif
      </div>

    </div>

  </div>
  <!-- Accordion card -->
	<div class="wrap-input100 alert-success" role= "alert" align="center">
		@if(session('success'))
		{{session('success')}}
		@endif
	</div>
  </div>
  <!-- Accordion card --></div>
						</section>
						<section id="tab2">
								<table class="table table-sm">
  <thead style="font-size: 15px;color: #2096c5;">
    <tr>
      <th scope="col">Numero</th>
      <th scope="col">Monto</th>
      <th scope="col">Pagado</th>
      <th scope="col">Balance</th>
      <th scope="col">Fecha</th>
      <th scope="col">Documento</th>
    </tr>
  </thead>
  <tbody style="font-size: 15px;">
   @foreach($facturacion as $factura)
    <tr>
     @if($factura->fac_num!=null)
      <th scope="row" style="color: #2096c5;">{{$factura->fac_num}}</th>
      @else
      <th scope="row" style="color: #2096c5;">{{$factura->id}}</th>
      @endif
      <td>{{number_format($factura->monto,2)." "}}Bs.S.</td>
      <td>{{number_format($factura->pagado,2)." "}}Bs.S.</td>
      <td>{{number_format($factura->bal,2)}}</td>
      <td>{{date('d/m/Y', strtotime($factura->created_at))}}</td>
	  <td><form target="_blank" class="form-horizontal" method="POST" action="{{ route('clientesoverfac') }}">
		  <input type="hidden" id="id" name="id" value="{{$factura->id}}">
		  <button class="login100-form-btn" type="submit" style="padding-left: 20px;padding-right: 20px;width: auto;">
			<i class="fas fa-download"></i>
		  </button>
		  </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<div style="width: 100%;text-align: center;margin-bottom: 10px;">
										@if(round($balanceo,2) != 0)
										<span style="color:red">Su deuda total es de:{{number_format($balanceo, 2)." "}} Bs.S.</span>
										@elseif(round($balanceo,2) == 0 && round($afavor,2) > 0)
										<span style="color:green">Usted tiene un saldo a favor de: {{number_format($afavor)." "}} Bs.S.</span>
										@elseif(round($balanceo,2) == 0 && round($afavor,2) == 0)
										<span style="color:green">Usted se encuentra solvente</span>
										@endif
									</div>

						@if($cliente->serie==0)
						<div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">

  <!-- Accordion card -->
  <div class="card">

    <!-- Card header -->
    <div class="card-header" role="tab" id="headingOne1">
      <a data-toggle="collapse" data-parent="#accordionEx" href="#cuentas1" aria-expanded="true"
        aria-controls="collapseOne1">
        <h5 class="mb-0" style="font-size: 14px;">Cuentas para pagar<i class="fa fa-angle-down rotate-icon"></i>
        </h5>
      </a>
    </div>

    <!-- Card body -->
    <div id="cuentas1" class="collapse" role="tabpanel" aria-labelledby="headingOne1" data-parent="#accordionEx">
      <div class="card-body">
        <div class="table-responsive">
        	@foreach($bancos as $b)
									@if($b['perm'] == '1')
									<div class="card-body">
									<p class="blue">
										<span class="font-weight-bold">Titular:</span> <span style="color: #74777b;">{{ucwords($b['titular'])}}</span> <br />
									</p>
									<p class="blue">
										<span class="font-weight-bold">Identificacion:</span> <span style="color: #74777b;">{{ucwords($b['dni'])}}</span> <br />
									</p>
									<p class="blue">
										<span class="font-weight-bold">Cuenta:</span> <span style="color: #74777b;">{{strtoupper($b['banco']).": ".$b['numero']}}</span>
									</p>
									</div>
									@endif
									@endforeach
        </div>
      </div>
    </div>

  </div>
  </div>
  <!-- Accordion card -->
  @elseif($cliente->serie==1)

<div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">

  <!-- Accordion card -->
  <div class="card">

    <!-- Card header -->
    <div class="card-header" role="tab" id="headingOne1">
      <a data-toggle="collapse" data-parent="#accordionEx" href="#cuentas2" aria-expanded="true"
        aria-controls="collapseOne1">
        <h5 class="mb-0" style="font-size: 14px;">Cuentas para pagar<i class="fa fa-angle-down rotate-icon"></i>
        </h5>
      </a>
    </div>

    <!-- Card body -->
    <div id="cuentas2" class="collapse" role="tabpanel" aria-labelledby="headingOne1" data-parent="#accordionEx">
      <div class="card-body">
        <div class="table-responsive">
        	@foreach($bancos as $b)
									@if($b['perm'] == '2')
									<div class="card-body">
										<p class="blue">
										<span class="font-weight-bold">Titular:</span> <span style="color: #74777b;">{{ucwords($b['titular'])}}</span> <br />
									</p>
									<p class="blue">
										<span class="font-weight-bold">Identificacion:</span> <span style="color: #74777b;">{{ucwords($b['dni'])}}</span> <br />
									</p>
									<p class="blue">
										<span class="font-weight-bold">Cuenta:</span> <span style="color: #74777b;">{{strtoupper($b['banco']).": ".$b['numero']}}</span>
									</p>
									</div>
									@endif
									@endforeach
        </div>
      </div>
    </div>
  </div>
  </div>
  @endif
						<!-- Fin del collapse -->
					</section>
						<section id="tab3">
							<table class="table table-bordered">
@foreach($historial as $historial)
	<tr>
		<td class="tituloplanes" colspan="3">
		   <h6>{{$historial->history}}
		   </h6>
		</td>
	</tr>
	<tr>
		<td class="costoplanes" colspan="2">
		  <h6>{{date('d/m/Y', strtotime($historial->created_at))}}
		  </h6>
		</td>
	</tr>
	@endforeach
</table>
						</section>
						<section id="tab4" class="tab-pane {{ !empty($tab) && $tab == 'tab4' ? 'active' : ''}}">
							@if($cliente->serie == 0)
						<div class="four-tab-l" >
							<form method="POST" action="{{ route('reportarpago') }}">
								{{ csrf_field() }}
								<div class="wrap-input100">
							<select class="input100" id="baltip" type="select" class="form-control" name="baltip" required autofocus>
								<option value="" selected="selected">Banco</option>
								<option value="2">Banesco</option>
								<option value="1">BOD</option>
							</select>
							<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-money-check-alt" aria-hidden="true"></i>
						</span>
								</div>

								<div class="wrap-input100">
							<select class="input100" id="balfrom" type="select" class="form-control" name="balfrom" required autofocus>
								<option value="" selected="selected">Tipo de Transaccion</option>
								<option value="1">Transferencia(Mismo banco)</option>
								<option value="2">Transferencia(Otros bancos)</option>
								<option value="3">Deposito Bancario</option>
							</select>
							<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-money-check" aria-hidden="true"></i>
						</span>
								</div>

								<div class="wrap-input100 validate-input">
									<input class="input100" id="comment" type="comment" class="form-control" name="comment" required placeholder="Referencia" required autofocus>
									<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-check-double" aria-hidden="true"></i>
						</span>
									<!-- <span class="focus-input100"></span> -->
								</div>

								<div class="wrap-input100 validate-input">
									<input class="input100" id="monto" type="number" class="form-control" name="monto" min="0" required placeholder="Monto" required autofocus step="any">
									<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fas fa-money-bill-wave-alt" aria-hidden="true"></i>
						</span>
									<!-- <span class="focus-input100"></span> -->
								</div>

								<div class="wrap-input50 validate-input">
								<label for="Fecha" class="col-md-4 control-label">Fecha</label>
    						<input id="fecha" type="date" class="form-control" name="fecha" required autofocus>
								</div>



								<div class="container-login100-form-btn">
									<button class="login100-form-btn" type="submit">
										Cargar Pago
									</button></div>
									</form>


						</div>
							<div class="four-tab-r">
							<div style="width: 100%; text-align: center;">
								@if(round($balanceo,2) != 0)
								<span style="color:red">Su deuda total es de:{{number_format($balanceo, 2)." "}} Bs.S.</span>
								@elseif(round($balanceo,2) == 0 && round($afavor,2) > 0)
								<span style="color:green">Usted tiene un saldo a favor de: {{number_format($afavor)." "}} Bs.S.</span>
								@elseif(round($balanceo,2) == 0 && round($afavor,2) == 0)
								<span style="color:green">Usted se encuentra solvente</span>
								@endif
							</div>

							<div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true" data-parent="#accordionEx">

  <!-- Accordion card -->
  <div class="card">

    <!-- Card header -->
    <div class="card-header" role="tab" id="headingOne1">
      <a data-toggle="collapse" data-parent="#accordionEx" href="#One1" aria-expanded="true"
        aria-controls="collapseOne1">
        <h5 class="mb-0" style="font-size: 14px;">Pagos por verificación <i class="fa fa-angle-down rotate-icon"></i>
        </h5>
      </a>
    </div>

    <!-- Card body -->
    <div id="One1" class="collapse" role="tabpanel" aria-labelledby="headingOne1" data-parent="#accordionEx">
      <div class="card-body">
        <div class="table-responsive">
        @if(count($pagosp)>0)
									<table class="table table-sm">
									@foreach($pagosp as $pagosp)
									<tr>
												<td colspan="2" style="background-color: #65b7d7;color: #fff;font-family: Montserrat;">
													@if($pagosp->bal_tip == '1')
													 BOD
													@elseif($pagosp->bal_tip == '2')
													 Banesco
													@endif<br>
												 </td>
												</tr>
												<tr>
												  <td style="font-size: 16px;font-weight: bold;color: #96989a;">
													<span style="color: #65b7d7;text-transform: uppercase;">-</span>@if($pagosp->bal_from == '1')
													Mismo banco
													@elseif($pagosp->bal_from == '2')
													Otros bancos
													@elseif($pagosp->bal_from == '3')
													Deposito Bancario
													@endif<span style="color: #65b7d7;text-transform: uppercase;">-</span><br>
													<span style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Ref:</span> {{$pagosp->bal_comment}}<br>
													<span style="font-size: 22px;line-height: 15px;">{{number_format($pagosp->bal_monto,2)." "}}</span><span style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Bs.S.</span><br>
													<span style="color: #65b7d7;text-transform: uppercase;">{{date('d/m/Y', strtotime($pagosp->created_at))}}</span>
												</td>
											</tr>
											@endforeach
</table>
							@else
									No tiene pagos pendientes por aprobacion
									@endif
								</div>
      </div>
    </div>

  </div>
  <!-- Accordion card --></div>

</div>
@elseif($cliente->serie == 1)
<div>

		<span style="font-size: 30px;color: #2096c5">Para reportar su pago por favor enviar el soporte del mismo a administracion@maraveca.com</span>

</div>
@endif
						</section>
		<script src="{{asset('js/cbpFWTabs.js')}}"></script>
		<script>
			(function() {

				[].slice.call( document.querySelectorAll( '.tabs' ) ).forEach( function( el ) {
					new CBPFWTabs( el );
				});

			})();
		</script>
						<!--/form-->
					</div>

				</div>




<div class="pull-right" style="margin-top: 10px;">
					<form class="form-horizontal" method="POST" action="{{ route('logout') }}">
						{{ csrf_field() }}
						<button type="submit" class="login100-form-btn">
							Cerrar Sesión
						</button>
					</form>
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
