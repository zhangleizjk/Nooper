<?php
namespace Nooper;

include_once './library/payer.class.php';

$app_id='0001';
$mch_id='0002';
$key='0003';
$notify_url='0004';
$payer=new Payer($app_id, $mch_id, $key, $notify_url);




?>
