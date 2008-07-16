<?php
/*
hello
@@
Usage: $0 [name]
Synonyms: none
Switches: none
Say hello to the nice man.
@@
*/
/* 
	Example custom command 

	Input is split into tokens ($tokens[0] is 'hello',
	$tokens[1] et seq. are "parameters" following 'hello',
	i.e. $tokens ~== argv. To use everything after the command in toto,
	use $params.
	
	Output is done via the 'e()' function. This is because we're really
	producing XML, so we have to package it up at the end. If you'd like some
	debugging output, try err().
*/

if(count($tokens)==2){
	e('<p>My name isn\'t '.$tokens[1].'!</p>');
}elseif(count($tokens)>2){
	e('<p>Your <em>mother</em> can '.$params.'!</p>');
}else{
	e('<p>Hello to you too.</p>');
}
?>
