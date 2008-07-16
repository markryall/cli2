<?php
/*
cloud
@@
Usage: $0 [regenerate]
Synonyms: none
Switches: none
Displays a "cloud" of word frequencies in the blog. "cloud regenerate" updates the cloud.
@@
*/
		$regen=false;
		if(isset($tokens[1]) && $tokens[1]=='regenerate'){
			$regen=true;
		}
		include('lib/cloud.php');
?>
