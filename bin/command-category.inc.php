<?php
/*
category
@@
Usage: $0 &lt;category_id>|&lt;category_slug>
Synonyms: none
Switches: none
Display posts in the specified category.
@@
*/
$cs=get_category_by_path($params);
//e('<pre>'.var_export($cs,true).'</pre>');

if(!isset($tokens[1])){
	e('<p>No category specified.</p>');
}else{
	$orderby="ID";
	if($wp_version < 2.3){
		if(is_numeric($tokens[1])){
			$q="SELECT `ID`,`post_name`, `post_title`, `post_date` FROM `".$wpdb->posts."` as p, `".$wpdb->post2cat."` as pc 
				WHERE (p.`post_status`='publish' OR p.`post_status`='static')
				AND p.`ID`=pc.`post_id`
				AND pc.`category_id`=".$tokens[1]." 
				ORDER BY `$orderby` ASC";
		}else{		
			$q="SELECT `ID`,`post_name`, `post_title`, `post_date` FROM `".$wpdb->posts."` as p, `".$wpdb->post2cat."` as pc, `".$wpdb->categories."` as c 
				WHERE (p.`post_status`='publish' OR p.`post_status`='static')
				AND p.`ID`=pc.`post_id`
				AND pc.`category_id`=c.`cat_ID`
				AND c.`category_nicename`='".$tokens[1]."' 
				ORDER BY `$orderby` ASC";
		}
	}else{
/*wp2.3*/
		if(is_numeric($tokens[1])){
			$q="SELECT `ID`,`post_name`, `post_title`, `post_date` FROM `".$wpdb->posts."` as p, `".$wpdb->term_relationships."` as tr 
				WHERE (p.`post_status`='publish' OR p.`post_status`='static')
				AND p.`ID`=tr.`object_id`
				AND tr.`term_taxonomy_id`=".$tokens[1]." 
				ORDER BY `$orderby` ASC";
		}else{		
			$q="SELECT `ID`,`post_name`, `post_title`, `post_date` FROM `".$wpdb->posts."` as p, `".$wpdb->term_relationships."` as tr, `".$wpdb->term_taxonomy."` as tt, `".$wpdb->terms."` as t 
				WHERE (p.`post_status`='publish' OR p.`post_status`='static')
				AND p.`ID`=tr.`object_id`
				AND tr.`term_taxonomy_id`=tt.`term_taxonomy_id`
				AND tt.`term_id`=t.`term_id`
				AND (t.`slug`='".$tokens[1]."' OR t.`name`='".$tokens[1]."') 
				ORDER BY `$orderby` ASC";
		}
	}
	$r=mysql_query($q);
	if(!$r){
		e(mysql_error()."<br><b>$q</b>");
	}else{
		if(mysql_num_rows($r)==0){
			e('<p>No category with that id, or specified category is empty.</p>');
		}else{
			e('<table>');
			for($i=0;$i<mysql_num_rows($r);$i++){
				list($id,$name,$title,$date)=mysql_fetch_row($r);
				e("<tr onclick=\"showpost('$id')\"><td>$id</td>"
					."<td class=\"linky\">$title</td><td>$date</td></tr>");
			}
			e('</table>');
		}
	}
}
?>
