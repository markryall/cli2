<?php
/*
last
@@
Usage: $0 [num]
Synonyms: latest, l
Switches: none
Displays and moves the cursor to the most recent (public) post. If [num] is supplied, lists the last [num] posts.
@@
*/
if(!$tokens[1]){
	/* Cheers, Sim */ 
	$q="SELECT ID AS c 
		FROM `".$wpdb->posts."` 
		WHERE `post_date` IN 
			(SELECT MAX(`post_date`) 
			FROM `".$wpdb->posts."` 
			WHERE (`post_status`='publish' OR `post_status`='static'))"; 
	$r=mysql_query($q);
	if(mysql_num_rows($r)>0 && mysql_result($r,0,'c')){
		$_SESSION['current']=mysql_result($r,0,'c');
		cat($_SESSION['current']);
	}
}else{
	$q="SELECT `ID`,`post_name`, `post_title`, `post_date`, `post_content` FROM `".$wpdb->posts."` "
		."WHERE (`post_status`='publish'  OR `post_status`='static') ".$search." ORDER BY `post_date` DESC LIMIT ".mysql_real_escape_string($tokens[1]+0);
	$r=mysql_query($q);
	if(mysql_num_rows($r)>0){
		e('<table>');
		for($i=0;$i<mysql_num_rows($r);$i++){
			list($id,$name,$title,$date,$content)=mysql_fetch_row($r);
			e("<tr onclick=\"showpost('$id')\"><td>$id</td><td class=\"linky\">$title</td><td>".strlen($content)."</td><td>$date</td></tr>");
		}
		e('</table>');
	}else{
		e('<p>No matching posts.</p>');
	}
}
?>