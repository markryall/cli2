<?php
/*
current
@@
Usage: $0
Synonyms: cursor
Switches: none
Displays the ID of the current post, or sets the cursor to the next highest existing post ID
@@
*/
		if(isset($tokens[1])){
			$q="SELECT MIN(`ID`) AS c FROM `".$wpdb->posts."` "
				."WHERE (`post_status`='publish' OR `post_status`='static') "
				."AND `ID` >= ".$tokens[1];
			$r=mysql_query($q);
			if($r && mysql_num_rows($r)==1){
				$_SESSION['current']=mysql_result($r,0,'c');
			}
		}
		e('<p>'.$_SESSION['current'].'</p>');
?>