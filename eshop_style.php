<?php
function eshop_process_style($styleFile) {
	global $wpdb;
	//processes style page forms
	if(!empty($_POST['cssFile'])){
		//update css file
    	$newfile = stripslashes($_POST['cssFile']);      	
		if(is_writeable($styleFile)) {
   			$f = fopen($styleFile, 'w+');
         	fwrite($f, $newfile);
        	fclose($f);
    		echo ' <div id="message" class="updated fade"><p><strong>The Stylesheet Has Been Updated</strong></p></div>'."\n";
		} 
	}
	if(!empty($_POST['usestyle'])){
		update_option('eshop_style',$wpdb->escape($_POST['usestyle']));
		if($_POST['usestyle']=='yes'){
			$use='Default style has been applied.';
		}else{
			$use='Default style has been turned off.';
		}
		echo ' <div id="message" class="updated fade"><p><strong>'.$use.'</strong></p></div>'."\n";

	}
	return;
}
function eshop_form_admin_style(){
	//make sure options exist for the style page
	//config options
     $eshopurl=eshop_files_directory();

    $styleFile = $eshopurl['0'].'eshop.css';
    $style=eshop_process_style($styleFile);
    if(!is_writeable($styleFile)) {
  			echo ' <div id="message" class="error fade"><p><strong>Warning!</strong> The css file is not currently editable/writable! File permissions must first be changed.</p>
	   		</div>'."\n";
 	}
?>
<div class="wrap">
<h2>eShop Styles</h2>
 <p>Use this page to modify your default styling.</p> 
</div>
<div class="wrap">
<h2>Default Style</h2>
<p>Default style is used by default. You can edit this via the editor below, or choose not to use it.</p>
<form action="" method="post" id="style_form" name="style">
 <fieldset>
  <legend>Use Default Style</legend>
  <?php
  if(get_option('eshop_style')=='yes'){
  	$yes=' checked="checked"';
  	$no='';
  }else{
  	$no=' checked="checked"';
  	$yes='';
  }
  ?>
  <input type="radio" id="usestyle" name="usestyle" value="yes"<?php echo $yes; ?> /><label for="usestyle">Yes</label> 
  <input type="radio" id="nostyle" name="usestyle" value="no"<?php echo $no; ?> /><label for="nostyle">No</label>
  <p class="submit eshop"><input type="submit" value="Amend" name="submit" /></p>

</fieldset>
</form>
</div>
<div class="wrap">
<h2>Style Editor</h2>
 <p>Use this simple <abbr><span class="abbr" title="Cascading Style Sheet">CSS</span></abbr> file editor to modify the default style sheet file.</p>
 <form method="post" action="" id="edit_box">
  <fieldset>
   <legend>Style File Editor.</legend>
   <label for="stylebox">Edit Style</label><br />
	<textarea rows="20" cols="80" id="stylebox" name="cssFile"><?php 
	if(!is_file($styleFile))
		$error = 1;

	if(!$error && filesize($styleFile) > 0) {
		$f="";
		$f = fopen($styleFile, 'r');
		$file = fread($f, filesize($styleFile));
		echo $file;
		fclose($f);
	} else 
		echo 'Sorry. The file you are looking for could not be found';
		?>
	</textarea>
   <p class="submit eshop"><input type="submit" value="Update Style" name="submit" /></p>
  </fieldset>
</form>
</div>
	<?php 
	//end custom styling
	eshop_show_credits();
}
?>