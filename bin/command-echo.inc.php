<?php
/*
echo
@@
Usage: $0 [string]
Synonyms: none
Switches: none
Displays the argument.
@@
*/

if($stdin){
        e('<p>'.$stdin.'</p>');
}else{
        if($params{0} == '$'){
                $v=substr($params,1);
                e('<p>'.$v.' = '.$_SESSION[$v].'</p>');
        }else{
                e('<p>'.$params.'</p>');
        }
}
?>
