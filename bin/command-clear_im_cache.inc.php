<?php
/*
clear_im_cache
@@
Usage: $0
Synonyms: none
Switches: none
Clears the image cache directory. Use after changing the theme colours so that images will match.
@@
*/
	$d=opendir(IMAGE_CACHE_DIR);
	if($d){
		while($f=readdir($d)){
			if(substr($f,-3)=='gif'){
				unlink(IMAGE_CACHE_DIR.'/'.$f);
			}
		}
		closedir($d);
		e("<p>Cleared.</p>");
	}else{
		err("<p>Failed.</p>");
	}
?>
