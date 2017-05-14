<?php
namespace Nooper;

include_once './library/payer.class.php';
include_once './library/translator.class.php';

$app_id='0001';
$mch_id='0002';
$key='0003';
$notify_url='0004';
$payer=new Payer($app_id, $mch_id, $key, $notify_url);
//$payer->data('body','这是一个测试订单');
try{
	$payer->parse('<xml><aaa>tom</aaa></xml>');
}catch(\Exception $e){
	echo $e->getCode();
	echo "<br />";
	echo strtoupper($e->getMessage());
}



?>
