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

try{
uniqidReal('abc');
}catch(Throwable $e){
	echo "err";
}
?>
