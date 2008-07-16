<?php
/*
hosts
@@
Usage: $0
Synonyms: none
Switches: --refresh: refresh host list
lists possible hosts to telnet to
@@
*/

if(!isset($_SESSION['hosts']) || isset($switches['refresh'])){
	$hostsxml=@file_get_contents('http://blog.elinc.ca/rod/cli-mothership/list.php');
	if(!$hostsxml){
		e('<p>Failed to get host list</p>');
	}else{
		$_SESSION['hosts']=array();
		preg_match_all('/name="(.*?)" interpreter="(.*?)"/',$hostsxml,$m);
		for($i=0;$i<count($m[1]);$i++){
			$_SESSION['hosts'][$m[1][$i]]=$m[2][$i];
		}
	}
}
if(count($_SESSION['hosts'])==0){
	e("<p>No hosts.</p>");
}else{
	foreach($_SESSION['hosts'] as $h => $u){
		$esch=str_replace("'","\\\\'+String.fromCharCode(39)+'",$h);
		e('<span class="linky" onclick="telnet(\''.$esch.'\');">'.$h.'</span><br />');
	}
}

?>
