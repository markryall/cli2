<?php
// filesystem.inc.php

if(substr(PHP_VERSION,0,1)=='4'){
	require(CLI_DIR.'/lib/filesystem-php4.inc.php');
}else{
	require(CLI_DIR.'/lib/filesystem-php5.inc.php');
}


function absolute($path){
        if($path{0} != '/'){
                $path='/'.$_SESSION['path'].'/'.$path;
        }
        $path = str_replace('//','/', $path);
        return $path;
}

function path_expand($p){
        global $username;
        if($p{0} == '~'){
                if($p == '~' || $p{1} == '/'){
                        $p = str_replace('~', '/authors/'.$username, $p);
                }else{
                        $p = str_replace('~', '/authors/', $p);
                }
        }
        $p = absolute($p);
        $p = explode('/',$p);

        $newpath = array();
        foreach($p as $c){
                if($c == '..' && count($newpath > 0)){
                        array_pop($newpath);
                }elseif($c == '.'){
                        continue;
                }elseif($c != ''){
                        array_push($newpath, $c);
                }
        }
        $p = '/'.implode('/', $newpath);
        return $p;
}

function path_exists($path){
        global $root;
        $path = absolute($path);
        $path = explode('/', $path);
        $d=$root;
        for($i = 0; $i < count($path); $i++){
                if($path[$i] != ''){
                        $hmm = @$d->children[$path[$i]];
                        if($hmm && $hmm->type == 'd'){
                                $d = $hmm;
                        }else{
                                $d=false;
                                continue;
                        }
                }
        }
        if(!$d) return false;
        return $d;
}

function fs_tab_completions($t){
        $t = path_expand($t);
        $d = dirname($t);
        $b = basename($t);
        if($filenode = path_exists($t)){
                $b='';
        }elseif(!$filenode = path_exists($d)){
                return false;
        }
        $candidates = array_keys($filenode->children);
        $result = array();
        foreach($candidates as $c){
                if($b == '' || strpos($c, $b) === 0){
                        if($filenode->children[$c]->type == 'd') $c.='/';
                        $result[] = $c;
                }
        }
        if($result)
                return array_unique($result);
        return $result;
}

/* these should all return array(name => js link, ...) */

function authorIDs(){ // returns array of all (author) users
        global $wpdb;
        $authors = array();
        $authors = $wpdb->get_col("SELECT DISTINCT post_author FROM $wpdb->posts");
        ksort($authors);
        return $authors;
}

function authorList(){
        $aids = authorIDs();
        $a = array();
        foreach($aids as $id){
                $u = get_userdata($id);
                $a[] = $u->display_name;
        }
        sort($a);
        return $a;
}

function pageList(){ // list of all 'pages' (not posts) [+ blogroll?]
        global $wpdb;
        $q = "SELECT `ID` FROM $wpdb->posts
                WHERE `post_type`='page' AND `post_status`='publish'";
        $pageIDs = $wpdb->get_col($q);
        $pages = array();
        if($pageIDs){
	        foreach($pageIDs as $id){
	                $post = get_post($id);
                	$pages[$post->post_title] = $id;
	        }
        }
        return $pages;
}

function postsByAuthor($a){ // author nicename
        global $wpdb;
        $result = array();
        $q = "SELECT p.`post_title` as t, p.`ID` as i
                FROM $wpdb->posts as p, $wpdb->users as u
                WHERE `post_status`='publish'
                AND u.ID=p.post_author
                AND u.display_name='$a'";
        $r = $wpdb->query($q);
        for($i = 0; $i < $r; $i++){
                $row = $wpdb->get_row(null, ARRAY_A, $i);
                $result[$row['t']] = $row['i'];
        }
        return $result;
}

function postsInCategory($cid){ // category 
	global $wpdb;
	global $wp_version;

	$result = array();
	if($wp_version < 2.3){
 	       $q = "SELECT p.`post_title` as t, p.`ID` as i
       	         FROM $wpdb->posts as p,
       	                  $wpdb->categories as c,
       	                  $wpdb->post2cat as p2
       	         WHERE p.`post_status`='publish'
       	         AND p2.post_ID=p.ID
       	         AND c.cat_ID='$cid'
       	         AND c.cat_ID=p2.category_id";
	}else{
			$q="SELECT DISTINCT p.`post_title` as t, p.`ID` as i 
				FROM `".$wpdb->posts."` as p, `".$wpdb->term_relationships."` as tr, `".$wpdb->term_taxonomy."` as tt, `".$wpdb->terms."` as ts 
				WHERE (p.`post_status`='publish' OR p.`post_status`='static')
				AND p.`ID`=tr.`object_id`
				AND tr.`term_taxonomy_id`=$cid";
	}
	$r=mysql_query($q);
	while($row = mysql_fetch_assoc($r)){
   	$result[$row['t']] = $row['i'];
	}
	return $result;
}

function traverse($d, $depth = 0){ // for recursive listing
        for($i = 0; $i < $depth; $i++){
                e('&nbsp;&nbsp;&nbsp;&nbsp;');
        }
        e('<span class="linky" onclick="'.$d->getlink().'">'.$d->name.'/:</span>');
        e('<br />');
        foreach($d->children as $dn){
                if($dn->type == 'd'){
                        traverse($dn, $depth + 1);
                }else{
                        for($i = 0;$i <= $depth; $i++){
                                e('&nbsp;&nbsp;&nbsp;&nbsp;');
                        }
                        e('<span class="linky" onclick="'.$dn->getlink().'">'.$dn->name);
                        if($dn->type == 'l')e('@');
                        e('</span><br />');
                }
        }
}

$root=initFS();
?>
