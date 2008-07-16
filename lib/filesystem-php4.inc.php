<?php
/* for php 4.x */

class FSnode{
        var $fsparent;
        var $children;
        var $type;
        var $name;
        var $id;

        function FSnode($n, $t, $p = false){
                $this->name = $n;
                $this->type = $t;  //f(ile), d(irectory), l(ink)
                $this->parent = &$p; //if false, this is root
                $this->children = array();
                $this->id = false;
        }

        function mdir($dirname){
                $d = new FSnode($dirname, 'd', $this);
                $this->children[$dirname] = &$d;
                return $d;
        }

        function mfiles($fdata){
                foreach($fdata as $filename => $fileid){
                        $f = new FSnode($filename,'f',$this);
                        $f->id = $fileid;
                        $this->children[$filename] = $f;
                }
        }

        function mlink($linkname, $target){
                $l = new FSnode($linkname, 'l', $this);
                $l->id = $target;
                $this->children[$linkname] = $l;
        }

        function abspath(){
                $i = $this;
                $path = '';
                do{
                        $path = $i->name.'/'.$path;
                        $i = $i->fsparent;
                }while($i);
                return $path;
        }

        function getlink(){
                switch($this->type){
                        case 'f':
                        case 'l':
                                return "showpost(".$this->id.");";
                        case 'd':
                                return "ls(String.fromCharCode(34)+'"
                                	.safe_escape($this->abspath())
                                	."'+String.fromCharCode(34));";
                        default:
                                return false;
                }
        }
}

function initFS(){
        $fsroot=new FSnode('','d');
	if(get_option(THEME_OPTION_PREFIX.'authors_dir')){
	        $authsnode=new FSnode('authors', 'd', &$fsroot);
	        $fsroot->children['authors']=&$authsnode;
	        $al = authorList();
	        foreach($al as $author){
			$authnode=new FSnode($author, 'd', &$authsnode);
	                $authnode->mfiles(postsByAuthor($author));
	        	$authsnode->children[$author]=$authnode;
	        }
        }
        $catsnode = &$fsroot;
        if(get_option(THEME_OPTION_PREFIX.'categories_in_root') == 'categories'){
                $catsnode = new FSnode('categories','d', &$fsroot);
                $fsroot->children['categories'] = &$catsnode;
        }
        if(get_option(THEME_OPTION_PREFIX.'categories_as_tree') == 'tree'){
           insertCategories(&$catsnode, 0);
        }else{ // categories in flat list
                if(function_exists('get_categories')){
                        $cl = get_categories();
                }else{
                        $cl = get_the_category();
                }
                foreach($cl as $category){
	                $catnode = new FSnode($category->cat_name,'d',$catsnode);
                        $catnode->mfiles(postsInCategory($category->cat_ID));
                        $catsnode->children[$category->cat_name] = $catnode;
                }
        }
        $fsroot->mfiles(pageList());
        $fsroot->mlink('latest', "'latest'");
        return $fsroot;
}

function insertCategories($filenode, $fsparent){
       	if(function_exists('get_categories')){
               	$cats = get_categories('child_of='.$fsparent);
	}else{
		$cats = get_the_category();
	}
        foreach($cats as $c){
                if($fsparent != $c->category_parent) continue; //you'd think I wouldn't have to check this, but I do. Crazy! The bug is in get_categories.
	        $catnode = new FSnode($c->cat_name,'d',$filenode);
                $catnode->mfiles(postsInCategory($c->cat_ID));
                $filenode->children[$c->cat_name]=$catnode;
                insertCategories(&$catnode, $c->cat_ID);
        }
}
?>
