<?php
/*
links
@@
Usage: $0
Switches: none
Displays links, aka blogroll
@@
*/
ob_start();
?>
<ul>
<?php
get_links_list();
?>
</ul>
<?php
e(ob_get_contents());
ob_end_clean();
?>
