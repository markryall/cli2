<?php
/*
previous
@@
Usage: $0
Synonyms: prev, p
Switches: none
Displays the post chronologically before the post currently being viewed.
@@
*/
		$q="SELECT MAX(`ID`) AS c 
			FROM `".$wpdb->posts."` 
			WHERE 
				(`post_status`='publish' OR `post_status`='static')
				 AND `ID`<".$_SESSION['current'];
		$r=mysql_query($q);
		if($r && mysql_num_rows($r)>0 && mysql_result($r,0,'c')	){
			$_SESSION['current']=mysql_result($r,0,'c');
			cat($_SESSION['current']);
		}else{
			e('<p><b>You are at the first post.</b></p>');
		}
?>