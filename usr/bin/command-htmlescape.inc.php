<?
/*
htmlescape
@@
Usage: $0
Switches: none
Applies PHP's htmlspecialchars() to stdin
@@
*/
/*
	This command is here purely to illustrate output piping.
	Try ls|htmlescape or latest|htmlescape.
	
	An enterprising soul might make something like "topdf" to create
	PDFs on the fly. Crazy idea!
*/

if(!isset($stdin)){
	err('no input<br/>');
}else{
	e(htmlspecialchars($stdin));
}