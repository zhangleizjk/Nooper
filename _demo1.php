<?php
function uniqidReal(int $len = 13) {
		$str='';
		$chars = array_merge(range('0', '9'), range('a', 'z'));
		$end = count($chars) - 1;
		for($i = 0;$i < $len;$i++){
			$str .= $chars[mt_rand(0, $end)];
		}
		return strtoupper($str);
}

include_once './library/translator.class.php';
$helper=new \Nooper\Translator();
$datas=['id'=>'0001', 'name'=>'tom', [
	'a1'=>'中国的人',
	'a2'=>13
]];
$end=$helper->createXML($datas);
var_dump($end);
?>
