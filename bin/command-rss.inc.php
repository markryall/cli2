<?php
/*
rss
@@
Usage: $0
Switches: none
Display link to this blog's RSS feed.
@@
*/
	e('<p><a href="'.get_bloginfo('rss_url').'">Click here for the RSS feed.</a></p>');
?>