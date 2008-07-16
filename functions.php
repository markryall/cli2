<?php
include('cli.conf.php');
add_action('admin_menu', 'cli_add_theme_page');

/* widgets-ready, baby */
if ( function_exists('register_sidebar') )
    register_sidebar();
    
function cli_init_opts(){
	$cli=array(
		'welcome'			=> "Welcome. Type 'help' for assistance.",
		'debug'				=> '1',
		'gui_url'			=> '',
		'scroll_step'		=> "24",
		'last_resort'		=> '1',
		'fg_colour_red'	=>	"255",
		'fg_colour_green'	=> "208",
		'fg_colour_blue'	=>	"0",
		'bg_colour_red'	=>	"8",
		'bg_colour_green'	=>	"8",
		'bg_colour_blue'	=>	"8",
		'num_colours'		=>	"8",
		'br_colour_red'	=>	"8",
		'br_colour_green'	=>	"8",
		'br_colour_blue'	=>	"8",
		'br_width'			=> "20",
		'cursor_style'		=> "block",
		'cursor_blink_time' =>	"500",
		'static_help'		=> '0',
		'categories_in_root'		=> 'categories',
		'categories_as_tree'		=> 'tree',
		'font'				=> "Courier New",
		'social'			=> '1',
		'no_post_list'			=> '1',
		'sidebar'			=> 'none',
		'process_images'		=> '1',
		'authors_dir'			=> '0'
	);
	
	foreach($cli as $opt => $val){
		$sv = get_option(THEME_OPTION_PREFIX.$opt);
		if($sv===false){
			add_option(THEME_OPTION_PREFIX.$opt,$val);
		}else{	
			$cli[$opt]=$sv;
		}
	}
	return $cli;
}

function cli_save_opts($cli){
	foreach($cli as $opt => $val){
		update_option(THEME_OPTION_PREFIX.$opt,stripslashes($val));
	}
}

function cli_add_theme_page() {
	$cli=cli_init_opts();
	$ctp='cli_theme_page';
	if ( $_GET['page'] == basename(__FILE__) ) {
		if ( 'save' == $_POST['action'] ) {
			foreach($cli as $opt => $val){
				if(isset($_POST['cli_'.$opt])){
					$cli[$opt]=$_POST['cli_'.$opt];
				}else{
					$cli[$opt]='0';
				}
			}
			cli_save_opts($cli);
			$ctp='cli_theme_page_saved';
		}
	}
	add_theme_page('Customize CLI', 'CLI Options', 'edit_themes', basename(__FILE__), $ctp);
}

function cli_option($cli, $name, $type, $title, $size="20", $script=false){
?>
		<tr>
			<td><label for="cli_<?php echo $name ?>_id"><?php echo $title ?></label></td>
			<td><input type="<?php echo $type ?>" <?php 
			if ($type=='text') echo('size="'.$size.'"') 
			?>id="cli_<?php echo $name ?>_id" name="cli_<?php 
			echo $name ?>"  <?php 
				if($type ==	'checkbox'){
					echo('value="1" ');
					if($cli[$name]){
						echo('checked="checked"');
					}
				}else{
					echo('value="'.stripslashes($cli[$name]).'"');
				}
			if($script) echo " ".$script;		
			?> /></td>
		</tr>
<?php
}

function cli_colour_option($cli, $id, $title){
?>
		<tr>
			<td><label for="cli_<?php echo $id ?>_colour_id"><?php echo $title ?> colour</label></td>
			<td>R<input onchange="cc('<?php echo $id ?>')" type="text" size="3" maxlength="3" id="cli_<?php echo $id ?>_colour_id" name="cli_<?php 
			echo $id ?>_colour_red" value="<?php 
			echo $cli[$id.'_colour_red'] ?>" />
			G<input onchange="cc('<?php echo $id ?>')" type="text" size="3" maxlength="3" name="cli_<?php 
			echo $id ?>_colour_green" value="<?php 
			echo $cli[$id.'_colour_green'] ?>" />
			B<input onchange="cc('<?php echo $id ?>')" type="text" size="3" maxlength="3" name="cli_<?php 
			echo $id ?>_colour_blue" value="<?php 
			echo $cli[$id.'_colour_blue'] ?>" />
			<span id="sample_<?php echo $id ?>2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
<?php
}

function cli_theme_page_saved(){
?>
	<div style="text-align:center;background-color:lightgreen">Options saved.</div>
<?php
//var_export($_POST);
	cli_theme_page();
}

function cli_theme_page(){
	$cli=cli_init_opts();
	$vc=dirname(__FILE__).'/var/';
	$cc=is_writable($vc.'cache/cloud');
	$ic=is_writable($vc.'cache/images');
?>
<style type="text/css">textarea{width:100% !important;}</style>
<div style="margin:2em;">
<h1>CLI Theme Options</h1>
<div style="width:70%;margin-left:15%">
<h3>Directories:</h3>
<ul>
<li><?php 
echo $vc.'cache/cloud is ';
if(!$cc) echo '<span style="color:red">not</span> ';
echo 'writeable.'; ?></li>
<li><?php 
echo $vc.'cache/images is ';
if(!$ic) echo '<span style="color:red">not</span> ';
echo 'writeable.' ?></li>
</ul>

<form action="" method="post">
	<table>
		<tr>
			<td colspan="2">
<?php if(function_exists('user_can_richedit') &&
	function_exists('the_editor')){ ?>
				<fieldset id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
				<legend><h3><?php _e('Welcome Message') ?></h3></legend>
<?php the_editor($cli['welcome'],'cli_welcome'); ?>
				</fieldset>
<?php }else{ ?>
<textarea name="cli_welcome"><?php echo htmlspecialchars($cli['welcome']); ?></textarea>				
<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<span id="reg_status"><span></span></span>
				<input type="button" onclick="register();" value="Register this blog with the CLI Mothership" />
				<input type="button" onclick="unregister();" value="Unregister" />
			</td>
		</tr>
		<tr>
			<td>Sidebar</td>
			<td><select name="cli_sidebar">
				<option value="none" <?php if($cli['sidebar']=='none') echo 'selected="selected"' ?>>None</option>
				<option value="left" <?php if($cli['sidebar']=='left') echo 'selected="selected"' ?>>Left</option>
				<option value="right" <?php if($cli['sidebar']=='right') echo 'selected="selected"' ?>>Right</option>
			</select></td>
		</tr>
<?php
	cli_option($cli,'static_help','checkbox','Use Help Message (check to use usr/share/doc/help.html, uncheck to generate command list dynamically)'); 
	cli_option($cli,'social','checkbox','Social bookmarking links');
	cli_option($cli,'no_post_list','checkbox','Hide posts on front page');

	cli_option($cli,'gui_url','text','GUI URL (full URL, with <q>http://</q>)');
	cli_option($cli,'last_resort','checkbox','<q>Last resort</q> search of post titles if no matching command');
?><tr><td><h3>Images</h3></td></tr><?php
	cli_option($cli,'process_images','checkbox','Process images (monochrome/raster effect)');
	cli_option($cli,'num_colours','text','Number of colours for image processing', '3');
?><tr><td><h3>Colours</h3></td></tr><?php
	cli_colour_option($cli,'fg','Foreground');
	cli_colour_option($cli,'bg','Background');
	cli_colour_option($cli,'br','Border');
	cli_option($cli,'br_width','text','Border width (pixels)', '2', 'onchange="br()"');
?>
		<tr>
		<td colspan="2">
<input type="button" value="C64" onclick="preset('64');" />
<input type="button" value="VIC" onclick="preset('20');" />
<input type="button" value="ZX81" onclick="preset('zx');" />
<input type="button" value="LCD mono" onclick="preset('mono');" />
<input type="button" value="VT" onclick="preset('vt');" />
<input type="button" value="Paperwhite" onclick="preset('paperwhite');" />
			<div style="float:right;height:100px;width:150px" id="sample_br">
			<div style="position:relative;top:0;left:0;width:100px;height:100px;font-family:Monospace;" id="sample_bg"><span id="sample_fg">Lorem ipsum atque foobar<span id="clicsr">&nbsp;</span></span></div>
			</div>
		</td>
		</tr>
<tr><td><h3>Presentation</h3></td></tr>
<?php	
	cli_option($cli,'font','text','Preferred Font (Defaults to Courier New, Courier, Monospace in that order)', '20', 'onchange="ft()"');
	cli_option($cli,'scroll_step','text','Smooth scroll speed, pixels per 10ms (0 for no smooth scroll)',2);
?>


		<tr>
			<td><label for="cli_cursor_style_id">Cursor style</label></td>
			<td>
			<input type="radio" id="cli_cursor_style_id" name="cli_cursor_style" value="block"<?php
			if($cli['cursor_style']=='block') echo(' checked="checked"'); 
			?> /> Block
			<input type="radio" name="cli_cursor_style" value="underline"<?php
			if($cli['cursor_style']=='underline') echo(' checked="checked"'); 
			?> /> Underline
			</td> 
		</tr>
<?php
	cli_option($cli,'cursor_blink_time','text','Cursor blink (ms)', '3');	
?><tr><td><h3><q>Filesystem</q></h3></td></tr><?php
	cli_option($cli,'authors_dir','checkbox','Authors directory');

?>
		<tr>
			<td><label for="cli_categories_in_root_id">Location of categories</label></td>
			<td>
			<input type="radio" id="cli_categories_in_root_id" name="cli_categories_in_root" value="root"<?php
			if($cli['categories_in_root']=='root') echo(' checked="checked"'); 
			?> /> / (root)
			<input type="radio" name="cli_categories_in_root" value="categories"<?php
			if($cli['categories_in_root']=='categories') echo(' checked="checked"'); 
			?> /> /categories
			</td> 
		</tr>
		<tr>
			<td><label for="cli_categories_as_tree_id">Categories layout</label></td>
			<td>
			<input type="radio" id="cli_categories_as_tree_id" name="cli_categories_as_tree" value="tree"<?php
			if($cli['categories_as_tree']=='tree') echo(' checked="checked"'); 
			?> /> Tree
			<input type="radio" name="cli_categories_as_tree" value="flat"<?php
			if($cli['categories_as_tree']=='flat') echo(' checked="checked"'); 
			?> /> Flat
			</td> 
		</tr>
<?php
	cli_option($cli,'debug','checkbox','Debug output');
?>		
	</table>
	<div style="text-align:right">
	<input type="hidden" name="action" value="save" />
	<input type="submit" value="Save" />
	</div>
</form>
</div>
</div>
<script type="text/javascript">
//What, you don't like script outside the HEAD? Boo hoo.

	function preset(which){
		if(which=='64'){
			document.forms[0]['cli_fg_colour_red'].value=140;
			document.forms[0]['cli_fg_colour_green'].value=140;
			document.forms[0]['cli_fg_colour_blue'].value=255;
			document.forms[0]['cli_bg_colour_red'].value=8;
			document.forms[0]['cli_bg_colour_green'].value=8;
			document.forms[0]['cli_bg_colour_blue'].value=200;
			document.forms[0]['cli_br_colour_red'].value=140;
			document.forms[0]['cli_br_colour_green'].value=140;
			document.forms[0]['cli_br_colour_blue'].value=255;
			document.forms[0]['cli_br_width'].value=12;
			document.forms[0]['cli_num_colours'].value=16;
			document.forms[0]['cli_cursor_style'][0].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=500;
			document.forms[0]['cli_font'].value="Courier New";
		}
		if(which=='20'){
			document.forms[0]['cli_fg_colour_red'].value=0;
			document.forms[0]['cli_fg_colour_green'].value=0;
			document.forms[0]['cli_fg_colour_blue'].value=180;
			document.forms[0]['cli_bg_colour_red'].value=255;
			document.forms[0]['cli_bg_colour_green'].value=255;
			document.forms[0]['cli_bg_colour_blue'].value=255;
			document.forms[0]['cli_br_colour_red'].value=0;
			document.forms[0]['cli_br_colour_green'].value=255;
			document.forms[0]['cli_br_colour_blue'].value=0;
			document.forms[0]['cli_br_width'].value=12;
			document.forms[0]['cli_num_colours'].value=8;
			document.forms[0]['cli_cursor_style'][0].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=500;
			document.forms[0]['cli_font'].value="Courier New";
		}
		if(which=='mono'){
			document.forms[0]['cli_fg_colour_red'].value=0;
			document.forms[0]['cli_fg_colour_green'].value=0;
			document.forms[0]['cli_fg_colour_blue'].value=140;
			document.forms[0]['cli_bg_colour_red'].value=180;
			document.forms[0]['cli_bg_colour_green'].value=180;
			document.forms[0]['cli_bg_colour_blue'].value=255;
			document.forms[0]['cli_br_colour_red'].value=180;
			document.forms[0]['cli_br_colour_green'].value=180;
			document.forms[0]['cli_br_colour_blue'].value=255;
			document.forms[0]['cli_br_width'].value=5;
			document.forms[0]['cli_num_colours'].value=4;
			document.forms[0]['cli_cursor_style'][1].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=500;
			document.forms[0]['cli_font'].value="Courier New";
		}
		if(which=='zx'){
			document.forms[0]['cli_fg_colour_red'].value=32;
			document.forms[0]['cli_fg_colour_green'].value=32;
			document.forms[0]['cli_fg_colour_blue'].value=32;
			document.forms[0]['cli_bg_colour_red'].value=200;
			document.forms[0]['cli_bg_colour_green'].value=200;
			document.forms[0]['cli_bg_colour_blue'].value=200;
			document.forms[0]['cli_br_colour_red'].value=200;
			document.forms[0]['cli_br_colour_green'].value=200;
			document.forms[0]['cli_br_colour_blue'].value=200;
			document.forms[0]['cli_br_width'].value=0;
			document.forms[0]['cli_num_colours'].value=4;
			document.forms[0]['cli_cursor_style'][0].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=1000;
			document.forms[0]['cli_font'].value="Luxi Mono";
		}
		if(which=='vt'){
			document.forms[0]['cli_fg_colour_red'].value=0;
			document.forms[0]['cli_fg_colour_green'].value=240;
			document.forms[0]['cli_fg_colour_blue'].value=0;
			document.forms[0]['cli_bg_colour_red'].value=0;
			document.forms[0]['cli_bg_colour_green'].value=48;
			document.forms[0]['cli_bg_colour_blue'].value=0;
			document.forms[0]['cli_br_colour_red'].value=0;
			document.forms[0]['cli_br_colour_green'].value=48;
			document.forms[0]['cli_br_colour_blue'].value=0;
			document.forms[0]['cli_br_width'].value=20;
			document.forms[0]['cli_num_colours'].value=4;
			document.forms[0]['cli_cursor_style'][0].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=250;
			document.forms[0]['cli_font'].value="Courier New";
		}
		if(which=='amber'){
			document.forms[0]['cli_fg_colour_red'].value=240;
			document.forms[0]['cli_fg_colour_green'].value=220;
			document.forms[0]['cli_fg_colour_blue'].value=0;
			document.forms[0]['cli_bg_colour_red'].value=30;
			document.forms[0]['cli_bg_colour_green'].value=24;
			document.forms[0]['cli_bg_colour_blue'].value=0;
			document.forms[0]['cli_br_colour_red'].value=30;
			document.forms[0]['cli_br_colour_green'].value=24;
			document.forms[0]['cli_br_colour_blue'].value=0;
			document.forms[0]['cli_br_width'].value=20;
			document.forms[0]['cli_num_colours'].value=16;
			document.forms[0]['cli_cursor_style'][0].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=250;
			document.forms[0]['cli_font'].value="Courier New";
		}
		if(which=='paperwhite'){
			document.forms[0]['cli_fg_colour_red'].value=0;
			document.forms[0]['cli_fg_colour_green'].value=0;
			document.forms[0]['cli_fg_colour_blue'].value=0;
			document.forms[0]['cli_bg_colour_red'].value=255;
			document.forms[0]['cli_bg_colour_green'].value=255;
			document.forms[0]['cli_bg_colour_blue'].value=255;
			document.forms[0]['cli_br_colour_red'].value=255;
			document.forms[0]['cli_br_colour_green'].value=255;
			document.forms[0]['cli_br_colour_blue'].value=255;
			document.forms[0]['cli_br_width'].value=10;
			document.forms[0]['cli_num_colours'].value=16;
			document.forms[0]['cli_cursor_style'][1].checked=true;
			document.forms[0]['cli_cursor_blink_time'].value=750;
			document.forms[0]['cli_font'].value="Times New Roman";
		}
		init();
	}

	function br(){
		var bgs=document.getElementById('sample_bg').style;
		var brw=document.forms[0]['cli_br_width'].value;
		bgs.top=brw+'px';
		bgs.height=(100-brw)+'px';
		bgs.left=brw+'px';
		bgs.width=(150-brw)+'px';
	}
	function cc(what){
		var d=document.getElementById('sample_'+what);
		var d2=document.getElementById('sample_'+what+'2');
		var n='cli_'+what+'_colour_';
		var c=	'rgb('
			+document.forms[0][n+'red'].value+','
			+document.forms[0][n+'green'].value+','
			+document.forms[0][n+'blue'].value+')';
		if(what=='fg'){
			d.style.color=c;
		}else{
			d.style.backgroundColor=c;
		}
		d2.style.backgroundColor=c;
	}
	function ft(){
		document.getElementById('sample_fg').style.fontFamily=
			document.forms[0]['cli_font'].value;
	}
	function csr(onoff){
		var f='rgb('
					+document.forms[0]['cli_fg_colour_red'].value+','
					+document.forms[0]['cli_fg_colour_green'].value+','
					+document.forms[0]['cli_fg_colour_blue'].value+')';
		var b='rgb('
					+document.forms[0]['cli_bg_colour_red'].value+','
					+document.forms[0]['cli_bg_colour_green'].value+','
					+document.forms[0]['cli_bg_colour_blue'].value+')';
		var cr=document.getElementById('clicsr');
		if(document.forms[0]['cli_cursor_style'][0].checked){ //block
			cr.style.textDecoration="none";
			if(onoff){
				cr.style.color=f;
				cr.style.backgroundColor=b;
			}else{
				cr.style.color=b;
				cr.style.backgroundColor=f;
			}
		}else{
			cr.style.color=f;
			cr.style.backgroundColor=b;
			if(onoff){
				cr.style.textDecoration="underline";
			}else{
				cr.style.textDecoration="none";
			}
		}
		var blinktime=parseInt(document.forms[0]['cli_cursor_blink_time'].value)+10; // just to be safe (not 0)
		setTimeout('csr('+!onoff+');',blinktime); 
	}

	function register(){
		var mothership="http://thrind.xamai.ca/cli-mothership/register.php?";
		var myinterpreter="<?php echo base64_encode(get_bloginfo('stylesheet_directory').'/interpret.php'); ?>";
		var myblogname="<?php echo base64_encode(get_bloginfo('name')) ?>";
		var statimg=document.createElement('img');
		statimg.setAttribute('alt','(result image)');
		statimg.setAttribute('src',mothership+'i='+myinterpreter+'&n='+myblogname+'&op=register');
		document.getElementById('reg_status').replaceChild(statimg,document.getElementById('reg_status').firstChild);
		return;
	}
		
	function unregister(){
		var mothership="http://thrind.xamai.ca/cli-mothership/register.php?";
		var myinterpreter="<?php echo base64_encode(get_bloginfo('stylesheet_directory').'/interpret.php'); ?>";
		var myblogname="<?php echo base64_encode(get_bloginfo('name')) ?>";
		var statimg=document.createElement('img');
		statimg.setAttribute('alt','(result image)');
		statimg.setAttribute('src',mothership+'i='+myinterpreter+'&n='+myblogname+'&op=remove');
		document.getElementById('reg_status').replaceChild(statimg,document.getElementById('reg_status').firstChild);
		return;
	}
		
	function init(){-
		cc('fg');
		cc('bg');
		cc('br');
		br();
		ft();
		csr(true);
	}
	
	init();
</script>
<?php
}
?>
