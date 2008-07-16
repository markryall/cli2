<?php
/* Sadly this logic has to be duplicated because of some dying and redirecting that happens in
	the "vanilla" version */

//require( dirname(__FILE__) . '/wp-config.php' );

//nocache_headers();
$successful_comment=false;

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($status->comment_status) ) {
	cli_do_action('comment_id_not_found', $comment_post_ID);
//	exit;
} elseif ( 'closed' ==  $status->comment_status ) {
	cli_do_action('comment_closed', $comment_post_ID);
//	wp_die( __('Sorry, comments are closed for this item.') );
} elseif ( 'draft' == $status->post_status ) {
	cli_do_action('comment_on_draft', $comment_post_ID);
//	exit;
}else{
	
	$comment_author       = trim($_POST['author']);
	$comment_author_email = trim($_POST['email']);
	$comment_author_url   = trim($_POST['url']);
	$comment_content      = trim($_POST['comment']);
//err($comment_author);	
//err($comment_author_email);	
//err($comment_author_url);	
//err($comment_content);	
	// If the user is logged in
//	$user = wp_get_current_user();
	if ( $user->ID ) {
		$comment_author       = $wpdb->escape($user->display_name);
		$comment_author_email = $wpdb->escape($user->user_email);
		$comment_author_url   = $wpdb->escape($user->user_url);
	}
	if ( get_option('comment_registration') ) {
		cli_wp_die( __('Sorry, you must be logged in to post a comment.') );
	}	
	$comment_type = '';
	
	if ( get_option('require_name_email') && !$user->ID ) {
		if ( 6 > strlen($comment_author_email) || '' == $comment_author )
			cli_wp_die( __('Error: please fill the required fields (name, email).') );
		elseif ( !is_email($comment_author_email))
			cli_wp_die( __('Error: please enter a valid email address.') );
	//}else{
			
		if ( '' == $comment_content )
			cli_wp_die( __('Error: please type a comment.') );
		else {
			$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');
			
			$comment_id = wp_new_comment( $commentdata );
			
			$comment = get_comment($comment_id);
			if ( !$user->ID ) :
				setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
				setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
				setcookie('comment_author_url_' . COOKIEHASH, clean_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			endif;
			$successful_comment=true;
//			unset($GLOBALS);//brutal!
		}
	}
}
/*
$location = ( empty($_POST['redirect_to']) ? get_permalink($comment_post_ID) : $_POST['redirect_to'] ) . '#comment-' . $comment_id;
$location = apply_filters('comment_post_redirect', $location, $comment);

wp_redirect($location);
*/

function cli_wp_die($msg){
	e($msg);
}

function cli_do_action($what,$id){
	err($msg.': post id '.$id);	
}
?>
