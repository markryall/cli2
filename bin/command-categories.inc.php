<?php
/*
categories [-r] [search_term]
@@
Usage: $0
Switches: 
	none
Displays a list of categories for the blog. The list is clickable, i.e. if a category name is clicked a list of posts tagged with that category is shown.
@@
*/
$cs=get_categories();
sort($cs);
$children=array();
foreach ($cs as $ix => $catobj){
	$children[$catobj->category_parent][]=$catobj;
}
//e('<pre>'.var_export($children,true).'</pre>');

e('<table>');
displaycat($children,0,0);
e('</table>');

function displaycat($catinfo, $parent, $level){
/*all the ternaries are because of the new 2.3 category system*/
	$indent='';
	if($level > 0) $indent='`';
	for($i = 0; $i < $level; $i++){
		$indent=$indent.'-';
	}
	foreach ($catinfo[$parent] as $ix => $obj){
		e("<tr onclick=\"showcat('".($obj->slug?$obj->slug:$obj->category_nicename)."');\">"
			."<td>".$indent.$obj->cat_ID."</td>"
			."<td class=\"linky\">".($obj->name?$obj->name:$obj->cat_name)."</td>"
			."<td>(".($obj->count?$obj->count:$obj->category_count).")</td></tr>");
		if($catinfo[$obj->cat_ID]){
			displaycat($catinfo, $obj->cat_ID, $level+1);
		}
	}
}
?>
