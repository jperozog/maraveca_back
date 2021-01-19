<!DOCTYPE html>
<html lang="en">
<head>

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
<div>
<div  >

									 <div class="four-tab-l" >
										 <div class="panel-heading">
											 <div class="nav nav-tabs">
										 <div class="panel with-nav-tabs panel-primary">
                                             <br>
											 <select style="color:#2096C5; margin-left: 20px;background-color: #e6e6e6;margin-bottom: 20px;border-radius: 25px; width: 248px" id="dropDown" class=" active browser-default custom-select custom-select-lg mb-3 ">
												 <option class ="opcion" > Pago en Dolares</option>
												 <option class ="opcion"value="tab2primary">Efectivo</option>
												 <option class ="opcion"value="tab3primary">Pagos Zelle</option>
												 <option class ="opcion" value="tab4primary">Transferencias o Wire transfer</option>
											 </select>
											 </div>
										 </div>
											 <div class="panel-heading" style="background-color: #e6e6e6;margin-bottom: 20px;text-align: center;border-radius: 25px;">

											 </div>

											 <div class="panel-body">
												 <div class="tab-content">

												 </div>
												 <div class="tab-pane drop-down-show-hide" id="tab2primary">
													 Efectivo en $
													 <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
														 <?php echo e(csrf_field()); ?>

														 <div>
															 <input type="hidden" id="id" name="pagot" value="efectivopd">
														 </div>
														 <div class="wrap-input100">
															 <select class="input100" id="balfrom" type="select" class="form-control" name="balfrom" required autofocus>
																 <option value="" selected="selected">Oficina de recepcion</option>
																 <option value="4">Coro</option>
																 <option value="5">Maracaibo</option>
																 <option value="6">Taquilla de pago Dabajuro</option>
															 </select>
															 <span class="focus-input100"></span>
															 <span class="symbol-input100">
																					<i class="fas fa-money-check" aria-hidden="true"></i>
																					</span>
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
												 <div class="tab-pane drop-down-show-hide" id="tab3primary">

													 <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
														 <?php echo e(csrf_field()); ?>

														 <div>
															 Zelle
														 </div>
														 <div class="wrap-input100">
															 <input type="hidden" id="ID" name="pagot" value="zellepd">
														 </div>
														 <div class="wrap-input100 validate-input">
															 <input class="input100" id="titular" type="text" class="form-control" name="titular" required placeholder="Titular de la Cuenta del Emisor" required autofocus>
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
												 <div class="tab-pane drop-down-show-hide" id="tab4primary">

													 <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
														 <?php echo e(csrf_field()); ?>

														 <div>Wire Transfer</div>
														 <div class="wrap-input100">
															 <input type="hidden" id="ID" name="pagot" value="wirepd">
														 </div>
														 <div class="wrap-input100 validate-input">
															 <input class="input100" id="codigo" type="text" class="form-control" name="codigo" required placeholder="Codigo de confirmacion" required autofocus>
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
											 </div>


										 </div>
									 </div>


									 <div class="four-tab-r">
										 <div style="width: 100%; text-align: center;">
											 <?php if(round($balanceoD,2) != 0): ?>
											 <span style="color:red">Su deuda total es de: <?php echo e(number_format($balanceoD)." "); ?> $</span>
											 <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) > 0): ?>
											 <span style="color:green">Usted tiene un saldo a favor de: <?php echo e(number_format($afavord)." "); ?> $</span>
											 <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) == 0): ?>
											 <span style="color:green">Usted se encuentra solvente</span>
											 <?php endif; ?>
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
															 <?php if(count($pagosp_in)>0): ?>
															 <table class="table table-sm">
																 <?php $__currentLoopData = $pagosp_in; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagosp_in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																 <tr>
																	 <td colspan="2" style="background-color: #65b7d7;color: #fff;font-family: Montserrat;">

																		 <?php if($pagosp_in->bal_tip == '12'): ?>
																		 Zelle
																		 <?php elseif($pagosp_in->bal_tip == '13'): ?>
																		 Wire Transfer
																		 <?php elseif($pagosp_in->bal_tip == '14'): ?>
																		 Efectivo
																		 <?php endif; ?><br>
																	 </td>
																 </tr>
																 <tr>
																	 <td style="font-size: 16px;font-weight: bold;color: #96989a;">



																		 <?php if($pagosp_in->bal_tip_in == '12'): ?>
																		 <span style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Titular:</span> <?php echo e($pagosp_in->bal_comment_in); ?><br>
																		 <span style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp_in->bal_monto_in)." "); ?></span><span style="color: #65b7d7;text-transform: uppercase;font-size: 13px;">US$</span><br>
																		 <?php elseif($pagosp_in->bal_tip_in == '13'): ?>
																		 <span style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Codigo de Confirmacion:</span> <?php echo e($pagosp_in->bal_comment_in); ?><br>
																		 <span style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp_in->bal_monto_in)." "); ?></span><span style="color: #65b7d7;text-transform: uppercase;font-size: 13px;">US$</span><br>
																		 <?php elseif($pagosp_in->bal_tip_in == '14'): ?>
																		 <span style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp_in->bal_monto_in)." "); ?></span><span style="color: #65b7d7;text-transform: uppercase;font-size: 13px;">US$</span><br>
																		 <?php endif; ?>
																		 <span style="color: #65b7d7;text-transform: uppercase;"><?php echo e(date('d/m/Y', strtotime($pagosp_in->created_at))); ?></span>
																	 </td>
																 </tr>
																 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															 </table>

															 <?php else: ?>
															 No tiene pagos pendientes por aprobacion
															 <?php endif; ?>
														 </div>
													 </div>
												 </div>

											 </div>
											 <!-- Accordion card --></div>

									 </div>
								 </div>
	<script src="<?php echo e(asset('js/cbpFWTabs.js')); ?>"></script>
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
