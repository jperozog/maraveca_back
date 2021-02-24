<!DOCTYPE html>
<html lang="en">

<head>
    <title>Maraveca Telecomunicaciones</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/icons/favicon.ico')); ?>" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="<?php echo e(asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css')); ?>">
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
    #exTab3 .nav-pills>li>a {
        border-radius: 4px 4px 0 0;
    }
    </style>
</head>

<body>

    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-content100">
                <svg class="hidden">
                    <defs>
                        <path id="tabshape" d="M80,60C34,53.5,64.417,0,0,0v60H80z" />
                    </defs>
                </svg>
                <section style="width: 100%;">
                    <div class="tabs tabs-style-shape">
                        <nav>
                            <ul>
                                <li>
                                    <a href="#tab1">
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <span style="font-family: Montserrat-Bold;">Informacion de Contacto</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab2">
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <span style="font-family: Montserrat-Bold;">Tus Facturas</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab3">
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <span style="font-family: Montserrat-Bold;">Movimientos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab4">
                                        <svg viewBox="0 0 80 60" preserveAspectRatio="none">
                                            <use xlink:href="#tabshape"></use>
                                        </svg>
                                        <span style="font-family: Montserrat-Bold;">Reportar Pagos</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <div class="content-wrap">
                            <section id="tab1">
                                <div class="columns-l">
                                    <?php if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social))): ?>
                                    <div class="blue" style="text-align: left !important;">
                                        <span class="font-weight-bold"
                                            style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
                                            <?php echo e(ucwords(strtolower($cliente->social))); ?>

                                    </div>
                                    <?php else: ?>
                                    <div class="blue"
                                        style="text-align: left;font-weight: bold;font-family: Montserrat-Bold;">
                                        <?php echo e(ucwords(strtolower($cliente->nombre." ".$cliente->apellido))); ?>

                                    </div>
                                    <p class="blue">
                                        <span class="font-weight-bold">Cedula:</span><span style="color: #74777b;">
                                            <?php echo e(ucfirst($cliente->kind." ".$cliente->dni)); ?></span>
                                    </p>
                                    <p class="blue">
                                        <span class="font-weight-bold">Correo:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->email)); ?></span>
                                    </p>
                                    <p class="blue">
                                        <span class="font-weight-bold">Telefono:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->phone1)); ?></span>
                                    </p>
                                    <?php if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2)): ?>
                                    <p class="blue">
                                        <span class="font-weight-bold">Telefono 2:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->phone2)); ?></span>
                                    </p>
                                    <?php endif; ?>
                                    <p class="blue">
                                        <span class="font-weight-bold">Direccion:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst(strtolower($cliente->direccion))); ?></span>
                                    </p>
                                    <?php endif; ?>
                                    <?php if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social))): ?>
                                    <p class="blue">
                                        <span class="font-weight-bold">Rif:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->kind).$cliente->dni); ?></span>
                                    </p>
                                    <p class="blue">
                                        <span class="font-weight-bold">Persona de contacto:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->nombre." ".$cliente->apellido)); ?></span>
                                        <span class="font-weight-bold">Telefono:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->phone1)); ?></span>
                                    </p>
                                    <?php if(isset($cliente->phone2) && ($cliente->phone1 != $cliente->phone2)): ?>
                                    <p class="blue">
                                        <span class="font-weight-bold">Telefono 2:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->phone2)); ?></span>
                                    </p>
                                    <?php endif; ?>
                                    <p class="blue">
                                        <span class="font-weight-bold">Correo:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->email)); ?></span>
                                    </p>
                                    <p class="blue">
                                        <span class="font-weight-bold">Direccion:</span> <span
                                            style="color: #74777b;"><?php echo e(ucfirst($cliente->direccion)); ?></span>
                                    </p>
                                    <?php endif; ?>
                                </div>

                                <div class="columns-r">
                                    <div class="accordion md-accordion" id="accordionEx" role="tablist"
                                        aria-multiselectable="true">

                                        <!-- Accordion card -->
                                        <div class="card">

                                            <!-- Card header -->
                                            <div class="card-header" role="tab" id="headingOne1">
                                                <a data-toggle="collapse" data-parent="#accordionEx"
                                                    href="#collapseOne1" aria-expanded="true"
                                                    aria-controls="collapseOne1">
                                                    <h5 class="mb-0" style="font-size: 14px;">Tus Planes <i
                                                            class="fa fa-angle-down rotate-icon"></i>
                                                    </h5>
                                                </a>
                                            </div>

                                            <!-- Card body -->
                                            <div id="collapseOne1" class="collapse" role="tabpanel"
                                                aria-labelledby="headingOne1" data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <td class="tituloplanes" colspan="3">
                                                                    <h6>
                                                                        <?php echo e($plan->name_plan); ?>

                                                                    </h6>
                                                                </td>
                                                            </tr>
                                                            <!--	{{--<tr>
															<td class="costoplanes" colspan="2">
																<h6>
																	<?php echo e(number_format($plan->cost_plan, 2)." "); ?>Bs.S.
																</h6>
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
                                                    <!--	{{--	<p class="text text-center">
													El monto total de su renta basica y/o servicios es de: <a class="font-weight-bold"><?php echo e(number_format($total_mensual, 2)." "); ?>Bs.S.</a>
												</p>--}}-->
                                                </div>
                                            </div>

                                        </div>
                                        <!-- Accordion card -->

                                        <!-- Accordion card -->
                                        <div class="card">

                                            <!-- Card header -->
                                            <div class="card-header" role="tab" id="headingTwo2">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx"
                                                    href="#collapseTwo2" aria-expanded="false"
                                                    aria-controls="collapseTwo2">
                                                    <h5 class="mb-0" style="font-size: 14px;">Tickets <i
                                                            class="fa fa-angle-down rotate-icon"></i>
                                                    </h5>
                                                </a>
                                            </div>

                                            <!-- Card body -->
                                            <div id="collapseTwo2" class="collapse" role="tabpanel"
                                                aria-labelledby="headingTwo2" data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <?php if(count($soportes)>0): ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead style="font-size: 15px;color: #2096c5;">
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Estado</th>
                                                                    <th scope="col">Motivo</th>

                                                                    <th scope="col">Apertura</th>
                                                                    <th scope="col">Cierre</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody style="font-size: 15px;">
                                                                <?php $__currentLoopData = $soportes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <tr>
                                                                    <th scope="row" style="color: #2096c5;">
                                                                        <?php echo e($ticket->id_soporte); ?></th>
                                                                    <td>Cerrado</td>
                                                                    <td><?php echo e(ucwords($ticket->problems)); ?>
                                                                    </td>
                                                                    <td><?php echo e(date('d/m/Y', strtotime($ticket->created_at))); ?>
                                                                    </td>
                                                                    <td><?php echo e(date('d/m/Y', strtotime($ticket->updated_at))); ?>
                                                                    </td>
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
                                        <div class="wrap-input100 alert-success" role="alert" align="center">
                                            <?php if(session('success')): ?>
                                            <?php echo e(session('success')); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Accordion card -->
                                </div>
                            </section>

                            <section id="tab2">

                                <div id="calc_fecha" style="display: none;">
                                    <p class="fecha_tasa">
                                        <?php echo 'Usted ha consultado el calculo del monto a pagar en moneda local: '.$fecha_tasa ?>
                                    </p>
                                    <p class="fecha_tasa"><b>Hora:</b><?php echo $hora_tasa ?></p>

                                </div>
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


                                        <?php $sum_rest = 0; ?>

                                        <?php $__currentLoopData = $facturacion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <!-- =========================================================================================================================================== -->
                                        <?php 
                                        $precio_plan_dl = e(number_format($factura->monto,2)." ");
                                        $pagado_dl = e(number_format($factura->pagado,2)." ");;
                                        $monto_rest= e(number_format($factura->bal,2));

                                         ?>


                                        <tr id="element1-<?php echo e($factura->id);?>">
                                            <?php if($factura->fac_num!=null): ?>
                                            {{--									<!--<th scope="row" style="color: #2096c5;"><?php echo e($factura->fac_num); ?></th>-->--}}
                                            <?php else: ?>
                                            <th scope="row" style="color: #2096c5;"><?php echo e($factura->id); ?></th>
                                            <?php endif; ?>
                                            <td class="monto">
                                                <span>
                                                    <?php echo e(number_format($factura->monto,2)." ");?>$
                                                </span>
                                                <span style="color: #C52A20;">
                                                    <?php if($factura->bal != '0'): ?>
                                                    (<?php echo number_format( ((float)$precio_plan_dl * (float)$tasa),2); ?>
                                                    Bs.S )
                                                </span>
                                                <!--
											<button id="show-<?php echo e($factura->id);?>" title="calcular el monto en Bs.S " class="show calc ">
												<i class="fas fa-calculator"></i>
											</button>
											-->
                                                <?php endif; ?>
                                            </td>
                                            <td>

                                                <?php echo e(number_format($factura->pagado,2)." "); ?>$

                                            </td>
                                            <td>
                                                <span>
                                                    <?php echo e(number_format($factura->bal,2)); ?> $
                                                </span>
                                                <span style="color: #C52A20;">
                                                    (<?php echo number_format( ((float)$monto_rest * (float)$tasa),2); ?>
                                                    BS.S) </span>
                                                </span>
                                            </td>
                                            <td><?php echo e(date('d/m/Y', strtotime($factura->created_at))); ?></td>
                                            <td>
                                                <form target="_blank" class="form-horizontal" method="POST"
                                                    action="<?php echo e(route('clientesoverfac')); ?>">
                                                    <input type="hidden" id="id" name="id"
                                                        value="<?php echo e($factura->id); ?>">
                                                    <button class="login100-form-btn" type="submit"
                                                        style="padding-left: 20px;padding-right: 20px;width: auto;">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </form>
                                            </td>


                                        </tr>
                                        <!--  ================================================================================================================================================================================================== -->
                                        <!-- este "tr" es el monto a bs del cliente, cuando se desea conocer su monto a bs -->







                                        <?php 
                                        $precio_plan_dl = e(number_format($factura->monto,2)." ");
                                        $pagado_dl = e(number_format($factura->pagado,2)." ");;
                                        $monto_rest= e(number_format($factura->bal,2));

                                         ?>

                                        <tr id="element-<?php echo e($factura->id);?>" style="display: none;">

                                            <?php if($factura->fac_num!=null): ?>
                                            <th scope="row" style="color: #2096c5;"><?php echo e($factura->fac_num); ?>
                                            </th>
                                            <?php else: ?>
                                            <th scope="row" style="color: #2096c5;"><?php echo e($factura->id); ?></th>
                                            <?php endif; ?>


                                            <td class="monto">
                                                <span><?php echo number_format( ((float)$precio_plan_dl * (float)$tasa),2); ?>
                                                    BS.S </span>
                                                <button id="ocultar-<?php echo e($factura->id);?>" class="ocultar calc"
                                                    title="volver a mostrar monto en $ " type="submit">
                                                    <i class="fas fa-calculator"></i>
                                                </button>

                                            </td> <!-- se reflejara el monto del plan en dolares -->
                                            <td class="monto">
                                                <?php echo number_format( ((float)$pagado_dl * (float)$tasa),2); ?> BS.S
                                            </td>

                                            <td class="monto">
                                                <?php echo number_format( ((float)$monto_rest * (float)$tasa),2); ?>
                                                BS.S</td>

                                            <td><?php echo e(date('d/m/Y', strtotime($factura->created_at))); ?></td>
                                            <td>
                                                <form target="_blank" class="form-horizontal" method="POST"
                                                    action="<?php echo e(route('clientesoverfac')); ?>">
                                                    <input type="hidden" id="id" name="id"
                                                        value="<?php echo e($factura->id); ?>">
                                                    <button class="login100-form-btn" type="submit"
                                                        style="padding-left: 20px;padding-right: 20px;width: auto;">

                                                        <i class="fas fa-download"></i>
                                                    </button>

                                                </form>
                                            </td>

                                        </tr>


                                        <!--  ================================================================================================================================================================================================== -->

                                        <?php $sum_rest += $monto_rest;?>




                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </tbody>
                                </table>
                                <div style="width: 100%;text-align: center;margin-bottom: 10px;">
                                    <?php if(round($balanceoD,2) != 0): ?>
                                    <span style="color:red">Su deuda total es de:
                                        <?php echo e(number_format($balanceoD)." "); ?> US$.</span>
                                    <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) > 0): ?>
                                    <span style="color:green">Usted tiene un saldo a favor de:
                                        <?php echo e(number_format($afavord)." "); ?> US$.</span>
                                    <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) == 0): ?>
                                    <span style="color:green">Usted se encuentra solvente</span>
                                    <?php endif; ?>
                                </div>

                                <?php if($cliente->serie==0): ?>
                                <div class="accordion md-accordion" id="accordionEx" role="tablist"
                                    aria-multiselectable="true">

                                    <!-- Accordion card -->
                                    <div class="card">

                                        <!-- Card header -->
                                        <div class="card-header" role="tab" id="headingOne1">
                                            <a data-toggle="collapse" data-parent="#accordionEx" href="#cuentas1"
                                                aria-expanded="true" aria-controls="collapseOne1">
                                                <h5 class="mb-0" style="font-size: 14px;">Cuentas para pagar<i
                                                        class="fa fa-angle-down rotate-icon"></i>
                                                </h5>
                                            </a>
                                        </div>

                                        <!-- Card body -->
                                        <div id="cuentas1" class="collapse" role="tabpanel"
                                            aria-labelledby="headingOne1" data-parent="#accordionEx">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <?php $__currentLoopData = $bancos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($b['perm'] == '1'): ?>
                                                    <div class="card-body">
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Titular:</span> <span
                                                                style="color: #74777b;"><?php echo e(ucwords($b['titular'])); ?></span>
                                                            <br />
                                                        </p>
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Identificacion:</span> <span
                                                                style="color: #74777b;"><?php echo e(ucwords($b['dni'])); ?></span>
                                                            <br />
                                                        </p>
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Cuenta:</span> <span
                                                                style="color: #74777b;"><?php echo e(strtoupper($b['banco']).": ".$b['numero']); ?></span>
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

                                <div class="accordion md-accordion" id="accordionEx" role="tablist"
                                    aria-multiselectable="true">

                                    <!-- Accordion card -->
                                    <div class="card">

                                        <!-- Card header -->
                                        <div class="card-header" role="tab" id="headingOne1">
                                            <a data-toggle="collapse" data-parent="#accordionEx" href="#cuentas2"
                                                aria-expanded="true" aria-controls="collapseOne1">
                                                <h5 class="mb-0" style="font-size: 14px;">Cuentas para pagar<i
                                                        class="fa fa-angle-down rotate-icon"></i>
                                                </h5>
                                            </a>
                                        </div>

                                        <!-- Card body -->
                                        <div id="cuentas2" class="collapse" role="tabpanel"
                                            aria-labelledby="headingOne1" data-parent="#accordionEx">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <?php $__currentLoopData = $bancos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($b['perm'] == '2'): ?>
                                                    <div class="card-body">
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Titular:</span> <span
                                                                style="color: #74777b;"><?php echo e(ucwords($b['titular'])); ?></span>
                                                            <br />
                                                        </p>
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Identificacion:</span> <span
                                                                style="color: #74777b;"><?php echo e(ucwords($b['dni'])); ?></span>
                                                            <br />
                                                        </p>
                                                        <p class="blue">
                                                            <span class="font-weight-bold">Cuenta:</span> <span
                                                                style="color: #74777b;"><?php echo e(strtoupper($b['banco']).": ".$b['numero']); ?></span>
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
                            <section id="tab4"
                                class="tab-pane <?php echo e(!empty($tab) && $tab == 'tab4' ? 'active' : ''); ?>">
                                <?php if($cliente->serie == 0): ?>
                                <div class="four-tab-l">


                                    <div class="panel with-nav-tabs panel-primary">
                                        <div class="panel-heading">
                                            <ul class="nav nav-tabs">
                                                <li class="active"
                                                    style="margin-left: 20px;background-color: #e6e6e6;margin-bottom: 20px;border-radius: 25px; width: 148px">
                                                    <a href="#tab1primary" data-toggle="tab">Pago en Bolivares</a>
                                                </li>
                                                <li class="dropdown"
                                                    style="left: 20px;background-color: #e6e6e6;margin-bottom: 20px;border-radius: 25px; width: 145px">
                                                    <a href="#" data-toggle="dropdown">Pago en Dolares <span
                                                            class="caret">▼</span></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="#tab2primary" data-toggle="tab">Efectivo</a></li>
                                                        <li><a href="#tab3primary" data-toggle="tab">Pagos Zelle</a>
                                                        </li>
                                                        <li><a href="#tab4primary" data-toggle="tab">Transferencias o
                                                                Wire transfer</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="panel-heading"
                                            style="background-color: #e6e6e6;margin-bottom: 20px;text-align: center;border-radius: 25px;">

                                        </div>

                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab1primary">
                                                    <form method="POST"
                                                        action="<?php echo e(route('reportarpago')); ?>">
                                                        <?php echo e(csrf_field()); ?>

                                                        <div>
                                                            <input type="hidden" id="id" name="pagot" value="bolivar">
                                                        </div>
                                                        <div class="wrap-input100">

                                                            <select class="input100" id="baltip" type="select"
                                                                class="form-control" name="baltip" required autofocus>
                                                                <option value="" selected="selected">Banco</option>
                                                                <option value="2">Banesco</option>
                                                                <option value="1">BOD</option>
                                                                <option value="6">Bicentenario</option>
                                                            </select>
                                                            <span class="focus-input100"></span>
                                                            <span class="symbol-input100">
                                                                <i class="fas fa-money-check-alt"
                                                                    aria-hidden="true"></i>
                                                            </span>
                                                        </div>

                                                        <div class="wrap-input100">
                                                            <select class="input100" id="balfrom" type="select"
                                                                class="form-control" name="balfrom" required autofocus>
                                                                <option value="" selected="selected">Tipo de Transaccion
                                                                </option>
                                                                <option value="1">Transferencia(Mismo banco)</option>
                                                                <option value="12">Otros bancos (Venezuela)</option>
                                                                <option value="13">Otros bancos (Bicentenario)</option>
                                                                <option value="14">Otros bancos (BNC)</option>
                                                                <option value="15">Otros bancos (Provincial)</option>
                                                                <option value="16">Otros bancos (Mercantil)</option>
                                                                <option value="17">Otros bancos (Banco del Tesoro)
                                                                </option>
                                                                <option value="18">Otros bancos (Venezolano de Crédito)
                                                                </option>
                                                                <option value="19">Otros bancos (BANPLUS)</option>
                                                                <option value="20">Otros bancos (BFC)</option>
                                                                <option value="2">Transferencia(Otros bancos)</option>
                                                                <option value="3">Deposito Bancario</option>
                                                            </select>
                                                            <span class="focus-input100"></span>
                                                            <span class="symbol-input100">
                                                                <i class="fas fa-money-check" aria-hidden="true"></i>
                                                            </span>
                                                        </div>

                                                        <div class="wrap-input100 validate-input">
                                                            <input class="input100" id="comment" type="comment"
                                                                class="form-control" name="comment" required
                                                                placeholder="Referencia" required autofocus>
                                                            <span class="focus-input100"></span>
                                                            <span class="symbol-input100">
                                                                <i class="fas fa-check-double" aria-hidden="true"></i>
                                                            </span>
                                                            <!-- <span class="focus-input100"></span> -->
                                                        </div>

                                                        <div class="wrap-input100 validate-input">
                                                            <input class="input100" id="montobs" type="text"
                                                                class="form-control" name="monto" required
                                                                placeholder="Monto" required autofocus>
                                                            <span class="focus-input100"></span>
                                                            <span class="symbol-input100">
                                                                <i class="fas fa-money-bill-wave-alt"
                                                                    aria-hidden="true"></i>
                                                            </span>
                                                            <!-- <span class="focus-input100"></span> -->
                                                        </div>


                                                        <div class="wrap-input100 validate-input">
                                                            <label for="Fecha"
                                                                class="col-md-4 control-label">Fecha</label>
                                                            <input id="fecha" type="date" class="form-control"
                                                                name="fecha" required autofocus>

                                                            <div class="container-login100-form-btn">
                                                                <button class="login100-form-btn" type="submit">
                                                                    Cargar Pago
                                                                </button>
                                                            </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab2primary">
                                                Efectivo en $
                                                <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
                                                    <?php echo e(csrf_field()); ?>

                                                    <div>
                                                        <input type="hidden" id="id" name="pagot" value="efectivo">
                                                    </div>
                                                    <div class="wrap-input100">
                                                        <select class="input100" id="balfrom" type="select"
                                                            class="form-control" name="balfrom" required autofocus>
                                                            <option value="" selected="selected">Oficina de recepcion
                                                            </option>
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
                                                        <input class="input100" id="monto" type="number"
                                                            class="form-control" name="monto" min="0" required
                                                            placeholder="Monto" required autofocus step="any">
                                                        <span class="focus-input100"></span>
                                                        <span class="symbol-input100">
                                                            <i class="fas fa-money-bill-wave-alt"
                                                                aria-hidden="true"></i>
                                                        </span>
                                                        <!-- <span class="focus-input100"></span> -->
                                                    </div>

                                                    <div class="wrap-input50 validate-input">
                                                        <label for="Fecha" class="col-md-4 control-label">Fecha</label>
                                                        <input id="fecha" type="date" class="form-control" name="fecha"
                                                            required autofocus>
                                                    </div>

                                                    <div class="container-login100-form-btn">
                                                        <button class="login100-form-btn" type="submit">
                                                            Cargar Pago
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade" id="tab3primary">

                                                <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
                                                    <?php echo e(csrf_field()); ?>

                                                    <div>
                                                        Zelle
                                                    </div>
                                                    <div class="wrap-input100">
                                                        <input type="hidden" id="ID" name="pagot" value="zelle">
                                                    </div>
                                                    <div class="wrap-input100 validate-input">
                                                        <input class="input100" id="titular" type="text"
                                                            class="form-control" name="titular" required
                                                            placeholder="Titular de la Cuenta del Emisor" required
                                                            autofocus>
                                                        <span class="focus-input100"></span>
                                                        <span class="symbol-input100">
                                                            <i class="fas fa-check-double" aria-hidden="true"></i>
                                                        </span>
                                                        <!-- <span class="focus-input100"></span> -->
                                                    </div>


                                                    <div class="wrap-input100 validate-input">
                                                        <input class="input100" id="monto" type="number"
                                                            class="form-control" name="monto" min="0" required
                                                            placeholder="Monto" required autofocus step="any">
                                                        <span class="focus-input100"></span>
                                                        <span class="symbol-input100">
                                                            <i class="fas fa-money-bill-wave-alt"
                                                                aria-hidden="true"></i>
                                                        </span>
                                                        <!-- <span class="focus-input100"></span> -->
                                                    </div>

                                                    <div class="wrap-input50 validate-input">
                                                        <label for="Fecha" class="col-md-4 control-label">Fecha</label>
                                                        <input id="fecha" type="date" class="form-control" name="fecha"
                                                            required autofocus>
                                                    </div>



                                                    <div class="container-login100-form-btn">
                                                        <button class="login100-form-btn" type="submit">
                                                            Cargar Pago
                                                        </button>
                                                    </div>
                                                </form>


                                            </div>
                                            <div class="tab-pane fade" id="tab4primary">

                                                <form method="POST" action="<?php echo e(route('reportarpago')); ?>">
                                                    <?php echo e(csrf_field()); ?>

                                                    <div>Wire Transfer</div>
                                                    <div class="wrap-input100">
                                                        <input type="hidden" id="ID" name="pagot" value="wire">
                                                    </div>
                                                    <div class="wrap-input100 validate-input">
                                                        <input class="input100" id="codigo" type="text"
                                                            class="form-control" name="codigo" required
                                                            placeholder="Codigo de confirmacion" required autofocus>
                                                        <span class="focus-input100"></span>
                                                        <span class="symbol-input100">
                                                            <i class="fas fa-check-double" aria-hidden="true"></i>
                                                        </span>
                                                        <!-- <span class="focus-input100"></span> -->
                                                    </div>


                                                    <div class="wrap-input100 validate-input">
                                                        <input class="input100" id="monto" type="number"
                                                            class="form-control" name="monto" min="0" required
                                                            placeholder="Monto" required autofocus step="any">
                                                        <span class="focus-input100"></span>
                                                        <span class="symbol-input100">
                                                            <i class="fas fa-money-bill-wave-alt"
                                                                aria-hidden="true"></i>
                                                        </span>
                                                        <!-- <span class="focus-input100"></span> -->
                                                    </div>

                                                    <div class="wrap-input50 validate-input">
                                                        <label for="Fecha" class="col-md-4 control-label">Fecha</label>
                                                        <input id="fecha" type="date" class="form-control" name="fecha"
                                                            required autofocus>
                                                    </div>



                                                    <div class="container-login100-form-btn">
                                                        <button class="login100-form-btn" type="submit">
                                                            Cargar Pago
                                                        </button>
                                                    </div>
                                                </form>


                                            </div>
                                        </div>
                                    </div>


                                </div>
                        </div>
                        <div class="four-tab-r">
                            <div style="width: 100%; text-align: center;">
                                <?php if(round($balanceoD,2) != 0): ?>
                                <span style="color:red">Su deuda total es de:
                                    <?php echo e(number_format($balanceoD, 2,',', '.')." "); ?> $</span>
                                <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) > 0): ?>
                                <span style="color:green">Usted tiene un saldo a favor de:
                                    <?php echo e(number_format($afavord, 2,',', '.')." "); ?> $</span>
                                <?php elseif(round($balanceoD,2) == 0 && round($afavord,2) == 0): ?>
                                <span style="color:green">Usted se encuentra solvente</span>
                                <?php endif; ?>
                            </div>

                            <div class="accordion md-accordion" id="accordionEx" role="tablist"
                                aria-multiselectable="true" data-parent="#accordionEx">

                                <!-- Accordion card -->
                                <div class="card">

                                    <!-- Card header -->
                                    <div class="card-header" role="tab" id="headingOne1">
                                        <a data-toggle="collapse" data-parent="#accordionEx" href="#One1"
                                            aria-expanded="true" aria-controls="collapseOne1">
                                            <h5 class="mb-0" style="font-size: 14px;">Pagos por verificación <i
                                                    class="fa fa-angle-down rotate-icon"></i>
                                            </h5>
                                        </a>
                                    </div>

                                    <!-- Card body -->
                                    <div id="One1" class="collapse" role="tabpanel" aria-labelledby="headingOne1"
                                        data-parent="#accordionEx">
                                        <div class="card-body">

                                            <div class="table-responsive">
                                                <?php if(count($pagosp)>0): ?>
                                                <table class="table table-sm">
                                                    <?php $__currentLoopData = $pagosp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagosp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td colspan="2"
                                                            style="background-color: #65b7d7;color: #fff;font-family: Montserrat;">
                                                            <?php if($pagosp->bal_tip == '1'): ?>
                                                            BOD
                                                            <?php elseif($pagosp->bal_tip == '2'): ?>
                                                            Banesco
                                                            <?php elseif($pagosp->bal_tip == '6'): ?>
                                                            Bicentenario
                                                            <?php elseif($pagosp->bal_tip == '9'): ?>
                                                            Zelle
                                                            <?php elseif($pagosp->bal_tip == '10'): ?>
                                                            Wire Transfer
                                                            <?php elseif($pagosp->bal_tip == '11'): ?>
                                                            Efectivo
                                                            <?php endif; ?><br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 16px;font-weight: bold;color: #96989a;">
                                                            <span
                                                                style="color: #65b7d7;text-transform: uppercase;">-</span>

                                                            <?php if($pagosp->bal_from == '1' && $pagosp->bal_tip < 8): ?>
                                                            Mismo banco
                                                            <?php elseif($pagosp->bal_from == '2'): ?>
                                                            Otros bancos
                                                            <?php elseif($pagosp->bal_from == '12'): ?>
                                                            Otro bancos(Venezuela)
                                                            <?php elseif($pagosp->bal_from == '13'): ?>
                                                            Otro bancos(Bicentenario)
                                                            <?php elseif($pagosp->bal_from == '14'): ?>
                                                            Otro bancos(BNC)
                                                            <?php elseif($pagosp->bal_from == '15'): ?>
                                                            Otro bancos(Provincial)
                                                            <?php elseif($pagosp->bal_from == '16'): ?>
                                                            Otro bancos(Mercantil)
                                                            <?php elseif($pagosp->bal_from == '17'): ?>
                                                            Otro bancos(Banco del Tesoro)
                                                            <?php elseif($pagosp->bal_from == '18'): ?>
                                                            Otro bancos(Venezolano de Credito)
                                                            <?php elseif($pagosp->bal_from == '19'): ?>
                                                            Otro bancos(BANPLUS)
                                                            <?php elseif($pagosp->bal_from == '20'): ?>
                                                            Otro bancos(BFC)
                                                            <?php elseif($pagosp->bal_from == '3'): ?>
                                                            Deposito Bancario
                                                            <?php elseif($pagosp->bal_from == '4'): ?>
                                                            Coro
                                                            <?php elseif($pagosp->bal_from == '5'): ?>
                                                            Maracaibo
                                                            <?php elseif($pagosp->bal_from == '6'): ?>
                                                            Taquilla de pago Dabajuro
                                                            <?php endif; ?><span
                                                                style="color: #65b7d7;text-transform: uppercase;">-</span><br>

                                                            <?php if($pagosp->bal_tip < '8'): ?>
                                                            <span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Ref:</span>
                                                            <?php echo e($pagosp->bal_comment); ?><br>
                                                            <span
                                                                style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp->bal_monto,2,',', '.')." "); ?></span><span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Bs.S.</span><br>
                                                            <?php elseif($pagosp->bal_tip == '9'): ?>
                                                            <span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Titular:</span>
                                                            <?php echo e($pagosp->bal_comment); ?><br>
                                                            <span
                                                                style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp->bal_monto,2)." "); ?></span><span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">$</span><br>
                                                            <?php elseif($pagosp->bal_tip == '10'): ?>
                                                            <span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">Codigo
                                                                de Confirmacion:</span>
                                                            <?php echo e($pagosp->bal_comment); ?><br>
                                                            <span
                                                                style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp->bal_monto,2)." "); ?></span><span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">$</span><br>
                                                            <?php elseif($pagosp->bal_tip == '11'): ?>
                                                            <span
                                                                style="font-size: 22px;line-height: 15px;"><?php echo e(number_format($pagosp->bal_monto,2)." "); ?></span><span
                                                                style="color: #65b7d7;text-transform: uppercase;font-size: 10px;">$</span><br>
                                                            <?php endif; ?>
                                                            <span
                                                                style="color: #65b7d7;text-transform: uppercase;"><?php echo e(date('d/m/Y', strtotime($pagosp->created_at))); ?></span>
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
                                <!-- Accordion card -->
                            </div>

                        </div>

                        <?php elseif($cliente->serie == 1): ?>
                        <div>

                            <span style="font-size: 30px;color: #2096c5">Para reportar su pago enviar el soporte del
                                mismo a administracion@maraveca.com</span>

                        </div>
                        <?php endif; ?>
                </section>
                <script src="<?php echo e(asset('js/cbpFWTabs.js')); ?>"></script>
                <script>
                (function() {

                    [].slice.call(document.querySelectorAll('.tabs')).forEach(function(el) {
                        new CBPFWTabs(el);
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
        <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
        </script>
        <!--===============================================================================================-->
        <script src="<?php echo e(asset('js/main.js')); ?>"></script>

</body>

</html>