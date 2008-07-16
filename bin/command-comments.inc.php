<?php
/*
comments
@@
Usage: $0
Synonyms: none
Switches: none
Displays comments on the current post.
@@
*/
		$q="SELECT `comment_author`, `comment_author_url`, `comment_date`, `comment_content`
			FROM `".$wpdb->comments."`
			WHERE `comment_approved`='1' AND `comment_post_ID`=".$_SESSION['current']
			." ORDER BY `comment_date` ASC";
		$r=mysql_query($q);
		if(!$r || mysql_num_rows($r)==0){
			e('<p>No comments for this post.</p>');
		}else{
			e('<ol>');
			for($i=0;$i<mysql_num_rows($r);$i++){
				list($author,$url,$date,$content)=mysql_fetch_row($r);
				e('<li>');
				if($url){
					e('<a href="'.$url.'" target="_blank">'.$author.'</a>');
				}else{
					e($author);
				}
				e(' '.$date.' ');
				e(cli_edit_comment_link());
				e('<br />');
				e(format_to_post(nl2br($content)));
				e('</li>');
			}
			e('</ol>');
		}

function cli_edit_comment_link($link = 'Edit This', $before = '', $after = '') {
        global $post, $comment;

        if( $post->post_type == 'page' ){
                if ( ! current_user_can('edit_page', $post->ID) )
                        return;
        } else {
                if ( ! current_user_can('edit_post', $post->ID) )
                        return;
        }

        $location = get_option('siteurl') . "/wp-admin/comment.php?action=editcomment&amp;c=$comment->comment_ID";
        return $before . "<a href='$location'>$link</a>" . $after;
}

?>