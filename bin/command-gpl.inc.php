<?php
/*
gpl (syn. license)
@@
Usage: $0
Switches: none
Displays the GNU General Public license
@@
*/

	$fn='usr/share/doc/gpl.html';
	$fh=fopen($fn,'r');
	$f=fread($fh,filesize($fn));
	fclose($fh);
	e($f);
?>
