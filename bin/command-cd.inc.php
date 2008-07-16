<?php
/*
cd
@@
Usage: $0 [path]
Synonyms: chdir
Switches: none
Change directory to user's home directory (guest home is /), or to specified path.
@@
*/
require_once(CLI_DIR.'/lib/filesystem.inc.php');

if(!isset($tokens[1]) || $tokens[1]=='~'){
	//change to home, or...
	$maybe_homedir='/authors/'.$username;
	if(path_exists($maybe_homedir)){
		$_SESSION['path']=$maybe_homedir;
	}else{	
		$_SESSION['path']='/';
	}
}else{
	$p=path_expand($tokens[1]);
	if(path_exists($p)){
		$_SESSION['path']=$p;
		$prompt=defaultprompt();
	}else{
//		err($p);
		e("<p>bash: cd: $tokens[1]: No such file or directory</p>");
	}
}

$_SESSION['path']=str_replace('//','/',$_SESSION['path']);
?>
