<script type="text/javascript">
var s=document.getElementById('scr');
document.write('<'+'div style=\"height:'+s.offsetHeight+'px\">&nbsp;<'+'/div>');
</script>
<h1><?php echo get_bloginfo('name'); ?></h1>
<?php
if(get_option(THEME_OPTION_PREFIX.'no_post_list')){
	$skip=true;
}else{
	$skip=false;
/*
	$qv = $wp_query->query_vars;
	//var_export($qv);
	$skip = (
	$qv['error'] || 
	!$qv['m'] || 
	!$qv['p'] || 
	!$qv['subpost'] || 
	!$qv['subpost_id'] || 
	!$qv['attachment'] || 
	!$qv['attachment_id'] || 
	!$qv['name'] || 
	!$qv['hour'] || 
	!$qv['static'] || 
	!$qv['pagename'] || 
	!$qv['page_id'] || 
	!$qv['second'] || 
	!$qv['minute'] || 
	!$qv['day'] || 
	!$qv['monthnum'] || 
	!$qv['year'] || 
	!$qv['w'] || 
	!$qv['category_name'] || 
	!$qv['author_name'] || 
	!$qv['feed']
	);
*/
}	 
if($wp_query->post_count == 1){
	if (have_posts()){
		include(get_template_directory().'/lib/cat.inc.php');
		while (have_posts()){
			the_post();
			cat($post->ID,$post);
			$_SESSION['current']=$post->ID;
		}
	}
}else if(count($posts) > 0 && !$skip){
	$posts=array_reverse($posts);		
	if (have_posts()){
		echo('<table>');
		while (have_posts()){
			the_post();
			$_SESSION['current']=$post->ID;
			e("<tr onclick=\"showpost('".$post->ID."')\"><td>"
				.$post->ID."</td><td class=\"linky\">"
				.$post->post_title."</td><td>"
				.$post->post_date."</td><td>"
				.'<a href="'.get_permalink($post->ID).'" title="Permalink">&raquo;</a>'
				."</td></tr>");
		}
		echo('</table>');
	}
	
}

if(isset($_GET['yoohoo'])){ // this is purely for the SEO people
	$q="SELECT `ID` FROM `".$wpdb->posts."` "
		."WHERE (`post_status`='publish') ORDER BY `post_date` ASC";
	$r=mysql_query($q);
	echo('<table>');
	for($i = 0; $i < mysql_num_rows($r); $i++){
		$post=get_post(mysql_result($r,$i,'ID'));
		echo('<tr><td><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></td>'
			.'<td>'.$post->post_date.'</td></tr>');
	}
	echo('</table>');
}
?>
<div id="welcome" style="visibility:hidden"><?php echo get_option(THEME_OPTION_PREFIX.'welcome'); ?></div>
<noscript>
<p>Sorry, the CLI requires JavaScript to work. Please turn on JavaScript<?php if (get_option(THEME_OPTION_PREFIX.'gui_url')){ ?>, 
or try the <a href="<?php echo get_option(THEME_OPTION_PREFIX.'gui_url') ?>">GUI Interface</a>.
<?php }else{ echo ".";} ?>
</p>
</noscript>
