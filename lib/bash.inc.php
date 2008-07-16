<?php
/* bash.inc.php */
require_once(CLI_DIR.'/lib/cat.inc.php');

if(!isset($_GET['cancel'])){
	if(!isset($_SESSION['current'])){ // set current post if not set already
	/* Cheers, Sim */ 
		$q="SELECT ID AS c 
			FROM `".$wpdb->posts."` 
			WHERE `post_date` IN 
				(SELECT MAX(`post_date`) 
				FROM `".$wpdb->posts."` 
				WHERE (`post_status`='publish' OR `post_status`='static'))"; 
		$r=mysql_query($q);
		$_SESSION['current']=mysql_result($r,0,'c');
	}
	/* command line parsing */
	$prompt="(taint)";	
	$full_line=trim(stripslashes($_GET['c']));
	$full_line=do_escape($full_line);

	/* hash quoted strings (" > ') */
	$stdnoi = stringThatDoesNotOccurIn($full_line);
	$quotedstrings=array();
	if (preg_match_all('/(".*?")/',$full_line,$qs)){
		foreach($qs[1] as $q){
			$hash=md5($q);
			$full_line=str_replace($q,$stdnoi.$hash,$full_line);
			$quotedstrings[$hash]=$q;		
		}	
	}
	if (preg_match_all("/('.*?')/",$full_line,$qs)){
		foreach($qs[1] as $q){
			$hash=md5($q);
			$full_line=str_replace($q,$stdnoi.$hash,$full_line);
			$quotedstrings[$hash]=$q;		
		}	
	}

	$line=$full_line;

	$chaincmd=false;
	$chains=explode(';',$full_line);
	$line=array_shift($chains);
	$chaincmd=implode(';',$chains);
	$chaincmd=unpackquotes($chaincmd,$quotedstrings);
	$chaincmd=un_escape($chaincmd);		
		
	$pipecmd=false;
	$pipes=explode('|',$line);
	$line=array_shift($pipes);
	$pipecmd=implode('|',$pipes);
	$pipecmd=unpackquotes($pipecmd,$quotedstrings);
	$pipecmd=un_escape($pipecmd);

	/* at this point $line should just have command [switches] [operands] */
	
	$tokens=preg_split('/\s+/',$line);
	
	$cmd=strtolower(array_shift($tokens));
	$params=unpackquotes(implode(' ',$tokens),$quotedstrings);;
	$params=un_escape($params);
	$line=unpackquotes($line, $quotedstrings);	
	$line=un_escape($line);
	$switches=array();
	$tmptokens=array();
	foreach($tokens as $tk){
		if($tk{0}=='-'){
			if($tk{1}=='-'){ // it's a --option type switch
				$ex=explode('=',substr($tk,2),2);
				$switches[$ex[0]]=($ex[1]?$ex[1]:true);
			}else{ //it's a -laR buncha switches
				for($i=1; $i < strlen($tk); $i++){
					$switches[$tk{$i}]=true;
				}
			}
		}else{
			$tmptokens[]=un_escape(unpackquotes($tk,$quotedstrings));
		}
	}
	$tokens=$tmptokens;
	array_unshift($tokens,$cmd);
	
	/* aliases */
	include(CLI_DIR.'/lib/bash.aliases.inc.php');
	if(isset($aliases[$cmd])) $cmd=$aliases[$cmd];

	if($params=='--help' || $params=='-?' || $params=='-h'){
		$tokens[1] = $cmd;
		$cmd='help';
	}

	$cmd=un_escape($cmd);
	
	if(strpos($cmd,'/')){ // Fudging a little. Oh stop whining, it's not *really* bash after all
		$cmd=substr($cmd,strrpos($cmd,'/')+1);
	}		

	/* say $PATH=/usr/bin:/bin */
	/* and no, the user can't change that!*/
	
	if(file_exists(CLI_DIR.'/usr/bin/command-'.$cmd.'.inc.php')){
		include(CLI_DIR.'/usr/bin/command-'.$cmd.'.inc.php');
	}else if(file_exists(CLI_DIR.'/bin/command-'.$cmd.'.inc.php')){
		include(CLI_DIR.'/bin/command-'.$cmd.'.inc.php');
	}else{
		include(CLI_DIR.'/lib/bash-default.inc.php');
	}
	if($prompt=='(taint)')$prompt=defaultprompt();
}	

if($pipecmd){
//err('piped to '.$pipecmd);
	$_GET['c']=$pipecmd;
	$stdin=$html;
	$html='';
	include(__FILE__);
}

if($chaincmd){ 
	$_GET['c']=$chaincmd;
	include(__FILE__);
}

?>