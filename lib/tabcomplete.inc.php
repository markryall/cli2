<?php
/*
lib/tabcomplete.inc.php
*/

include(CLI_DIR.'/lib/filesystem.inc.php');

$tmp=$_GET['tc'];//preg_replace('/\s+/',' ',$_GET['tc']);
$sppos=strrpos($tmp,' ');
$precomplete=substr($tmp,0,$sppos);
$tocomplete=substr($tmp,$sppos);


$filesonly=false;

if($tocomplete{0}==' '){
	$filesonly=true;
	$tocomplete=substr($tocomplete,1);
	$precomplete.=' ';
}

if(strpos($tocomplete,'/')!==false){
	$filesonly=true;
}

$tcs=fs_tab_completions($tocomplete);
if(!$filesonly){ // do commands too
	/* stolen from command-help. if you fix that, fix this too */
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
		$result=array();
		foreach($cmds as $c){
			if($tocomplete=='' || strpos($c,$tocomplete)===0)
				$result[]=$c;
		}
		
		if(!$tcs)$tcs=$result;
		else if($result) $tcs=array_merge($tcs,$result);	
}

if(is_array($tcs)){
	$tcs=array_unique($tcs);
	
	if(false!==($k=array_search('latest@',$tcs)))$tcs[$k]='latest';

	sort($tcs);
	switch(count($tcs)){
		case 0:
			$commandline=$_GET['tc'];
			break;
		case 1:
			$commandline=$precomplete;
			if(strpos($tocomplete,'/')!==false){
				$commandline.=substr($tocomplete,0,strrpos($tocomplete,'/')+1);
			}
			$commandline.=safe_escape($tcs[0]);
			break;
		default:
			/* so then, we go up the strings until they diverge */
			/* I really am implementing bash in PHP. I am fucking insane. */
			$commandline=$precomplete;
			if(strpos($tocomplete,'/')!==false){
				$commandline.=substr($tocomplete,0,strrpos($tocomplete,'/')+1);
				$tocomplete=substr($tocomplete,strrpos($tocomplete,'/')+1);
			}
			$s=strlen($tocomplete);
			$done=false;
			do{
				$oc=false;
				foreach($tcs as $i => $p){
					if(!$oc) $oc=$p{$s};
					$c=$p{$s};
					if($c!=$oc || (strlen($p) < $s)){$done=true;$s--;break;}
				}
				$s++;
			}while(!$done);
			
			$commandline.=safe_escape(substr($tcs[0],0,$s));
			e('<p>');
			for($i=0;$i<count($tcs);$i++){
				e('<span class="linky" onclick="setcl(\''.safe_escape(substr($tcs[$i],$s)).'\');">'.$tcs[$i].'</span><br />');
			}
			e('</p>');
			break;
	}
}else{
	$commandline = $_GET['tc'];
}
?>