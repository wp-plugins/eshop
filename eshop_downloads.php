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
		 return '<span class="missing">Missing</span>';
	}else{ 
		 return '<span class="available">Available</span>';
		 fclose($file_exists);
	 }
	fclose($file_exists);
	return false;
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
			$file_name = $_FILES["upfile"]["name"];
			if(trim($_FILES["upfile"]["name"]) == "") {
				$error.="<p>No file indicated</p>";
			}
			if(!file_exists($dir_upload.$file_name) || $_POST['overwrite']=='yes'){
				if(@is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
					
					if(move_uploaded_file($_FILES["upfile"]["tmp_name"], $dir_upload.$file_name)){
						$success='<p>I moved it</p>';
					}else{
						$error.='<p>failed to move</p>';
					}

				} else {
					$error.="<p>Error uploading file " . $_FILES["upfile"]["name"] . " <strong>".$file_error_strings[$_FILES["upfile"]["error"]]."</strong></p>";
				}
			}else{
					$error.="<p>Error uploading file " . $_FILES["upfile"]["name"] . " it <strong>already exists!</strong></p>";
			}
		}else{
			$error.='<p>A title must be provided.</p>';
		}
		if($error==''){ //ie a successful upload
			$enttitle=$wpdb->escape($_POST['title']);
			$entfile=$wpdb->escape($file_name);
			$wpdb->query("INSERT INTO $table (title,added,files) VALUES ('$enttitle',NOW(),'$entfile')");

			echo '<div id="message" class="updated fade"><p>' . $_FILES["upfile"]["name"] . " has successfully uploaded</p></div>";
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
		echo '<div id="message" class="updated fade"><p>File deleted successfully</p></div>';
		unset($_GET['edit']);
	}  
	
	//when edit a file this is the bit that gets used.
	if(isset($_POST['editamend'])){
		if(is_numeric($_POST['downloads']) && is_numeric($_POST['purchases']) && $_POST['title']!=''){
			//add in mysql update here
			$query= 'UPDATE '.$table.' SET title = "'.$wpdb->escape($_POST['title']).'", downloads = "'.$wpdb->escape($_POST['downloads']).'", purchases = "'.$wpdb->escape($_POST['purchases']).'"  WHERE id = "'.$wpdb->escape($_POST['id']).'"';
			$wpdb->query("$query");
			echo '<div id="message" class="updated fade"><p>File updated successfully</p></div>';
			unset($_GET['edit']);
		}else{
			//error handling
			if($_POST['title']==''){
				$error.='<li>The title for the file cannot be blank!</li>';
			}
			if(!is_numeric($_POST['downloads'])){
				$error.='<li>Downloads should to be a number!</li>';
			}
			if(!is_numeric($_POST['purchases'])){
				$error.='<li>Purchases should to be a number!</li>';
			}

			echo '<div id="message" class="error fade"><p>Some errors were found:</p><ul>'.$error.'</ul></div>';
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
			<h2>Edit File details</h2>
			<table id="listing" summary="downloadable file details">
			<caption>File details</caption>
			<thead>
			 <tr>
			  <th id="edid">ID</th>
			  <th id="edtitle">Title</th>
			  <th id="edsize">Size</th>
			  <th id="edfile">File name</th>
			  <th id="eddate">Upload Date</th>
			  <th id="eddown">Downloads</th>
			  <th id="edpurc">Purchases</th>
			 </tr>
			 </thead>
			 <tbody>
			 <?php
				$filepath=eshop_download_directory().$row->files;
			   $size = @filesize($filepath);
			   $label = (strlen($row->title) >= 20) ? substr($row->title,0,20) . "&#8230;" : $row->title;
			   $calt++;
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
			$checkproduct = $wpdb->get_var("SELECT COUNT(post_id) FROM $metatable WHERE meta_key='Product Download'");
			if($checkproduct>0){
				$myrows=$wpdb->get_results("Select post_id FROM $metatable WHERE meta_key='Product Download'");
				echo '<p class="productassociation">This file is associated with the following product pages:</p>';
				echo '<ul class="productpages">';
				foreach($myrows as $myrow){
					echo '<li><a href="page.php?action=edit&amp;post='.$myrow->post_id.'" title="edit '.get_the_title($myrow->post_id).'">'.get_the_title($myrow->post_id).'</a></li>';
				}
				echo '</ul>';
			}
			?>
			<form method="post" action="" id="downloadedit">
			<fieldset><legend>Amend File details</legend>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />

			<label for="filetitle">Title</label><input type="text" name="title" id="filetitle" size="35" value="<?php echo $row->title; ?>" /><br />
			<label for="downloads">Downloads</label><input type="text" name="downloads" id="downloads" size="5" value="<?php echo $row->downloads; ?>" /><br />
			<label for="purchases">Purchases</label><input type="text" name="purchases" id="purchases" size="5" value="<?php echo $row->purchases; ?>" /><br />

			</fieldset>
			  <p class="submit"><input type="submit" name="editamend" value="Amend details" class="button" /></p>
			</form>
			</div>
			<?php
			if($checkproduct==0){
			?>
				<div class="wrap">
				<h2>Delete</h2>
				<p>You can only delete this file if it is <strong>not</strong> associated with a product page.</p>
				<form method="post" action="" id="downloadedit">
				<input type="hidden" name="delid" value="<?php echo $row->id; ?>" />
				<p class="submit"><input type="submit" name="editdelete" value="Delete File '<?php echo $row->title; ?>'" class="button" /></p>
				</form>
				</div>
			<?php
			}
		
		}else{
		//ie does not exist
			echo '<div id="message" class="error fade"><p>Product not found.</p></div>';
		}
	}else{
	//first page you see
		include_once ("pager-class.php");
		$cda=$cdd=$cta=$cdwa=$cpa=$ia='';
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
		<h2>Downloadable Products</h2>
		<?php
		$apge=wp_specialchars($_SERVER['PHP_SELF']).'?page='.$_GET['page'];
		echo '<ul id="eshopsubmenu">';
		echo '<li><span>Sort Orders by &raquo;</span></li>';
		echo '<li><a href="'.$apge.'&amp;by=ia"'.$cia.'>ID Number</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=ta"'.$cta.'>Title</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=da"'.$cda.'>Date Ascending</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=dd"'.$cdd.'>Date Descending</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=dwa"'.$cdwa.'>Downloads</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=pa"'.$cpa.'>Purchases</a></li>';
		echo '</ul>';
		?>
		<p><strong>Total Downloads: </strong><?php echo $total; ?><br />
		<strong>Total Purchases: </strong><? echo $purchased; ?><br />
		</p>  

		<table id="listing" summary="download listing">
		<caption>Available downloads</caption>
		<thead>
		 <tr>
		  <th id="edid">ID</th>
		  <th id="edtitle">Title</th>
		  <th id="edsize">Size</th>
		  <th id="edstatus">Status</th>
		  <th id="eddate">Upload Date</th>
		  <th id="eddown">Downloads</th>
		  <th id="edpurch">Purchases</th>
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
			echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>'). '<br />';
		}else{
			echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>'). '<br />';
		}
		echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ','&laquo; First Page','Last Page &raquo;').'';
		//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
		if($pager->_pages >= 2){
			echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;viewall=yes" title="View all">View All &raquo;</a>';
		}
		echo '</p></div>';
	}else{
	?>
		<div class="wrap">
		<h2>Downloadable Products</h2>
		<p>You currently have no downloadable products.</p>
		
	<?php
	}
	?>
		</div>
		<?php
		$dirpath=eshop_download_directory();
		
		if(!is_writeable($dirpath)) {
			echo '
			<div id="message" class="error fade">
			<p><strong>Warning!</strong> The download directory is not currently writable! File permissions must first be changed.
			</p>
			</div>'."\n";
		}else{
		// only displayed if the directory is writable to.
		?>
			<div class="wrap">
			<h2>Upload a File</h2>
			<p>Use this to upload your local file. Max file size is 2Mb.</p>
			<form action="" method="post" id="eshopup" enctype="multipart/form-data">
			<fieldset><legend>Upload</legend>
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				<label for="filetitle" class="lab">Title</label><input type="text" name="title" id="filetitle" size="35" value="<?php echo $atitle; ?>" /><br />
			   <label for="upfile" class="lab">Local File</label>
				 <input name="upfile" type="file" id="upfile" size="45" />
				 <fieldset><legend>Over write file it it exists</legend>
				 <input name="overwrite" type="radio" id="overwrite" value="no" checked="checked" /><label for="overwrite">No</label>
				 <input name="overwrite" type="radio" id="yesoverwrite" value="yes" /><label for="yesoverwrite">Yes</label>

				 </fieldset>
				</fieldset>
				  <p class="submit"><input type="submit" name="up" value="Upload File" class="button" /></p>
			</form>
		</div>

		<?php
		}
	}
	eshop_show_credits();
}
?>