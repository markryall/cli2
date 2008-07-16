<?php 
/* for php 5.x */

class FSnode{
        var $fsparent;
        var $children;
        var $type;
        var $name;
        var $id;

        function FSnode($n,$t,$p=false){
                $this->name=$n;
                $this->type=$t;  //f(ile), d(irectory), l(ink)
                $this->fsparent=$p; //if false, this is root
                $this->children=array();
                $this->id=false;
        }

        function mdir($dirname){
                $d=new FSnode($dirname,'d',$this);
                $this->children[$dirname]=$d;
                return $d;
        }

        function mfiles($fdata){
                foreach($fdata as $filename => $fileid){
                        $f=new FSnode($filename,'f',$this);
                        $f->id = $fileid;
                        $this->children[$filename]=$f;
                }
        }

        function mlink($linkname, $target){
                $l=new FSnode($linkname, 'l', $this);
                $l->id=$target;
                $this->children[$linkname]=$l;
        }

        function abspath(){
                $i=$this;
                $path='';
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
                                return "showpost($this->id);";
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
	        $authsnode=$fsroot->mdir('authors');
	        $al=authorList();
	        foreach($al as $author){
	                $authnode=$authsnode->mdir($author);
	                $authnode->mfiles(postsByAuthor($author));
	        }
        }
        $catsnode=$fsroot;
        if(get_option(THEME_OPTION_PREFIX.'categories_in_root')=='categories'){
                $catsnode=$fsroot->mdir('categories');
        }
        if(get_option(THEME_OPTION_PREFIX.'categories_as_tree')=='tree'){
           insertCategories($catsnode,0);
        }else{ // categories in flat list
                if(function_exists('get_categories')){
                        $cl = get_categories();
                }else{
                        $cl = get_the_category();
                }           foreach($cl as $category){
                $catnode=$catsnode->mdir($category->name?$category->name:$category->cat_name);
                $catnode->mfiles(postsInCategory($category->cat_ID));
        }
   }
   $fsroot->mfiles(pageList());
   $fsroot->mlink('latest',"'latest'");
   return $fsroot;
}

function insertCategories($fsnode,$parent){	
	$cats=get_categories(array('child_of'=>$parent));
	$fsnode->mfiles(postsInCategory($parent));
   foreach($cats as $c){
   	//$c = $cats[$i];
      if($parent != $c->category_parent) continue; //you'd think I wouldn't have to check this, but I do. Crazy! The bug is in get_categories.
      	$catnode = $fsnode->mdir($c->cat_name);
      	insertCategories($catnode, $c->cat_ID);
   }
}
?>