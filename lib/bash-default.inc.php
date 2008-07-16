<?php
/* bash-default.inc.php 

	separated out for nefarious reasons
*/
		$w=' 0 ';
		if(is_numeric($tokens[0])){
			$w="`ID`=".mysql_real_escape_string($tokens[0]);
		}else{
			if(get_option(THEME_OPTION_PREFIX.'last_resort') && strlen($line) > 2){
				$w="(`post_title` LIKE '%".mysql_real_escape_string($line)."%')";
			}else{
				$w="(`post_title` = '".mysql_real_escape_string($line)."')";
			}
		}
/** change this to use $wpdb->get_post or whatever it is **/

		$q="SELECT `ID`
			FROM `".$wpdb->posts."` 
			WHERE (`post_status`='publish' OR `post_status`='static') 
				AND ".$w." 
			ORDER BY `ID` ASC";
		$r=mysql_query($q);
		if($r && mysql_num_rows($r) > 0){
			if(mysql_num_rows($r) > 1){
				e('<p>'.mysql_num_rows($r).' posts found with "'.htmlspecialchars($cmd).'" in title:</p>');
				e('<table>');
				for($i=0;$i<mysql_num_rows($r);$i++){
					$post=get_post(mysql_result($r,$i,'ID'));
					e("<tr onclick=\"showpost('".$post->ID."')\"><td>$id</td><td class=\"linky\">".$post->post_title."</td><td>".strlen($post->post_content)."</td><td>".$post->post_date."</td>");
				}
				e('</table>');
			}else{
				$_SESSION['current']=mysql_result($r,0,'ID');
				cat($_SESSION['current']);
			}
		}else{
			if($cmd && !$suppress_bash_error){
				e('<p>Unrecognized command. Type \'help\' for assistance.</p>');
			}
		}
?>