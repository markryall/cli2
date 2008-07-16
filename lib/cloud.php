<?php
/*
	WordPress CLI Theme
	R. McFarland, 2006
	
	This generates a tag cloud based on the contents of your blog.
	Set the $chaff array in the cloud_parse function to contain words 
	you don't want counted.
	Call with $regen=true to regenerate the cloud, otherwise it will
	return the cached cloud. From the CLI, 
	use 'cloud regenerate' at the prompt.

	Heavily modified from 
	http://www.prism-perfect.net/archive/php-tag-cloud-tutorial/ 
*/

//require_once('../../../../wp-blog-header.php');
$cachefile=CLI_DIR.'/'.CLOUD_CACHE_DIR.'/cloudcache.html';

function cloud_parse($query){
	global $wpdb;
	$raw=$wpdb->get_col($query,0);
	$words=array();
	$chaff=array(null,
		'the', 'and', 'that', 'with', 'this', 'for', 'if', 'than' ,'then', 'quot', 'ccedil', '100n', 'you', 
		'your'
		);
	$txt="";
	for($i=0;$i<count($raw); $i++){
		$txt.=$raw[$i];
	}
	$txt=str_replace("\n"," ", $txt);
	$txt=preg_replace('/<[^>]+?'.'>/',' ',$txt);
	$txt=preg_replace('/[\!&;\s"-$\(\)\{\},\.><\/=\*:\?\-\[\]\\\]/',' ',$txt);
	$txt=preg_replace('/\'(s|m|ll|ld|ve|re)?\s/',' ',$txt);
	$txt=preg_replace('/\s\'/',' ',$txt);
	$txt=preg_replace('/&[^\s]{1,5};/',' ',$txt);
	$txt=preg_replace('/(^|$)/',' ',$txt);
	$txt=preg_replace('/\s+/',' ',$txt);
	$txt=preg_split('/ /',$txt,-1,PREG_SPLIT_NO_EMPTY);
	$counts=array_count_values($txt);
	foreach($counts as $w=>$n){
		$w=trim($w);
		if(preg_match('/[a-zA-Z]/',$w) && !array_search($w,$chaff) && strlen($w)>2) {
			if(isset($words[$w])){
				$words[$w]=$n;
			}else{
				$words[$w]+=$n;
			}
		}
	}
	return $words;
}

if(!is_writable($cachefile)){
	err($cachefile.' is not writable!');
}else{
	if(!file_exists($cachefile) || (isset($regen) && $regen)) {
		$words=cloud_parse("SELECT LCASE(CONCAT(`post_title`, ' ', `post_content`, ' ')) as body FROM ".$wpdb->posts
			." WHERE `post_status`='publish' OR `post_status`='static'");	
		$max_size = 250; // max font size in %
		$min_size = 70; // min font size in %
		
		// get the largest and smallest array values
		$max_qty = max(array_values($words));
		$min_qty = min(array_values($words));
		// find the range of values
		$spread = $max_qty - $min_qty;
		if (0 == $spread) { // we don't want to divide by zero
		$spread = 1;
		}
		
		// determine the font-size increment
		// this is the increase per word quantity (times used)
		$step = ($max_size - $min_size)/($spread);
		
		$cfh=fopen($cachefile,'w');
		if(!$cfh){
			err('Could not open '.$cachefile.' for writing.');
		}else{
			fwrite($cfh,'<div id="cloud">');
			$end=min(count($words),200);
		   arsort($words);
			reset($words);
			for($i=0;$i<$end;$i++){
				$word=key($words);
				$kept[key($words)]=$words[$word];
				next($words);
			}
			ksort($kept);
			foreach ($kept as $word => $value) {
				if($value>=$forcemin){
					$vs=$value;
					$size = $min_size + (($vs - $min_qty) * $step);
					$size = ceil($size);
					fwrite($cfh, '<span class="linky" onclick="autosearch(\''
						.str_replace("'","\\'",$word)
						.'\');" style="text-decoration:none;font-size:'
						.$size.'%"');
					fwrite($cfh, ' title="'.$value.' occurences of '.$word.'"');
					fwrite($cfh, '>'.$word.'</span> ');
				}
			}
			fwrite($cfh,'</div>');
			fclose($cfh);
		}
	}
	e(file_get_contents($cachefile));
}
?>
