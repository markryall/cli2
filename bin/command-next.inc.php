<?php
/*
next
@@
Usage: $0
Synonyms: n
Switches: none
Displays the post chronologically after the post currently being viewed.
@@
*/
		$q="SELECT MIN(`ID`) AS c FROM `".$wpdb->posts."` WHERE (`post_status`='publish' OR `post_status`='static')"
			." AND `ID`>".$_SESSION['current'];
		$r=mysql_query($q);
		if($r && mysql_num_rows($r)>0 && mysql_result($r,0,'c')	){
			$_SESSION['current']=mysql_result($r,0,'c');
			cat($_SESSION['current']);
		}else{
			e('<p><b>You are at the last post.</b></p>');
		}
?>