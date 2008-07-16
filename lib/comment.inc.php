<?php
/*	commenthandler.php
*/
if(isset($_GET['cancel'])){
	unset($_SESSION['comment_state']);
	unset($_SESSION['comment_data']);
	$_SESSION['interpreter']='default';
	$prompt=false;
	$commandline=false;
	e('<p>Cancelled.</p>');
}else{
	if(!isset($_SESSION['comment_state'])){
		$_SESSION['comment_state']='get_name';
	}
//	$user = wp_get_current_user();
	if ( $user->ID ) {
		$username = $wpdb->escape($user->display_name);
	}else{
		$username='guest';
	}
	$cs='get_name';
	switch($_SESSION['comment_state']){
		case "got_comment":
			do_comment_endgame();
			$prompt=false;
			$commandline='';
			break;
		case "got_webpage_get_comment":
			$_SESSION['comment_data']['url']=$_GET['c'];
			$prompt="Comment <em>(type Ctrl-x to finish)</em>: <br />";
			$multiline=true;
			$commandline='';
			$cs='got_comment';		
			break;
		case "got_email_get_webpage":
			$_SESSION['comment_data']['email']=$_GET['c'];
			$prompt="Web page: ";
			$commandline=$user->ID?$wpdb->escape($user->user_url):'';
			$cs='got_webpage_get_comment';		
			break;
		case "got_name_get_email":
			$_SESSION['comment_data']['author']=$_GET['c'];
			$prompt="Email: ";
			$commandline=$user->ID?$wpdb->escape($user->user_email):'';
			$cs='got_email_get_webpage';		
			break;
		case "get_name":
			$post=get_post($_SESSION['current']);
			e("<p>Leave a comment on \"".$post->post_title."\" (Ctrl-c cancels)</p>"); 
			$prompt="Name: ";
			$commandline=$username;
			$cs="got_name_get_email";
			break;
		default:
			err("Something bad has happened.");
	}
	$_SESSION['comment_state']=$cs;
}

function do_comment_endgame(){
	global $wpdb;
	/* using POST here for quasi-reuse of wp-comments-post */
	$_POST['comment']=urldecode($_POST['p']);
	$_POST['author']=$_SESSION['comment_data']['author']; 
	$_POST['email']=$_SESSION['comment_data']['email']; 
	$_POST['url']=$_SESSION['comment_data']['url']; 
	$_POST['comment_post_ID']=$_SESSION['current'];
//	err(var_export($_POST,true));
	include(CLI_DIR.'/lib/wp-comments-post.php');
	if($successful_comment){
		e('<p>Your comment has been accepted.</p>');
	}else{
		e('<p>Sorry, your comment was not accepted. Please try again.</p>');
	}				
	unset($_SESSION['comment_state']);
	unset($_SESSION['comment_data']);
	$_SESSION['interpreter']='default';
}
?>
