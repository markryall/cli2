<?php
/*
ls
@@
Usage: $0
Synonyms: list, dir
Switches:
	--author : show author
	--hide=PATTERN : do not show entries matching PATTERN 
	-Q, --quote-name : enclose entry names in double quotes
	-r, --reverse : reverse order while sorting
	-R : recursive listing
Shows posts and subdirectories of the working directory, or of the specified path
@@
*/

require_once(CLI_DIR.'/lib/filesystem.inc.php');

if(isset($tokens[1])){
	$path = absolute($tokens[1]);
}else{
	$path = '/'.$_SESSION['path'];
}
$path = explode('/', path_expand($path));
$d = $root;
//err(var_export($path,true));
for($i = 0; $i < count($path); $i++){
	if($d && $path[$i] != ''){
		$d=@$d->children[$path[$i]];
		//err($path[$i]);
	}
}
//err('<pre>'.var_export($d,true).'</pre>');

if(!$d){
	err('directory is invalid: '.implode('/',$path));
}else{
	if(switchval('R')){
		e('<p>');
		traverse($d);
		e('</p>');
	}else{
		$entries = array();

		foreach($d->children as $dn){
			$name = $dn->name;
			if( !(switchval('hide') && fnmatch(switchval('hide'), $name)))
				$z='';
				if($dn->type == 'd') $z = '/';
				if($dn->type == 'l') $z = '@';
				$entries[] = array($name.$z, $dn->getlink(), $dn->id);	
		}
		if(count($entries) > 0){
//e('<pre>'.var_export($entries,true).'</pre>');
			$s = 0; $m = count($entries); $inc = 1;
			if(switchval('r') || switchval('reverse')){
				$s = count($entries); $m = -1; $inc = -1;
			}
			e('<table>');
			for($i = $s; $i != $m; $i += $inc){
				$id = $entries[$i][2];
				$lnk= strpos($entries[$i][0],'@')==strlen($entries[$i][0])-1;
				if($id && $id != "'latest'"){
					$post = get_post($id);
				}else{
					$post = false;
				}
				if(switchval('Q') || switchval('quote-name')){
					$entries[$i][0] = '&quot;'.$entries[$i][0].'&quot;';
				}
				if($post->ID){
					e("<tr onclick=\"".$entries[$i][1]."\"><td>$id</td><td class=\"linky\">".$post->post_title."</td><td>".strlen($post->post_content)."</td><td>".$post->post_date."</td>");
				}else{
					e("<tr onclick=\"".$entries[$i][1]."\"><td>".($lnk?'LNK':'DIR')."</td><td class=\"linky\">".$entries[$i][0]."</td><td></td><td></td>");
				   	e('<td>'.$post->post_date.'</td>');
			   	}
			   	if($post->ID){ 
			   		if(switchval('author')){
			   			$ud = get_userdata($post->post_author);
			   			e('<td>'.$ud->display_name.'</td>');
				  	}
				}
			   	e("</tr>");
			}
			e('</table>');
		}
	}
}
?>
