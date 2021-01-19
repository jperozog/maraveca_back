<!DOCTYPE html>
<html lang="en">
<head>
	<title>Maraveca Telecomunicaciones</title>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="<?php echo e(asset('images/icons/favicon.ico')); ?>"/>
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
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
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/normalize.css')); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/demo.css')); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/tabs.css')); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/tabstyles.css')); ?>" />
	<script src="<?php echo e(asset('js/modernizr.custom.js')); ?>"></script>

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
						<section id="tab1"><div class="columns-l"><?php if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social))): ?>
								<div class="blue" style="text-align: left !important;">
								<span class="font-weight-bold" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
									<?php echo e(ucwords(strtolower($cliente->social))); ?>

								</div>
								<?php else: ?>
								<div class="blue" style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
									<?php echo e(ucwords(strtolower($cliente->nombre." ".$cliente->apellido))); ?>

								</div>
								<p class="blue">
									<span class="font-weight-bold">Cedula:</span><span style="color: #74777b;"> <?php echo e(ucfirst($cliente->kind." ".$cliente->dni)); ?></span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Correo:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->email)); ?></span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Telefono:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->phone1)); ?></span>
								</p>
								<?php if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2)): ?>
								<p class="blue">
									<span class="font-weight-bold">Telefono 2:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->phone2)); ?></span>
								</p>
								<?php endif; ?>
								<p class="blue">
									<span class="font-weight-bold">Direccion:</span> <span style="color: #74777b;"><?php echo e(ucfirst(strtolower($cliente->direccion))); ?></span>
								</p>
								<?php endif; ?>
								<?php if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social))): ?>
								<p class="blue">
									<span class="font-weight-bold">Rif:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->kind).$cliente->dni); ?></span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Persona de contacto:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->nombre." ".$cliente->apellido)); ?></span>
									<span class="font-weight-bold">Telefono:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->phone1)); ?></span>
								</p>
								<?php if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2)): ?>
								<p class="blue">
									<span class="font-weight-bold">Telefono 2:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->phone2)); ?></span>
								</p>
								<?php endif; ?>
								<p class="blue">
									<span class="font-weight-bold">Correo:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->email)); ?></span>
								</p>
								<p class="blue">
									<span class="font-weight-bold">Direccion:</span> <span style="color: #74777b;"><?php echo e(ucfirst($cliente->direccion)); ?></span>
								</p>
								<?php endif; ?></div>

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
													<table class="table table-bordered">						<?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<tr>
															<td class="tituloplanes" colspan="3">
																<h6>
																	<?php echo e($plan->name_plan); ?>

																</h6>
															</td>
														</tr>
													<!--{{--	<tr>
															<td class="costoplanes" colspan="2">
																<?php if($plan->tipo_plan_srv !=3): ?>
																<h6>
																	<?php echo e(number_format($plan->cost_plan, 2)." "); ?>Bs.S.
																</h6>
																	<?php else: ?>
																	<h6>
																		<?php echo e(number_format($plan->cost_plan)." "); ?>$
																	</h6>
																	<?php endif; ?>
															</td>
															<?php if($plan->comment_srv != "" && $plan->comment_srv != null): ?>
															<td class="lugarplanes" colspan="2">
																<h6>
																	<?php echo e($plan->comment_srv); ?>

																</h6>
															</td>
															<?php endif; ?>
														</tr>--}}-->
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</table>
												</div>
												<!--{{--	<?php if($plan->tipo_plan_srv !=3 && $plan->tipo_plan_srv !=1 ): ?>
												<p class="text text-center">
													El monto total de su renta basica y/o servicios es de: <a class="font-weight-bold"><?php echo e(number_format($total_mensualB, 2,',', '.')." "); ?>Bs.S.</a>
												</p>
												<?php elseif($plan->tipo_plan_srv =3 && $plan->tipo_plan_srv !=1): ?>
												<p class="text text-center">
													El monto total de su renta basica y/o servicios es de: <a class="font-weight-bold"><?php echo e(number_format($total_mensualD)." "); ?>$</a>
												</p>

												<?php endif; ?>--}}-->
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
												<?php if(count($soportes)>0): ?>
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
														<?php $__currentLoopData = $soportes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<tr>
															<th scope="row" style="color: #2096c5;"><?php echo e($ticket->id_soporte); ?></th>
															<td>Cerrado</td>
															<td><?php echo e(ucwords($ticket->problems)); ?></td>
															<td><?php echo e(date('d/m/Y', strtotime($ticket->created_at))); ?></td>
															<td><?php echo e(date('d/m/Y', strtotime($ticket->updated_at))); ?></td>
														</tr>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</tbody>
													</table>

												</div>
												<?php else: ?>
												<div class="text text-center">
													No posee tickets que mostrar
												</div>
												<?php endif; ?>
											</div>

										</div>

									</div>
									<!-- Accordion card -->
									<div class="wrap-input100 alert-success" role= "alert" align="center">
										<?php if(session('success')): ?>
                                            <?php echo e(session('success')); ?>

                                        <?php endif; ?>
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
								<?php $__currentLoopData = $facturacion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<?php if($factura->fac_num!=null): ?>
									<th scope="row" style="color: #2096c5;"><?php echo e($factura->fac_num); ?></th>
									<?php else: ?>
									<th scope="row" style="color: #2096c5;"><?php echo e($factura->id); ?></th>
									<?php endif; ?>
									<td><?php if($factura->denominacion != '$' ): ?>
										<?php echo e(number_format($factura->monto,2)." "); ?>Bs.S.
										<?php else: ?>
										<?php echo e(number_format($factura->monto,2)." "); ?>$
										<?php endif; ?></td>
									<td>
										<?php if($factura->denominacion != '$'  ): ?>
										<?php echo e(number_format($factura->pagado,2)." "); ?>Bs.S.
											<?php else: ?>
											<?php echo e(number_format($factura->pagado,2)." "); ?>$
											<?php endif; ?>
									</td>
									<td><?php echo e(number_format($factura->bal,2)); ?></td>
									<td><?php echo e(date('d/m/Y', strtotime($factura->created_at))); ?></td>
									<td><form target="_blank" class="form-horizontal" method="POST" action="<?php echo e(route('clientesoverfac')); ?>">
											<input type="hidden" id="id" name="id" value="<?php echo e($factura->id); ?>">
											<button class="login100-form-btn" type="submit" style="padding-left: 20px;padding-right: 20px;width: auto;">
												<i class="fas fa-download"></i>
											</button>
										</form>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
							<div style="width: 100%;text-align: center;margin-bottom: 10px;">
								<div style="width: 100%;text-align: center;margin-bottom: 10px;">
									<span style=" color: #2096C5">Su balance para planes en moneda local</span>
									<br>
									<?php if(round($balanceoB,2) != 0): ?>
									<span style="color:red">Su deuda total es de:<?php echo e(number_format($balanceoB,2,',', '.')." "); ?> Bs.S.</span>
									<?php elseif(round($balanceoB,2) == 0 && round($afavor,2) > 0): ?>
									<span style="color:green">Usted tiene un saldo a favor de: <?php echo e(number_format($afavor,2,',', '.')." "); ?> Bs.S.</span>
									<?php elseif(round($balanceoB,2) == 0 && round($afavor,2) == 0): ?>
									<span style="color:green">Usted se encuentra solvente</span>
									<?php endif; ?>
								</div>
								<div><hr></div>
							</div>
							<div style="width: 100%;text-align: center;margin-bottom: 10px;">
								<span style=" color: #2096C5">Su balance para planes en moneda internacional</span>
								<br>

									<?php if(round($balanceoD) != 0): ?>
									<span style="color:red">Su deuda total es de:<?php echo e(number_format($balanceoD)." "); ?> US$.</span>
									<?php elseif(round($balanceoD) == 0 && round($afavord,2) > 0): ?>
									<span style="color:green">Usted tiene un saldo a favor de: <?php echo e(number_format($afavord)." "); ?> US$.</span>
									<?php elseif(round($balanceoD) == 0 && round($afavord,2) == 0): ?>
									<span style="color:green">Usted se encuentra solvente</span>
									<?php endif; ?>

							</div>

							<?php if($cliente->serie==0): ?>
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
												<?php $__currentLoopData = $bancos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if($b['perm'] == '1'): ?>
												<div class="card-body">
													<p class="blue">
														<span class="font-weight-bold">Titular:</span> <span style="color: #74777b;"><?php echo e(ucwords($b['titular'])); ?></span> <br />
													</p>
													<p class="blue">
														<span class="font-weight-bold">Identificacion:</span> <span style="color: #74777b;"><?php echo e(ucwords($b['dni'])); ?></span> <br />
													</p>
													<p class="blue">
														<span class="font-weight-bold">Cuenta:</span> <span style="color: #74777b;"><?php echo e(strtoupper($b['banco']).": ".$b['numero']); ?></span>
													</p>
												</div>
												<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</div>
										</div>
									</div>

								</div>
							</div>
							<!-- Accordion card -->
							<?php elseif($cliente->serie==1): ?>

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
												<?php $__currentLoopData = $bancos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if($b['perm'] == '2'): ?>
												<div class="card-body">
													<p class="blue">
														<span class="font-weight-bold">Titular:</span> <span style="color: #74777b;"><?php echo e(ucwords($b['titular'])); ?></span> <br />
													</p>
													<p class="blue">
														<span class="font-weight-bold">Identificacion:</span> <span style="color: #74777b;"><?php echo e(ucwords($b['dni'])); ?></span> <br />
													</p>
													<p class="blue">
														<span class="font-weight-bold">Cuenta:</span> <span style="color: #74777b;"><?php echo e(strtoupper($b['banco']).": ".$b['numero']); ?></span>
													</p>
												</div>
												<?php endif; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<!-- Fin del collapse -->
						</section>
						<section id="tab3">
							<table class="table table-bordered">
								<?php $__currentLoopData = $historial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $historial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td class="tituloplanes" colspan="3">
										<h6><?php echo e($historial->history); ?>

										</h6>
									</td>
								</tr>
								<tr>
									<td class="costoplanes" colspan="2">
										<h6><?php echo e(date('d/m/Y', strtotime($historial->created_at))); ?>

										</h6>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</table>
						</section>
						<section id="tab4" class="tab-pane <?php echo e(!empty($tab) && $tab == 'tab4' ? 'active' : ''); ?>">

							<div >
									<p style="font-size: 22px;color: #2096c5;font-weight: bold">Por favor seleccione el tipo de plan al cual desea reportar su pago</p></br></br>
									<ul class="nav">
										<a href="#" id="btn1"  class="abt" ><span class="opcion1"style="font-size: 20px; text-align: center;line-height: 60px;font-weight: bold">Planes moneda local</span></a>
										<a href="#" id="btn2" class="abt"  ><span class="opcion1"style="font-size: 20px; text-align: center;line-height: 60px;">Planes moneda internacional</span></a>

									</ul>
								</div>



								 <div id="div-results"  ></div>





			</section>
			<script src="<?php echo e(asset('js/cbpFWTabs.js')); ?>"></script>
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
		<form class="form-horizontal" method="POST" action="<?php echo e(route('logout')); ?>">
			<?php echo e(csrf_field()); ?>

			<button type="submit" class="login100-form-btn">
				Cerrar Sesión
			</button>
		</form>
	</div>
			</section>
	<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/jquery/jquery-3.2.1.min.js')); ?>"></script>

	<!--===============================================================================================-->
	<script src="<?php echo e(asset('vendor/jquerymask/jquery.mask.min.js')); ?>"></script>
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
