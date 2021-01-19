<?php

require('routeros_api.class.php');

$API = new routeros_api();

$API->debug = true;

if ($API->connect('200.209.74.16', 'jesus', 'a019211894')) {

  $API->write('/ip/firewall/address-list/print', false);
   $API->write('?address='."200.209.74.162", false);
   $READ = $API->read(false);
   $ARRAY = $API->parseResponse($READ);

   print_r($ARRAY);

   $API->disconnect();

}

?>
