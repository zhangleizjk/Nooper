<?php
header ( "Cache-Control: max-age=0" );
header ( "Content-Description: File Transfer" );
header ( 'Content-disposition: attachment; filename=a.pdf' ); // 文件名
header ( "Content-Type: application/zip" ); // zip格式的
header ( "Content-Transfer-Encoding: binary" ); // 告诉浏览器，二进制文件
//header ( 'Content-Length: ' . filesize ( $filename ) ); // 告诉浏览器，文件大小
readfile ( 'perl.pdf' );//输出文件;
?>
