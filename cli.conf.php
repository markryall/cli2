<?php
/*
        WordPress CLI theme
        R. McFarland 2006

        Configuration.
*/

/* If you're wondering, 99% of the config options are now in the
        CLI Options tab under Presentation. If you do not run WP 2.1.x,
        you may have to set your options directly in the code of functions.inc.php
 */

/* writable, for storing generated images */
define('IMAGE_CACHE_DIR', 'var/cache/images');
define('CLOUD_CACHE_DIR', 'var/cache/cloud');

/* If you want multiple installations of this theme, you'll have to change this to something unique */
define('THEME_OPTION_PREFIX','cli_');

/*****************************************************************************************/
/* don't edit below here                                                                 */
/*****************************************************************************************/

/* this is all fun until someone loses an eye */
/* find and include wp-blog-header.php */

if(!isset($wp)){
        $dir="";
        $sanity=20;
        while(!file_exists($dir.'wp-blog-header.php')&&$sanity){
                $dir.='../';
                $sanity--;
        }
        if(!$sanity){
                die('Cannot locate wp-blog-header.php! Try hard-coding the location.');
        }
        unset($sanity);
        include($dir.'wp-blog-header.php');
}

define('CLI_URI', get_bloginfo('stylesheet_directory'));
define('CLI_DIR', dirname(__FILE__));
define('SITE_URI', get_settings('siteurl'));

define('FG_COLOUR','rgb('
        .get_option(THEME_OPTION_PREFIX.'fg_colour_red').','
        .get_option(THEME_OPTION_PREFIX.'fg_colour_green').','
        .get_option(THEME_OPTION_PREFIX.'fg_colour_blue').')');
define('BG_COLOUR','rgb('
        .get_option(THEME_OPTION_PREFIX.'bg_colour_red').','
        .get_option(THEME_OPTION_PREFIX.'bg_colour_green').','
        .get_option(THEME_OPTION_PREFIX.'bg_colour_blue').')');
define('BORDER_COLOUR','rgb('
        .get_option(THEME_OPTION_PREFIX.'br_colour_red').','
        .get_option(THEME_OPTION_PREFIX.'br_colour_green').','
        .get_option(THEME_OPTION_PREFIX.'br_colour_blue').')');

if(function_exists('wp_get_current_user')){
        $user = wp_get_current_user();
}else{
        $user = get_currentuserinfo();
}
if ( $user->ID ) {
        $username = $wpdb->escape($user->display_name);
}else{
        $username = 'guest';
}

?>
