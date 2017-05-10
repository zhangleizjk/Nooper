<?php

include_once './library/translator.class.php';
include_once './library/mimicry.class.php';
$mi=new \Nooper\Mimicry();
$data=$mi->post('http://127.0.0.1/reply.php', '');
$helper=new  \Nooper\Translator();
$datas=$helper->parseXML($data);
var_dump($datas);

?>
