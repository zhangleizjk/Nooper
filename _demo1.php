<?php
namespace Nooper;
include_once './library/mimicry.class.php';
include_once './library/translator.class.php';

$datas=[
	
];

$helper=new Translator();
$xml=$helper->createXML($datas, null, true, true);
echo $xml;

?>
