<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class servicios extends Model
{
    protected $fillable = [
      'plan_srv',
      'cliente_srv',
      'instalacion_srv',
      'recibo_srv',
      'costo_instalacion_srv',
      'credito_srv',
      'start_srv',
      'notify_srv',
      'equipo_srv',
      'ip_srv',
      'mac_srv',
      'serial_srv',
      'ap_srv',
        'tipo_plan_srv',
        'modo_pago_srv',
        'serie_srv',
        /*'direccion_srv',*/
      'zona_srv',
      'stat_srv',
        'gen_comision_serv',
    'user_comision_serv',
    'porcentaje_comision_serv',
      'comment_srv'];//
}
