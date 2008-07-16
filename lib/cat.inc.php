<?php
/* displays a post */
require_once(CLI_DIR.'/cli.conf.php');
/* do not include lib/utility.inc.php */
if(!isset($_SESSION['current'])){ // set current post if not set already
/* Cheers, Sim */ 
	$q="SELECT ID AS c 
		FROM `".$wpdb->posts."` 
		WHERE `post_date` IN 
			(SELECT MAX(`post_date`) 
			FROM `".$wpdb->posts."` 
			WHERE (`post_status`='publish' OR `post_status`='static'))"; 
	$r=mysql_query($q);
	$_SESSION['current']=mysql_result($r,0,'c');
}

function cat($id = 0, $pots = false, $pass=false){
	global $wpdb, $html, $post, $preview;
	if(!$pots){
		$pots = get_post($id);
	}else{
		$id = $post->ID;
	}
	$post=$pots;
//
	if(!$pass){
		$pass=stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]);
	}
	if(!empty($post->post_password)
		&& ($pass != $post->post_password ))
	{
		$_SESSION['current']=$id;
		$_SESSION['interpreter']='lib/pw.php';
	}else{
	//	
		
		if(!$post || 
			($post->post_status != 'publish' && !$preview)  
		){
			e('<p>No post with that ID.</p>');
		}else{
			$_SESSION['current']=$id;
			$authorinfo=get_userdata($post->post_author);
			if(
				strpos($post->post_content,'<form')!==false
			){
				e('<p>This post is unsuitable for display with the CLI. ');
				if(GUI_URL){
					$u=
					e('<a href="'.get_option(THEME_OPTION_PREFIX.'gui_url').'/?'.($status=='static'?'page_id':'p').'='.$id.'">Try the GUI</a>.');
				}
				e('</p>');
			}else{
				e('<div>');
				e('<h1>'.$post->post_title.'</h1>');
				e('<p>'.$post->post_date.' by '.$authorinfo->display_name.' in ');
	       	/*categories*/
				$cs=get_the_category($id);
				$ca=array();
				for($ci=0;$ci < count($cs) ;$ci++){
					$cd=$cs[$ci];
					$ca[]="<span onclick=\"showcat('".$cd->cat_ID
						."')\" class=\"linky\">".$cd->cat_name."</span>";
				}
				if($ca){
					e(implode(', ',$ca));
				}
				e(' (');
				switch($post->comment_count){
					case 0: e('no comments'); break;
					case 1: e('<span class="linky" onclick="showcomments();">1 comment</span>'); break;
					default: e('<span class="linky" onclick="showcomments();">'.$post->comment_count.' comments</span>');
				}
				e(') <a href="'.get_permalink($id).'">permalink</a> ');
				e(cli_edit_post_link("Edit This",$post));
				e('</p>');	
				$content=cli_get_the_content($post,'(more...)',0,'',$pass);
				//$content=preg_replace('/\s+/',' ',$content);
				$content=str_replace(array('<!--onlycli','/onlycli-->'),array('',''),$content);
				$content=preg_replace('/<!--nocli.*?\/nocli-->/','',$content);
				/* Greenify any images */
				if(get_option(THEME_OPTION_PREFIX.'process_images') 
					&& is_writable(CLI_DIR.'/'.IMAGE_CACHE_DIR) 
					&& function_exists('imagegif')){
					$content=monochromify_and_rasterize_images_in($content);
				}
				e($content);
				if(get_option(THEME_OPTION_PREFIX.'social')){
					e('<div style="text-align: right;">');
					e('<a href="http://del.icio.us/post?url='.urlencode(get_permalink($id)).'&amp;title='.addslashes($post->post_title).'" title="del.icio.us: '.addslashes($post->post_title).'">del.icio.us</a>&nbsp;');
					e('<a href="http://digg.com/submit?phase=2&amp;url='.urlencode(get_permalink($id)).'&amp;title='.addslashes($post->post_title).'" title="digg: '.addslashes($post->post_title).'">digg</a>&nbsp;');
					e('<a href="http://reddit.com/submit?url='.urlencode(get_permalink($id)).'&amp;title='.addslashes($post->post_title).'" title="reddit: '.addslashes($post->post_title).'">reddit</a>');
					e('</div>');
				}
				e('</div>');			
			}
		}
	}
}

function cli_edit_post_link($link = 'Edit This', $post) {
        if ( is_attachment() )
                return;

        if( $post->post_type == 'page' ) {
                if ( ! current_user_can('edit_page', $post->ID) )
                        return;
                $file = 'page';
        } else {
                if ( ! current_user_can('edit_post', $post->ID) )
                        return;
                $file = 'post';
        }

        $location = get_option('siteurl') . "/wp-admin/{$file}.php?action=edit&amp;post=$post->ID";
        return "<a href=\"$location\">$link</a>";
}

function monochromify_and_rasterize_images_in($content){
	$fg_colour=array(
		'red'		=>get_option(THEME_OPTION_PREFIX.'fg_colour_red'),
		'green'	=>get_option(THEME_OPTION_PREFIX.'fg_colour_green'),
		'blue'	=>get_option(THEME_OPTION_PREFIX.'fg_colour_blue')
	);
	
	$bg_colour=array(
		'red'		=>get_option(THEME_OPTION_PREFIX.'bg_colour_red'),
		'green'	=>get_option(THEME_OPTION_PREFIX.'bg_colour_green'),
		'blue'	=>get_option(THEME_OPTION_PREFIX.'bg_colour_blue')
	);
	$lighter=$fg_colour; 
	$darker=$bg_colour;
	if(grayvalue($lighter) < grayvalue($darker)){
		$lighter=$bg_colour; $darker=$fg_colour;
	}	
	preg_match_all('/(<img[^>]*>)/i',$content,$imgs);
	foreach($imgs[1] as $img){
		preg_match('/src\s*=\s*[\'"]([^\'"]*)[\'"]/i',$img,$src);
		preg_match('/alt\s*=\s*[\'"]([^\'"]*)[\'"]/i',$img,$alt);
		preg_match('/title\s*=\s*[\'"]([^\'"]*)[\'"]/i',$img,$tit);
		preg_match('/align\s*=\s*[\'"]([^\'"]*)[\'"]/i',$img,$ali);
		preg_match('/style\s*=\s*[\'"]([^\'"]*)[\'"]/i',$img,$sty);
		$r_cached_image=IMAGE_CACHE_DIR.'/'.md5($src[1]).'.gif';
		$a_cached_image=CLI_DIR.'/'.IMAGE_CACHE_DIR.'/'.md5($src[1]).'.gif';
		if(!file_exists($a_cached_image)){
			$type=strtolower(substr($src[1],strpos($src[1],'.')+1));
			if(!strpos($src[1],'://')){
				// is local
				if(substr($src[1],0,1)=='/'){
					//agh
					$imagestring=@file_get_contents($_SERVER['DOCUMENT_ROOT'].$src[1]);
					list($width, $height) = @getimagesize($_SERVER['DOCUMENT_ROOT'].$src[1]);
				}else{
					$imagestring=@file_get_contents(ABSPATH.$src[1]);
					list($width, $height) = @getimagesize(ABSPATH.$src[1]);
				}
			}else{
				$imagestring=@file_get_contents($src[1]);
				list($width, $height) = @getimagesize($src[1]);
			}
			if(!$imagestring) {err($_SERVER['DOCUMENT_ROOT'].$src[1]);continue;}
			$im=imagecreatefromstring($imagestring);
			$nc=get_option(THEME_OPTION_PREFIX.'num_colours');
			if (imageistruecolor($im)) {
				imagetruecolortopalette($im, true, $nc);
			}
			if (imagecolortransparent($im)== -1){
				imagecolorset($im,
					imagecolortransparent($im),
					$lighter['red'],
					$lighter['green'],
					$lighter['blue']
				);
			}
			/*set up palette*/
			for ($c = 0; $c < imagecolorstotal($im); $c++) {
				$col = imagecolorsforindex($im, $c);
				$gray = grayvalue($col);
				$gray = round($nc * $gray) / $nc;
				foreach(array('red', 'green', 'blue') as $component){
					${$component} = round($gray*($lighter[$component]-$darker[$component]))+$darker[$component];
				}
				imagecolorset($im, $c, $red, $green, $blue);
			}
			$bg=imagecolorallocate($im,$bg_colour['red'],$bg_colour['green'],$bg_colour['blue']);
	 		if (!$bg) $bg=imagecolorclosest($im,$bg_colour['red'],$bg_colour['green'],$bg_colour['blue']);
			$basename=CLI_DIR.'/'.IMAGE_CACHE_DIR."/tempgrn".md5($src[1]);
			for($blacken=7;$blacken>=0;$blacken--){
				imagegif($im,$basename.$blacken.'.gif');
				for($r=0;$r<$height;$r+=8){
					imageline ($im, 0, $blacken+$r, 
						$width, $blacken+$r, 
						$bg
					);
				}
			}
			$frames=array();
			$delays=array();
			for($f = 0; $f < 8; $f++){
				$frames[]=$basename.$f.'.gif';
				$delays[]=50;
			}
			require_once(CLI_DIR.'/lib/GifMerge.class.php');
			$ge=new GIFEncoder(
				$frames,
				$delays,
				1, // loop
				2, // disposal
				0,0,0, // transparent colour
				'url' // well, it should really be 'filename'
				);
			$anidata=$ge->getAnimation();
//e(htmlspecialchars($anidata));

			$afh=@fopen($a_cached_image,'w');
			fwrite($afh,$anidata);
			fclose($afh);
			for($f = 0; $f < 8; $f++){
				@unlink($frames[$f]);
			}
		}else{
			list($width, $height) = getimagesize($a_cached_image);
		}
		$content = str_replace($img,
			'<img src="'.CLI_URI.'/'
			.$r_cached_image.'" alt="'.$alt[1].'" title="'.$tit[1].'"'
			.' align="'.$ali.'" style="'.$sty.'"'
			.' height="'.$height.'" width="'.$width.'"'
			.' />',
			$content);
	}
	return $content;				
}

function grayvalue($colour){
	$gray = (
		  0.299 * $colour['red'] 
		+ 0.587 * $colour['green'] 
		+ 0.114 * $colour['blue']
		)/255; //this line is magic, stolen from php.net
	return $gray;
}




function cli_get_the_content($post, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '', $pass=false) {
	global $id, $more, $single, $withcomments, $page, $pages, $multipage, $numpages;
	global $preview;
	global $pagenow;
	$output = '';

	if ( !empty($post->post_password) ) { // if there's a password
		if ( $pass != $post->post_password ) {	// and it doesn't match the cookie
			$output = "(password protected)";
			return $output;
		}
	}

	$content = $post->post_content;
	if ( preg_match('/<!--more(.+?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = 1;
	$teaser = $content[0];
	if ( ($more) && ($stripteaser) )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<a id="more-'.$id.'"></a>'.$content[1];
		} else {
			$output = balanceTags($output);
			if ( ! empty($more_link_text) )
				$output .= ' <a href="'. get_permalink() . "#more-$id\" class=\"more-link\">$more_link_text</a>";
		}
			
	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace('/\%u([0-9A-F]{4,4})/e',	"'&#'.base_convert('\\1',16,10).';'", $output);

	$output = apply_filters('the_content', $output);
	$output = str_replace(']]>', ']]&gt;', $output);
	return $output;
}
