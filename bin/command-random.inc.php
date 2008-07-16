<?php
/*
random
@@
Usage: $0
Switches: none
Displays a randomly chosen post.
@@
*/
		$q="SELECT `ID` AS c 
			FROM `".$wpdb->posts."` 
			WHERE (`post_status`='publish' OR `post_status`='static') AND `post_type`='post' 
			ORDER BY RAND() 
			LIMIT 1"; 
		$r=mysql_query($q);
		if(!$r){
			e(mysql_error().'<br><b>'.$q.'</b>');
		}else{
			if(mysql_num_rows($r)>0 && mysql_result($r,0,'c')){
				$_SESSION['current']=mysql_result($r,0,'c');
				cat($_SESSION['current']);
			}else{
				e('<p>No posts.</p>');
			}
		}
?>
