<?php
function eshop_read_filesize($size){
  if ($size == NULL){
     return "error";
  }
  $i=0;
  $iec = array("Bytes", "KB", "MB", "GB");
  while (($size/1024)>1) {
     $size=$size/1024;
     $i++;
  }
  if($iec[$i]=='Bytes'){
  	return '&lt; 1Kb';
  }else{
  	return substr($size,0,strpos($size,'.')+3).$iec[$i];
  }
}

function eshop_check_brokenlink($file){
	$file_exists = @fopen($file, "r");

	if (!$file_exists){ 
		 return '<span class="missing">'.__('Missing','eshop').'</span>';
	}else{ 
		 return '<span class="available">'.__('Available','eshop').'</span>';
		 fclose($file_exists);
	 }
	fclose($file_exists);
	return false;
}
function eshop_contains_files(){
	global $wpdb;
	$contains='';
	$indir[]='';
	$table = $wpdb->prefix ."eshop_downloads";
	$rows=$wpdb->get_results("SELECT files FROM $table");
	foreach($rows as $row){
		$indir[]=$row->files;
	}
	if ($handle = opendir(eshop_download_directory())) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != ".htaccess" && $file != ".htpasswd" && !in_array($file,$indir)) {
				$contains[]=$file;
			}
		}
		closedir($handle);
	}
	return $contains;
}

function eshop_downloads_manager() {
	global $wpdb;
	$table = $wpdb->prefix ."eshop_downloads";
	$ordertable = $wpdb->prefix ."eshop_download_orders";
	$dir_upload=eshop_download_directory();
	$atitle='';
	if(isset($_POST['up'])){
		//borrowed this bit from wordpress
		$file_error_strings = array( false,
				__( "The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
				__( "The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
				__( "The uploaded file was only partially uploaded." ),
				__( "No file was uploaded." ),
				__( "Missing a temporary folder." ),
				__( "Failed to write file to disk." ));
		$error='';
		$new_name = "";
		if($_POST['title']!=''){
			if(function_exists('check_upload_size')){
				//for MU
				check_upload_size($_FILES["upfile"]);
			}
			$replace = array("'", "\"","&"," ");
			$file_name = str_replace($replace, "_", $_FILES["upfile"]["name"]);
			if(trim($_FILES["upfile"]["name"]) == "") {
				$error.="<p>".__('No file indicated','eshop')."</p>";
			}
			if(!file_exists($dir_upload.$file_name) || $_POST['overwrite']=='yes'){
				if(@is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
					if(!file_exists($dir_upload.$file_name)) $newfile='y';
					if(move_uploaded_file($_FILES["upfile"]["tmp_name"], $dir_upload.$file_name)){
						$success='<p>'.__('File moved','eshop').'</p>';
					}else{
						$error.='<p>'.__('Failed to move file','eshop').'</p>';
					}
				} else {
					$error.="<p>".__('Error uploading file','eshop')." " . $_FILES["upfile"]["name"] . " <strong>".$file_error_strings[$_FILES["upfile"]["error"]]."</strong></p>";
				}
			}else{
					$error.="<p>".__('Error uploading file','eshop')." " . $_FILES["upfile"]["name"] . " ".__('it <strong>already exists!</strong>','eshop')."</p>";
			}
		}else{
			$error.='<p>'.__('A title must be provided.','eshop').'</p>';
		}
		if(isset($success) && !isset($newfile)){
			$entfile=$wpdb->escape($file_name);
			$dafile=$wpdb->get_var("SELECT id FROM $table WHERE files='$entfile'");
			$enttitle=$wpdb->escape($_POST['title']);
			$wpdb->query("UPDATE $table SET title='$enttitle',added=NOW() WHERE id=$dafile");
			echo '<div id="message" class="updated fade"><p>' . $_FILES["upfile"]["name"] . " ".__('has successfully overwritten existing file','eshop').'</p></div>';

		}elseif($error==''){ //ie a successful upload
			$enttitle=$wpdb->escape($_POST['title']);
			$entfile=$wpdb->escape($file_name);
			$wpdb->query("INSERT INTO $table (title,added,files) VALUES ('$enttitle',NOW(),'$entfile')");

			echo '<div id="message" class="updated fade"><p>' . $_FILES["upfile"]["name"] . " ".__('has successfully uploaded','eshop').'</p></div>';
		}else{ //ie a failed upload
			echo '<div id="message" class="error fade">'.$error.'</div>';
			$atitle=$_POST['title'];
		}
		
		unset($_GET['edit']);
	}


	if (isset($_POST['editdelete'])) {
		// deleting entry
		$delid=$wpdb->escape($_POST['delid']);
		$delfile=$wpdb->get_var("SELECT files FROM $table WHERE id =$delid");
		$filepath=$dir_upload.$delfile;
		@unlink($filepath);
		$wpdb->query("DELETE FROM $table WHERE id = $delid");
		echo '<div id="message" class="updated fade"><p>'.__('File deleted successfully','eshop').'</p></div>';
		unset($_GET['edit']);
	}  
	
	//when edit a file this is the bit that gets used.
	if(isset($_POST['editamend'])){
		if(is_numeric($_POST['downloads']) && is_numeric($_POST['purchases']) && $_POST['title']!=''){
			//add in mysql update here
			$query= 'UPDATE '.$table.' SET title = "'.$wpdb->escape($_POST['title']).'", downloads = "'.$wpdb->escape($_POST['downloads']).'", purchases = "'.$wpdb->escape($_POST['purchases']).'"  WHERE id = "'.$wpdb->escape($_POST['id']).'"';
			$wpdb->query("$query");
			echo '<div id="message" class="updated fade"><p>'.__('File updated successfully','eshop').'</p></div>';
			unset($_GET['edit']);
		}else{
			//error handling
			if($_POST['title']==''){
				$error.='<li>'.__('The title for the file cannot be blank!','eshop').'</li>';
			}
			if(!is_numeric($_POST['downloads'])){
				$error.='<li>'.__('Downloads should to be a number!','eshop').'</li>';
			}
			if(!is_numeric($_POST['purchases'])){
				$error.='<li>'.__('Purchases should to be a number!','eshop').'</li>';
			}

			echo '<div id="message" class="error fade"><p>'.__('Some errors were found:','eshop').'</p><ul>'.$error.'</ul></div>';
		}
	}
	
	if(isset($_GET['eshop_orphan'])){
		if(is_array(eshop_contains_files())){
			foreach(eshop_contains_files() as $filename){
				$file=$wpdb->escape($filename);
				list($title, $ext) = split('[.]', $filename);
				$title=$wpdb->escape($filename);
				$wpdb->query("INSERT INTO $table (title,added,files) VALUES ('$title',NOW(),'$file')");
			}
		}
	}
	
	if(isset($_GET['edit'])){
		$id=$wpdb->escape($_GET['edit']);
		if($wpdb->get_var("SELECT title FROM $table WHERE id =$id")!=''){
		//ie exists
			//echo '<div id="message" class="updated fade"><p>found it</p></div>';
			$row=$wpdb->get_row("SELECT * FROM $table WHERE id =$id");
			
			?>
			<div class="wrap">
			<h2><?php _e('Edit File details','eshop'); ?></h2>
			<table id="listing" summary="<?php _e('downloadable file details','eshop'); ?>">
			<caption><?php _e('File details','eshop'); ?></caption>
			<thead>
			 <tr>
			  <th id="edid"><?php _e('ID','eshop'); ?></th>
			  <th id="edtitle"><?php _e('Title','eshop'); ?></th>
			  <th id="edsize"><?php _e('Size','eshop'); ?></th>
			  <th id="edfile"><?php _e('File name','eshop'); ?></th>
			  <th id="eddate"><?php _e('Upload Date','eshop'); ?></th>
			  <th id="eddown"><?php _e('Downloads','eshop'); ?></th>
			  <th id="edpurc"><?php _e('Purchases','eshop'); ?></th>
			 </tr>
			 </thead>
			 <tbody>
			 <?php
				$filepath=eshop_download_directory().$row->files;
			   $size = @filesize($filepath);
			   $label = (strlen($row->title) >= 20) ? substr($row->title,0,20) . "&#8230;" : $row->title;
			   echo "<tr>\n";
			   echo '<td id="redid'.$row->id.'" headers="edid">#'.$row->id."</td>\n";
			   echo '<td headers="edtitle redid'.$row->id.'">'.$label."</td>\n";
			   echo '<td headers="edsize redid'.$row->id.'">'.eshop_read_filesize($size)."</td>\n";
			   echo '<td headers="edfile redid'.$row->id.'">'.$row->files."</td>\n";
			   echo '<td headers="eddate redid'.$row->id.'">'.$row->added."</td>\n";
			   echo '<td headers="eddown redid'.$row->id.'">'.$row->downloads."</td>\n";
			   echo '<td headers="edpurc redid'.$row->id.'">'.$row->purchases."</td>\n";
			   echo "</tr>\n";
			 ?>
			 </tbody>
			</table>
			<?php
			$metatable=$wpdb->prefix ."postmeta";
			for($x=1;$x<=get_option('eshop_options_num');$x++){
				$metakeys[]="meta_key='_Download ".$x."'";
			}
			if(sizeof($metakeys)>0)
				$meta_keys='('.implode(' OR ',$metakeys). ') AND';
			else
				$meta_keys='';
			
			$checkproduct = $wpdb->get_var("SELECT COUNT(post_id) FROM $metatable WHERE ".$meta_keys." meta_value='$id'");
			if($checkproduct>0){
				$myrows=$wpdb->get_results("SELECT DISTINCT post_id FROM $metatable WHERE ".$meta_keys." meta_value='$id'");
				echo '<p class="productassociation">'.__('This file is associated with the following product pages:','eshop').'</p>';
				echo '<ul class="productpages">';
				foreach($myrows as $myrow){
					echo '<li><a href="page.php?action=edit&amp;post='.$myrow->post_id.'" title="edit '.get_the_title($myrow->post_id).'">'.get_the_title($myrow->post_id).'</a></li>';
				}
				echo '</ul>';
			}
			?>
			<form method="post" action="" id="downloadedit">
			<fieldset><legend><?php _e('Amend File details','eshop'); ?></legend>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />

			<label for="filetitle"><?php _e('Title','eshop'); ?></label><input type="text" name="title" id="filetitle" size="35" value="<?php echo $row->title; ?>" /><br />
			<label for="downloads"><?php _e('Downloads','eshop'); ?></label><input type="text" name="downloads" id="downloads" size="5" value="<?php echo $row->downloads; ?>" /><br />
			<label for="purchases"><?php _e('Purchases','eshop'); ?></label><input type="text" name="purchases" id="purchases" size="5" value="<?php echo $row->purchases; ?>" /><br />

			</fieldset>
			  <p class="submit"><input type="submit" name="editamend" value="<?php _e('Amend details','eshop'); ?>" class="button" /></p>
			</form>
			</div>
			<?php
			if($checkproduct==0){
			?>
				<div class="wrap">
				<h2><?php _e('Delete','eshop'); ?></h2>
				<p><?php _e('You can only delete this file if it is <strong>not</strong> associated with a product page.','eshop'); ?></p>
				<form method="post" action="" id="downloaddelete">
				<input type="hidden" name="delid" value="<?php echo $row->id; ?>" />
				<p class="submit"><input type="submit" name="editdelete" value="<?php _e('Delete File','eshop'); ?> '<?php echo $row->title; ?>'" class="button" /></p>
				</form>
				</div>
			<?php
			}
		
		}else{
		//ie does not exist
			echo '<div id="message" class="error fade"><p>'.__('Product not found','eshop').'.</p></div>';
		}
	}else{
	//first page you see
		include_once ("pager-class.php");
		$cda=$cdd=$cta=$cdwa=$cpa=$cia='';
		if(isset($_GET['by'])){
			switch ($_GET['by']) {
				case'dd'://date descending
					$sortby='ORDER BY added DESC';
					$cdd=' class="current"';
					break;
				case'da'://date ascending
					$sortby='ORDER BY added ASC';
					$cda=' class="current"';
					break;
				case'ta'://title alphabetically
					$sortby='ORDER BY title ASC';
					$cta=' class="current"';
					break;
				case'dwa'://number of downloads
					$sortby='ORDER BY downloads ASC';
					$cdwa=' class="current"';
					break;
				case'pa'://number of purchases
					$sortby='ORDER BY purchases ASC';
					$cpa=' class="current"';
					break;
				case'ia'://id
				default:
					$sortby='ORDER BY id ASC';
					$cia=' class="current"';
			}
		}else{
			$cia=' class="current"';
			$sortby='ORDER BY id ASC';
		}
		$range=10;
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $table WHERE id > 0");
		if(get_option('eshop_records')!='' && is_numeric(get_option('eshop_records'))){
			$records=get_option('eshop_records');
		}else{
			$records='10';
		}

		if(isset($_GET['viewall']))$records=$max;
		$pager = new eshopPager( 
			$max ,          //see above
			$records,            // how many records to display at one time
			@$_GET['_p'] 	//this is the current page no carried via _GET
		);
	
		$pager->set_range($range);
		$thispage=$pager->get_limit();
		$c=$pager->get_limit_offset();
	if($max>0){
		$myrowres=$wpdb->get_results("Select * From $table $sortby LIMIT $thispage");
		//work out totals for quick stats
		$total=0;
		$purchased=0;
		$mycounts=$wpdb->get_results("Select * From $table");
		foreach($mycounts as $acount){ 
			$total+=$acount->downloads;
			$purchased+=$acount->purchases;
		}
	?>
	<div class="wrap">
		<h2><?php _e('Downloadable Products','eshop'); ?></h2>
		<?php
		$apge=wp_specialchars($_SERVER['PHP_SELF']).'?page='.$_GET['page'];
		echo '<ul id="eshopsubmenu">';
		echo '<li><span>'.__('Sort Orders by &raquo;','eshop').'</span></li>';
		echo '<li><a href="'.$apge.'&amp;by=ia"'.$cia.'>'.__('ID Number','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=ta"'.$cta.'>'.__('Title','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=da"'.$cda.'>'.__('Date Ascending','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=dd"'.$cdd.'>'.__('Date Descending','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=dwa"'.$cdwa.'>'.__('Downloads','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=pa"'.$cpa.'>'.__('Purchases','eshop').'</a></li>';
		echo '</ul>';
		?>
		<p><strong><?php _e('Total Downloads:','eshop'); ?> </strong><?php echo $total; ?><br />
		<strong><?php _e('Total Purchases:','eshop'); ?> </strong><? echo $purchased; ?><br />
		</p>  
		<table id="listing" summary="<?php _e('download listing','eshop'); ?>">
		<caption><?php _e('Available downloads','eshop'); ?></caption>
		<thead>
		 <tr>
		  <th id="edid"><?php _e('ID','eshop'); ?></th>
		  <th id="edtitle"><?php _e('Title','eshop'); ?></th>
		  <th id="edsize"><?php _e('Size','eshop'); ?></th>
		  <th id="edstatus"><?php _e('Status','eshop'); ?></th>
		  <th id="eddate"><?php _e('Upload Date','eshop'); ?></th>
		  <th id="eddown"><?php _e('Downloads','eshop'); ?></th>
		  <th id="edpurch"><?php _e('Purchases','eshop'); ?></th>
		 </tr>
		 </thead>
		 <tbody>
		 <?php
		 $calt=0;
		foreach($myrowres as $row){    
			$filepath=$dir_upload.$row->files;
		   $size = @filesize($filepath);
		   $label = (strlen($row->title) >= 20) ? substr($row->title,0,20) . "&#8230;" : $row->title;
		   $calt++;
		   $alt = ($calt % 2) ? '' : ' class="alt"';
		   echo "<tr".$alt.">\n";
		   echo '<td id="redid'.$row->id.'" headers="edid">#'.$row->id."</td>\n";
		   echo '<td headers="edtitle redid'.$row->id.'"><a href="?page=eshop_downloads.php&amp;edit='.$row->id.'" title="edit details for '.$row->title.'">'.$label."</a></td>\n";
		   echo '<td headers="edsize redid'.$row->id.'">'.eshop_read_filesize($size)."</td>\n";
		   echo '<td headers="edstatus redid'.$row->id.'">'.eshop_check_brokenlink($filepath)."</td>\n";
		   echo '<td headers="eddate redid'.$row->id.'">'.$row->added."</td>\n";
		   echo '<td headers="eddown redid'.$row->id.'">'.$row->downloads."</td>\n";
		   echo '<td headers="edpurch redid'.$row->id.'">'.$row->purchases."</td>\n";
		   echo "</tr>\n";
		 }
		 ?>
		 </tbody>
		</table>
	<?php

	//fix the uri for pagination?
	//$_SERVER['REQUEST_URI']= preg_replace('/&edit=.*/','',$_SERVER['REQUEST_URI']);
	   //paginate
	echo '<div class="paginate"><p>';//<p class="checkers">Bulk:<a href="javascript:checkedAll(\'downloadlist\', true)" title="Select all of the checkboxes above">Check</a><span class="offset"> | </span><a href="javascript:checkedAll(\'downloadlist\', false)" title="Deselect all of the checkboxes above">Uncheck</a></p><p>';
		if($pager->_pages > 1){
			echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ',__('&laquo; First Page','eshop'),__('Last Page &raquo;','eshop')).'';
		//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
		if($pager->_pages >= 2){
			echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;viewall=yes">'.__('View All &raquo;','eshop').'</a>';
		}
		echo '</p></div>';
	}else{
	?>
		<div class="wrap">
		<h2><?php _e('Downloadable Product','eshop'); ?>s</h2>
		<p><?php _e('You currently have no downloadable products','eshop'); ?>.</p>
		
	<?php
	}
	?>
		</div>
		<?php
		$dirpath=eshop_download_directory();
		if(!is_writeable($dirpath)) {
			echo '
			<div id="message" class="error fade">
			<p>'.__('<strong>Warning!</strong>The download directory is not currently writable! File permissions must first be changed.','eshop').'
			</p>
			</div>'."\n";
		}else{
		// only displayed if the directory is writable to.
		$eshopmaxupload=ini_get("upload_max_filesize")*1048576;
		?>
			<div class="wrap">
			<h2><?php _e('Upload a File','eshop'); ?></h2>
			<?php
			$eshopmaxfilesize=ini_get("upload_max_filesize");
			//if mu use this
			if(function_exists('check_upload_size'))
				$eshopmaxfilesize=eshop_read_filesize(1024 * get_site_option( 'fileupload_maxk', 1500 ));
			?>
			<p><?php _e('Use this to upload your local file. Max file size is ','eshop'); echo $eshopmaxfilesize; ?></p>
			<form action="" method="post" id="eshopup" enctype="multipart/form-data">
			<fieldset><legend><?php _e('Upload','eshop'); ?></legend>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $eshopmaxupload; ?>" />
				<label for="filetitle" class="lab"><?php _e('Title','eshop'); ?></label><input type="text" name="title" id="filetitle" size="35" value="<?php echo $atitle; ?>" /><br />
			   <label for="upfile" class="lab"><?php _e('Local File','eshop'); ?></label>
				 <input name="upfile" type="file" id="upfile" size="45" />
				 <fieldset><legend><?php _e('Overwrite file it it exists','eshop'); ?></legend>
				 <input name="overwrite" type="radio" id="overwrite" value="no" checked="checked" /><label for="overwrite"><?php _e('No','eshop'); ?></label>
				 <input name="overwrite" type="radio" id="yesoverwrite" value="yes" /><label for="yesoverwrite"><?php _e('Yes','eshop'); ?></label>

				 </fieldset>
				</fieldset>
				  <p class="submit"><input type="submit" name="up" value="<?php _e('Upload File','eshop'); ?>" class="button-primary" /></p>
			</form>
		</div>
		<?php
		}
		//check for downloads that were uploaded via FTP.
		if(is_array(eshop_contains_files())){
			?>
			<div class="wrap">
			<h2><?php _e('Unknown Download Files','eshop'); ?></h2>
			<ul>
			<?php
			foreach(eshop_contains_files() as $contains){
				echo '<li>'.$contains.'</li>';
			}
			?>
			</ul>
			<p><a href="<?php echo wp_specialchars($_SERVER['REQUEST_URI']).'&amp;eshop_orphan'; ?>"><?php _e('Add all unknown download files','eshop'); ?></a></p>
			</div>
			<?php
		}
	}
	eshop_show_credits();
}
?>