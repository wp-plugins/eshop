<?php
function eshop_process_template($templateFile) {
	global $wpdb;
	//processes style page forms
	if(!empty($_POST['templateFile'])){
		//update css file
    	$newfile = stripslashes($_POST['templateFile']);      	
		if(is_writeable($templateFile)) {
   			$f = fopen($templateFile, 'w+');
         	fwrite($f, $newfile);
        	fclose($f);
    		echo '<div id="message" class="updated fade"><p><strong>'.__('The Template Has Been Updated','eshop').'</strong></p></div>'."\n";
		} 
	}
	
	return;
}
function eshop_template_email(){
	//make sure options exist for the style page
	//config options
	$eshopurl=eshop_files_directory();

	$templateFile = $eshopurl['0'];
	$file1='order-recieved-email.tpl';
	$file2='customer-response-email.tpl';
	if(isset($_POST['choose'])||isset($_POST['edit'])){
		if((isset($_POST['template']) && $_POST['template']==1 )|| (isset($_POST['edit']) && $_POST['edit']==$file1)){
			$templateFile.=$file1;
			$editthis=$file1;
			$oemail=' selected="selected"';
		}else{
			$templateFile.=$file2;
			$editthis=$file2;
			$cemail=' selected="selected"';
		}
	}else{
		$templateFile.=$file1;
		$editthis=$file1;
		$oemail=' selected="selected"';
	}
	$name1=__('Automatic order email','eshop');
	$name2=__('Customer response email','eshop');
    
    if(!is_writeable($templateFile) && (!isset($_POST['choose'])||!isset($_POST['edit']))) {
  			echo '<div id="message" class="error fade"><p>'.__('<strong>Warning!</strong> The template file is not currently editable/writable! File permissions must first be changed.','eshop').'</p>
	 		</div>'."\n";
 	}
     $template=eshop_process_template($templateFile);

    
?>
<div class="wrap">
<h2><?php _e('eShop Email Templates','eshop'); ?></h2>
 <p><?php _e('Use this page to modify your default email templates','eshop'); ?>.</p> 
</div>
<div class="wrap">
<h2><?php _e('Choose template','eshop'); ?></h2>
<p><?php _e('Choose which email template you would like to alter.','eshop'); ?></p>
<form action="#edit_section" method="post" id="template_form" name="template">
 <fieldset>
  <legend><?php _e('Choose Template','eshop'); ?></legend>
  <label for="template">Edit</label>
  <select id="template" name="template">
  <?php
  	if(isset($oemail)){
  		echo '<option value="1"'.$oemail.'>'.$name1.'</option>';
  		echo '<option value="2">'.$name2.'</option>';
	}elseif(isset($cemail)){
		echo '<option value="1">'.$name1.'</option>';
  		echo '<option value="2"'.$cemail.'>'.$name2.'</option>';
	}
  ?>
  </select>
  	<input type="hidden" name="choose" value="c" />
   <p class="submit eshop"><input type="submit" value="<?php _e('Choose','eshop'); ?>" name="submit" /></p>
</fieldset>
</form>
</div>
<div class="wrap">
<h2 id="edit_section"><?php _e('Email Template Editor','eshop'); ?></h2>
 <p><?php _e('Use this simple file editor to modify the default email template file.','eshop'); ?></p>
 <form method="post" action="" id="edit_box">
  <fieldset>
   <legend><?php _e('Template File Editor.','eshop'); ?></legend>
   <label for="stylebox"><?php _e('Edit Template','eshop'); ?></label><br />
	<textarea rows="20" cols="80" id="stylebox" name="templateFile"><?php 
	if(!is_file($templateFile))
		$error = 1;

	if(!isset($error) && filesize($templateFile) > 0) {
		$f="";
		$f = fopen($templateFile, 'r');
		$file = fread($f, filesize($templateFile));
		echo $file;
		fclose($f);
	} else 
		_e('Sorry. The file you are looking for could not be found','eshop');
		?>
	</textarea>
	<input type="hidden" name="edit" value="<?php echo $editthis;?>" />
   <p class="submit eshop"><input type="submit" class="button-primary" value="<?php _e('Update Template','eshop'); ?>" name="submit" /></p>
  </fieldset>
</form>
</div>
	<?php 
	//end custom styling
	eshop_show_credits();
}
?>