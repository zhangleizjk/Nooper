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
include_once './library/mimicry.class.php';
//$mi=new \Nooper\Mimicry();
//$data=$mi->post('http://127.0.0.1/demo2.php', '');
//header ( "Content-Description: File Transfer" );
//header ( 'Content-disposition: attachment; filename=bbb.pdf' ); // 文件名
//header ( "Content-Type: application/zip" ); // zip格式的
//header ( "Content-Transfer-Encoding: binary" ); // 告诉浏览器，二进制文件
//echo $data;

function now(int $seconds = 0): array {
		$dt = new DateTime();
		$dt->setTimezone(new DateTimeZone('Asia/Shanghai'));
		$dt->add(new DateInterval('PT' . $seconds . 'S'));
		$datas['stamp'] = $dt->getTimestamp();
		$datas['datetime'] = $dt->format('YmdHis');
		$datas['date'] = $dt->format('Ymd');
		return $datas;
	}
	
	$d=now(0);
	print_r($d);
?>
