<?php

function aaa(bool $yes){
	if($yes){
		throw new Exception('error',100);
		return null;
	}
	return null;
}

function bbb(string $input){
	echo $input;
}

function ccc(){
	bbb(aaa(false));
}

ccc();



?>
