<?php
/*
uname
@@
Usage: $0 
Synonyms: none
Switches: -nva
Show information about the blog.
@@
*/
if($switches['n'] || $switches['a']){
	e(strtolower(str_replace(' ','-',get_bloginfo('name'))).' ');
}
e('WordPress ');
if($switches['v'] || $switches['a']){
	e($wp_version);
}
?>