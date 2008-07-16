<?php
/*
first
@@
Usage: $0
Synonyms: f
Switches: none
Displays and moves the cursor to the blog's first post
@@
*/
		$q="SELECT MIN(`ID`) AS c 
			FROM `".$wpdb->posts."` 
			WHERE (`post_status`='publish' 
				OR `post_status`='static')"; 
		$r=mysql_query($q);
		if(mysql_num_rows($r)>0 && mysql_result($r,0,'c')	){
			$_SESSION['current']=mysql_result($r,0,'c');
			cat($_SESSION['current']);
		}
?>