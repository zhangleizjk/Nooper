<?php
function uniqidReal($len = 13) {
		$str='';
		$chars = array_merge(range('0', '9'), range('a', 'z'));
		$end = count($chars) - 1;
		for($i = 0;$i < $len;$i++){
			$str .= $chars[mt_rand(0, $end)];
		}
		return strtoupper($str);
}

$arr=['sign'=>'aaa', 'name'=>null, 'sex'=>'', 'fav'=>'box'];
foreach($arr as $key=>$value){
	if(!is_string($key)) unset($datas)
	if($key=='sign') unset($arr[$key]);
	elseif(is_null($value)) unset($arr[$key]);
}
print_r($arr);
?>
