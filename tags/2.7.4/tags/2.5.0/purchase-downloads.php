<?php
if (!function_exists('eshop_downloads')) {
	function eshop_downloads($_POST){
		global $wpdb;
		$table = $wpdb->prefix ."eshop_downloads";
		$ordertable = $wpdb->prefix ."eshop_download_orders";
		$dir_upload = eshop_download_directory();
		$echo='';
	//download is handled via cart functions as it needs to
	//be accessible before anything is printed on the page

		if (isset($_POST['code']) && isset($_POST['email'])){
		/*
		Need to add in check about number of downloads here, including unlimited!
		*/
			$code=$wpdb->escape($_POST['code']);
			$email=$wpdb->escape($_POST['email']);
			$dlcount = $wpdb->get_var("SELECT COUNT(id) FROM $ordertable where email='$email' && code='$code' && downloads!='0'");
			if($dlcount>0){
				$tsize=0;
				$x=0;
				if($dlcount>1){
					$echo .= '<p class="jdl"><a href="#dlall">Download all files</a></p>';
				}
				$dlresult = $wpdb->get_results("Select * from $ordertable where email='$email' && code='$code' && downloads!='0'");
				foreach($dlresult as $dlrow){
					//download single items.
					$filepath=$dir_upload.$dlrow->files;
			   		$dlfilesize = @filesize($filepath);
			   		$tsize=$tsize+$dlfilesize;
			   		if($dlrow->downloads==1){
			   			$dlword='download';
			   		}else{
			   			$dlword='downloads';
			   		}
			   		$dltitle = (strlen($dlrow->title) >= 20) ? substr($dlrow->title,0,20) . "&#8230;" : $dlrow->title;
					$echo.='
					<form method="post" action="" class="dlproduct"><fieldset>
					<legend>'.$dltitle.' ('.check_filesize($dlfilesize).')</legend>
					<input name="email" type="hidden" value="'.$_POST['email'].'" />
					<input name="code" type="hidden" value="'.$_POST['code'].'" />
					<input name="id" type="hidden" value="'.$dlrow->id.'" />
					<input name="eshoplongdownloadname" type="hidden" value="yes" />
					<label for="ro">Number of downloads remaining</label>
					<input type="text" readonly="readonly" name="ro" class="ro" id="ro'.$x.'" value="'.$dlrow->downloads.'" />
					<input type="submit" class="button" id="submit'.$x.'" name="Submit" value="Download '.$dltitle.'" />
					</fieldset></form>
					';
					$x++;
					$size=0;
				}
				if($dlcount>1){
					//download all form.
					$echo.='
					<form method="post" action="" id="dlall"><fieldset>
					<legend>Download all files ('.check_filesize($tsize).') in one zip file.</legend>
					<input name="email" type="hidden" value="'.$_POST['email'].'" />
					<input name="code" type="hidden" value="'.$_POST['code'].'" />
					<input name="id" type="hidden" value="all" />
					<input name="eshoplongdownloadname" type="hidden" value="yes" />
					<p><input class="button" type="submit" id="submit" name="Submit" value="Download All Files" /></p>
					</fieldset></form>
					';
				}
			}else{
				$prevdlcount = $wpdb->get_var("SELECT COUNT(id) FROM $ordertable where email='$email' && code='$code'");
				if($dlcount==$prevdlcount){
					$error='<p>Either your email address or code is incorrect, please try again.</p>';
				}else{
					$error='<p>Your email address and code are correct, however you have no downloads remaining.</p>';
				}
				$echo .= eshop_dloadform($email,$code,$error);
			}
		}else{
			$echo .= eshop_dloadform($email,$code);
		}
		return $echo;
	}
}
//the standard log in form
function eshop_dloadform($email,$code,$error=''){
	$echo='';
	if($error!=''){
		$echo .= $error;
	}
	$echo .='
	<form method="post" action="" id="eshopdlform">
	<fieldset><legend>Enter Details</legend>
	<label for="email">Email:</label> 
	<input name="email" id="email" type="text" value="'.$email.'" /><br />
	<label for="code">Code:</label> 
	<input name="code" id="code" type="text" value="'.$code.'" /><br />
	<input type="submit" id="submit" class="button" name="Submit" value="Submit" />
	</fieldset>
	</form>
	';
	return $echo;

}
function check_filesize($size){
  if ($size == NULL){
     return "error";
  }
  $i=0;
  $iec = array("Bytes", "KB", "MB", "GB");
  while (($size/1024)>1) {
     $size=$size/1024;
     $i++;
  }
  $size=ceil($size);
  if($iec[$i]=='Bytes'){
  	return '&lt; 1Kb';
  }else{
  	return substr($size,0,strpos($size,'.')+3).$iec[$i];
  }
}
?>