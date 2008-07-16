<?php
/*
telnet
@@
Usage: $0 hostname
Synonyms: none
Switches: none
connect to a remote host specified by hostname (use hosts to obtain a list of possible hosts).
@@
*/

// http://ca3.php.net/manual/en/function.file-exists.php#74075
if(function_exists('get_headers')){
	function url_exists($url) {
	     if ((strpos($url, "http")) === false) $url = "http://" . $url;
	     if (is_array(@get_headers($url)))
	         return true;
	     else
	         return false;
	}
}else{
	function url_exists($url){
		$f=@file_get_contents($url);
		if($f) return true;
		return false;
	}
}

if(!$params){
	e('<p>No host specified.</p>');
}else{
	if(!isset($_SESSION['hosts'])){
		// copied from command-hosts.inc.php
		$hostsxml=@file_get_contents('http://thrind.xamai.ca/cli-mothership/list.php');
		if(!$hostsxml){
			e('<p>Failed to get host list!</p>');
		}else{
			$_SESSION['hosts']=array();
			preg_match_all('/name="(.*?)" interpreter="(.*?)"/',$hostsxml,$m);
			for($i=0;$i<count($m[1]);$i++){
				$_SESSION['hosts'][$m[1][$i]]=$m[2][$i];
			}
		}
	}
	if(!isset($_SESSION['hosts'][$params])){
		e('<p>Unknown host '.$params.'.</p>');
	}else if(!url_exists($_SESSION['hosts'][$params])){
		bloodyhell();
	}else{
		$_SESSION['telnet_interpreter']=$_SESSION['hosts'][$params].'?'.session_name().'='.md5(rand(1,5000000));
		$foo=@file_get_contents($_SESSION['telnet_interpreter'].'&c=&requestId=0');
		if(!$foo){
			bloodyhell();
		}else{
			preg_match('/<prompt><!\[CDATA\[(.*?)\]\]><\/prompt>/',$foo,$m);
			$prompt=$m[1];
			e('<p>Connected to '.$params.'.</p>');
		}
	}	
}

function bloodyhell(){
			e('<p>Failed to connect to '.$params.'.</p>');
			unset($_SESSION['telnet_interpreter']);
			$foo=@file_get_contents('http://blog.elinc.ca/rod/cli-mothership/register.php?op=remove'
				.'&i='.base64encode($_SESSION['hosts'][$params])
				.'&n='.base64encode($params));
			unset($_SESSION['hosts']);
}
?>