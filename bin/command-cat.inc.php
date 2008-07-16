<?php
/*
cat
@@
Usage: $0 [post_id|page_id|post_title|title_search]
Synonyms: read, show, type
Switches: none
Displays a post. $0 by itself shows the <em>current</em> post (if there is no current post, the last post is shown). You may specify a post by supplying the post or page ID number, the exact post title, or depending on the CLI configuration, a portion of the title.

@@
*/
		if(isset($tokens[1])){
			if(is_numeric($tokens[1])){
				cat($tokens[1]);
			}else{
				/*this is pretty stinky. If the argument isn't numeric
					we just fall through to the default, but suppress
					any error message.  */
				$tokens[1]=basename($tokens[1]);
				$cmd=$tokens[1];
				array_shift($tokens);
				$suppress_bash_error=true; //crap hack
				include(CLI_DIR.'/lib/bash-default.inc.php'); 
			}
		}else{
			cat($_SESSION['current']);
		}
?>