<?php
/*
help (syn. man, h, ?)
@@
Usage: $0 [command]
Switches: none
Displays general help, or help on a specific command (if supplied).
@@
*/

if(!isset($tokens[1])){
	if(get_option(THEME_OPTION_PREFIX.'static_help')){
		$fn='usr/share/doc/help.html';
		$fh=fopen($fn,'r');
		$f=fread($fh,filesize($fn));
		fclose($fh);
		e($f);
	}else{
		$cmds=array();
		foreach(array('/bin','/usr/bin') as $d){
			$dh=opendir(CLI_DIR.$d);
			if($dh){
				while($f=readdir($dh)){
					if(preg_match('/^command-(.*?)\.inc\.php$/',$f,$m)){
						$cmds[]=$m[1];
					}
				}
				closedir($dh);
			}
		}
		$cmds=array_unique($cmds);
		sort($cmds);
		e('<table>');
		for($i=0; $i < count($cmds); $i+=3){
			e('<tr>');
			for($j = $i; $j < $i+3; $j++){
				e('<td>'.(isset($cmds[$j])?'<span class="linky" onclick="help(\''.$cmds[$j].'\');">'.$cmds[$j].'</span>':'').'</td>');
			}
			e('</tr>');
		}
		e('</table>');
	}
}else{
	include('lib/bash.aliases.inc.php');
	$hc=$tokens[1];
	if(isset($aliases[$tokens[1]])){
		$hc=$aliases[$tokens[1]];
		e('<p>'.$tokens[1].' is an alias of '.$hc.'.</p>');
	}	
	$cf=false;
	$cfc='bin/command-'.strtolower($hc).'.inc.php';
	if(file_exists('usr/'.$cfc)){
		$cf='usr/'.$cfc;
	}else if(file_exists($cfc)){
		$cf=$cfc;
	}
	if($cf){
		$fh=fopen($cf, 'r');
		$f=fread($fh,filesize($cf));
		fclose($fh);
		$f=nl2br($f);
		$f=str_replace("\n"," ",$f);
		preg_match('/@@(.*?)@@/',$f,$help);
		if(isset($help[1])){
			$h=str_replace('$0',$tokens[1],$help[1]);
			e('<pre>'.$h.'</pre>');
		}else{
			e('<p>No help available for "'.$tokens[1].'".</p>');
		}
	}else{
		e('<p>No help available for "'.$tokens[1].'".</p>');
	}
}
?>
