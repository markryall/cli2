<?php
/*
editing
@@
Usage: $0 
Switches: none
Displays information on editing keys
@@
*/

e('<p>Single line mode:</p>
<table>
<tr><td>Up / Down</td><td>Forward / backward in command history</td></tr>
<tr><td>Left / Right</td><td>Cursor left / right</td></tr>
<tr><td>Home / End, Ctrl-A / Ctrl-E</td><td>Beginning / End of line</td></tr>
<tr><td>Ctrl-Home / Ctrl-End</td><td>Top / bottom of scroll buffer</td></tr>
<tr><td>Page Up / Page Down</td><td>Move up / down scroll buffer by 1 screen</td></tr>
<tr><td>Shift-Up / Down</td><td>Move up / down scroll buffer by 1 line</td></tr>
<tr><td>Del</td><td>Delete character right of cursor</td></tr>
<tr><td>Backspace</td><td>Delete character left of cursor</td></tr>
<tr><td>Ctrl-L</td><td>Clear screen</td></tr>
</table>

<p>Multi-line mode:</p>
<table>
<tr><td>Up / Down</td><td>Up / down one line</td></tr>
<tr><td>Left / Right</td><td>Cursor left / right</td></tr>
<tr><td>Home / End, Ctrl-A / Ctrl-E</td><td>Beginning / End of line</td></tr>
<tr><td>Del</td><td>Delete character right of cursor</td></tr>
<tr><td>Backspace</td><td>Delete character left of cursor</td></tr>
<tr><td>Ctrl-X</td><td>Finish editing</td></tr>
</table>');
?>
