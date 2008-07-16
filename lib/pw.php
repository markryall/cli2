<?php
//pw.php
// check password for a password-protected post


if(!isset($_SESSION['yo'])){
	$_SESSION['yo'] = 1;
	e('<p>This post is password protected.</p>');
	$prompt = 'Password: ';
	$password_input = true;
}else{
	unset($_SESSION['yo']);
	$_SESSION['interpreter'] = 'default';
	if(isset($_GET['cancel'])){
		e('<p>Cancelled.</p>');
	}else{
		$pw = $_GET['c'];
		if ( get_magic_quotes_gpc() )
			$pw = stripslashes($pw);
		
		// 10 days
		setcookie('wp-postpass_' . COOKIEHASH, $pw, time() + 864000, COOKIEPATH);
		require_once(CLI_DIR.'/lib/cat.inc.php');	
		require_once(CLI_DIR.'/lib/utility.inc.php');	
		cat($_SESSION['current'], false, $pw);
	}
}
?>