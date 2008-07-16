<?php
/*
grep
@@
Usage: $0 [search_terms]
Synonyms: find, search
Switches: none
Searches the blog for occurrences of [search_terms].
@@
*/

$search_query = addslashes_gpc($params);
$search = ' AND (';
$search_query = preg_replace('/, +/', ' ', $search_query);
$search_query = str_replace(',', ' ', $search_query);
$search_query = str_replace('"', ' ', $search_query);
$search_query = trim($search_query);
$n = '%';
$s_array = explode(' ',$search_query);
$search .= '((post_title LIKE \''.$n.$s_array[0].$n.'\') OR (post_content LIKE \''.$n.$s_array[0].$n.'\'))';
for ( $i = 1; $i < count($s_array); $i = $i + 1) {
	$search .= ' AND ((post_title LIKE \''.$n.$s_array[$i].$n.'\') OR (post_content LIKE \''.$n.$s_array[$i].$n.'\'))';
}
$search .= ' OR (post_title LIKE \''.$n.$search_query.$n.'\') OR (post_content LIKE \''.$n.$search_query.$n.'\')';
$search .= ')';
$q="SELECT `ID`,`post_name`, `post_title`, `post_date` FROM `".$wpdb->posts."` "
	."WHERE (`post_status`='publish'  OR `post_status`='static') ".$search." ORDER BY `post_date`";
$r=mysql_query($q);
if(mysql_num_rows($r)>0){
	e('<table>');
	for($i=0;$i<mysql_num_rows($r);$i++){
		list($id,$name,$title,$date)=mysql_fetch_row($r);
		e("<tr onclick=\"showpost('$id')\"><td>$id</td><td class=\"linky\">$title</td><td>$date</td></tr>");
	}
	e('</table>');
}else{
	e('<p>No matching posts.</p>');
}
?>