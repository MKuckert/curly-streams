<?php

function readStreamFile($file, $retArr=false) {
	$in=file($file);
	
	// Skip the last row
	$c=count($in);
	unset($in[$c-1]);
	
	// Skip until class definition beginning
	for($i=0; $i<$c; $i++) {
		$l=$in[$i];
		unset($in[$i]);
		if(substr($l, 0, 5)==='class') {
			break;
		}
	}
	
	if($retArr) {
		return $in;
	}
	
	return rtrim(implode('', $in)).PHP_EOL;
}