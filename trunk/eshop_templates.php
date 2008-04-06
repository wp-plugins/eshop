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
    		echo ' <div id="message" class="updated fade"><p><strong>The Template Has Been Updated</strong></p></div>'."\n";
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
		if($_POST['template']==1 || $_POST['edit']==$file1){
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
	$name1='Automatic order email';
	$name2='Customer responce email';
    
    if(!is_writeable($templateFile) && (!isset($_POST['choose'])||!isset($_POST['edit']))) {
  			echo ' <div id="message" class="error fade"><p><strong>Warning!</strong> The template file is not currently editable/writable! File permissions must first be changed.</p>
	 		</div>'."\n";
 	}
     $template=eshop_process_template($templateFile);

    
?>
<div class="wrap">
<h2>eShop Email Templates</h2>
 <p>Use this page to modify your default email templates.</p> 
</div>
<div class="wrap">
<h2>Choose template</h2>
<p>Choose which email template you would like to alter.</p>
<form action="#edit_section" method="post" id="template_form" name="template">
 <fieldset>
  <legend>Choose Template</legend>
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
   <p class="submit eshop"><input type="submit" value="Choose" name="submit" /></p>
</fieldset>
</form>
</div>
<div class="wrap">
<h2 id="edit_section">Email Template Editor</h2>
 <p>Use this simple file editor to modify the default email template file.</p>
 <form method="post" action="" id="edit_box">
  <fieldset>
   <legend>Template File Editor.</legend>
   <label for="stylebox">Edit Template</label><br />
	<textarea rows="20" cols="80" id="stylebox" name="templateFile"><?php 
	if(!is_file($templateFile))
		$error = 1;

	if(!$error && filesize($templateFile) > 0) {
		$f="";
		$f = fopen($templateFile, 'r');
		$file = fread($f, filesize($templateFile));
		echo $file;
		fclose($f);
	} else 
		echo 'Sorry. The file you are looking for could not be found';
		?>
	</textarea>
	<input type="hidden" name="edit" value="<?php echo $editthis;?>" />
   <p class="submit eshop"><input type="submit" value="Update Template" name="submit" /></p>
  </fieldset>
</form>
</div>
	<?php 
	//end custom styling
	eshop_show_credits();
}
?>