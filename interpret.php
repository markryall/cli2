<?php 
/* 
	WordPress CLI Theme
	R. McFarland 2006

	This is the interpreter for the blog commands. Some commands that do not 
	require DB usage are handled in Javascript (cli.js.php), e.g. cls, login.

	The scheme is very simple, no LALR parsers or anything going on here. Maybe
	someday when I make bash-over-AJAX... Anyhow, if you feel like making new
	commands, just make a PHP file called command-{your-command}.inc.php in the
	'usr/bin/' subdirectory. Remember to update 'usr/share/doc/help.html' to 
	reflect any new commands (or don't). 
*/ 

$html = ''; // collects HTML to display. don't use echo()! we're producing XML

session_start();
if(isset($_GET['PHPSESSID'])){
	session_id($_GET['PHPSESSID']);
}
require_once('cli.conf.php');
require_once(CLI_DIR.'/lib/utility.inc.php');

$responseId = $_GET['requestId'];

if($responseId==0) unset($_SESSION['telnet_interpeter']);


if(isset($_SESSION['telnet_interpreter'])){
	$qs=$_SERVER['QUERY_STRING'];
	$qs=preg_replace('/'.session_name().'=[0-9a-f]*/','',$qs);
	$url=$_SESSION['telnet_interpreter'].'&'.$qs;
	$xml=(file_get_contents($url));

	if(strtolower(trim($_GET['c']))=='exit'){
		$xml='';
		unset($_SESSION['telnet_interpreter']);
		e('<p>Connection closed.</p>');
		$_GET['c']='';
	}

}
if(!isset($_SESSION['telnet_interpreter'])){
	if(isset($_SESSION['path'])){
		$path = $_SESSION['path'];
	}else{
		$path = '/';
	}
	
	if($responseId == 0 || $_GET['c'] == 'reset'){$_SESSION = array();}
	
	
	$_SESSION['path'] = $path;
	
	if(!isset($_SESSION['interpreter'])){
		$_SESSION['interpreter']='default';
	}
	
	if($_SESSION['interpreter']=='default'){
		include('lib/bash.inc.php');
		/*tab completion doohickey */
		if(isset($_GET['tc'])){
			include('lib/tabcomplete.inc.php');
		}
	}
	/* not an 'else' here as bash needs to direct to new interpreter the first time */
	if($_SESSION['interpreter']!='default'){
		include($_SESSION['interpreter']);
	}	
	
	/************** WRITE BACK XML RESPONSE ************************/
	$xml='<'.'?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
	$xml.="<cliresponse>";
	$xml.='<responseId value="'.$responseId.'" />';
	/***HTML***/
	if($html){
		$s=stringThatDoesNotOccurIn($html);
		$h=explode($s,chunk_split($html,1024,$s)); //PHP5 has a better way to do this
		for($i = 0;$i < count($h); $i++){
			if($h){
				$xml.='<display><![CDATA['.$h[$i].']]></display>';
			}
		}
	}
	/***CLIENT-SIDE COMMANDS***/
	if($_SESSION['interpreter']!='default'){
		$xml.='<clientsidecommands value="off" />';
	}
	
	/***PROMPT***/
	if(!isset($prompt) || !$prompt){
		$prompt=defaultprompt();
	}
	if($prompt == 'none') $prompt='';
	$xml.='<prompt><![CDATA['.$prompt.']]></prompt>';
	
	/***COMMANDLINE***/
	// for tab completion
	if(isset($commandline) && $commandline){
		$xml.='<commandline>'.$commandline.'</commandline>';
	}
	
	/***MULTILINE INPUT MODE***/
	/* must be set explicitly each time */
	if(isset($multiline) && $multiline){
		$xml.='<multiline value="1" />';
	}
	
	/***CLIENT-SIDE SPECIAL COMMAND HANDLER***/
	if(isset($sch)){
		if($sch){
			$s=stringThatDoesNotOccurIn($sch);
			$h=explode($s,chunk_split($sch,1024,$s)); //PHP5 has a better way to do this
			for($i = 0;$i < count($h); $i++){
				if($h){
					$xml.='<specialcommandhandler><![CDATA['.$h[$i].']]></specialcommandhandler>';
				}
			}
		}else{
			$xml.='<specialcommandhandler><![CDATA[0]]></specialcommandhandler>';
		}
	}
	
	if(isset($password_input)){
		$xml.='<dotecho value="1" />';
	}	
	/***END XML***/
	$xml.="</cliresponse>\n";
}
header('Content-Type: text/xml',true,200);
echo($xml);

/***** NO OUTPUT PAST THIS LINE *****/
?>