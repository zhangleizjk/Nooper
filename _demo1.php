<?php
namespace Nooper;
include_once './library/mimicry.class.php';

$mm=new Mimicry();
try{
	$data=$mm->get('http://404.php.net/',[]);
	echo $data;
}catch(\Exception $e){
	echo $e->getCode();
	echo $e->getMessage();
}

?>
