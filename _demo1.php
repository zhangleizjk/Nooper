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

	$dt = new DateTime();
	$dt->setTimezone(new DateTimeZone('Asia/Shanghai'));
		$datas['stamp'] = $dt->getTimestamp();
		$datas['format']=$dt->format('YmdHis');
		print_r($datas);
		$dt->add(new DateInterval('PT3600S'));
		$datas['stamp'] = $dt->getTimestamp();
		$datas['format']=$dt->format('YmdHis');
		print_r($datas);
?>
