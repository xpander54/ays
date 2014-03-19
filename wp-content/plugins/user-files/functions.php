<?php

 

function curPageName() { 

 $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

 
}
################
# Timestamp    #
############3###

function GetTimeStamp($tFilePath, $tUserID) {

$upload_dir = wp_upload_dir();
$absPt=$upload_dir['basedir'].'/file_uploads/'.$tUserID;
$FileForTime = pathinfo($tFilePath, PATHINFO_BASENAME);

$retDate = date ("F d Y", filemtime($absPt.'/'.$FileForTime));

return $retDate;

}


##########################
# List User Files        #
##########################
function ListUserFiles($Thefile,$TheClass,$userID,$fl) {
	global $wpdb;	
	global $post;
		$ext = pathinfo($Thefile, PATHINFO_EXTENSION);
		
		$tExt =  SetIcon($ext);
		
								
		echo '<td class="'.$TheClass.'" width="60%" ><img src="'. $tExt.'" width="20" > '.pathinfo($Thefile, PATHINFO_FILENAME) .'</td>';
        
         $getDescr= $wpdb->get_var("SELECT description FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$Thefile ."' and user_id='" .$userID. "'");
        
        if(!empty($getDescr)){
        
        //echo '<td class="'.$TheClass.'" id="ShowUFnotes" ><a  href="javascript:void(0);" onclick="javascript:shownotes(\''.$getDescr.'\');">Show Notes</a></td>';
        
        echo '<td class="'.$TheClass.'"> <a id="dLink'.$fl.'" href="javascript:void(0);" onclick="javascript:toggle2(\'dNotes'.$fl.'\',\'dLink'.$fl.'\');" >Show Notes</a>
 


     <div id="dNotes'.$fl.'" style="display:none;">'. $getDescr.'</div>
</td>'; /**/

        
       }else{	            
	         
	    echo '<td class="'.$TheClass.'" >&nbsp;</td>';
        }
        
		echo '<td class="'.$TheClass.'" >'. GetTimeStamp($Thefile,$userID)  .'</td>';
		
		    echo '<td class="'.$TheClass.'">';
						 
		$currOpts_defcat = get_option('file_manger_defaultcat');
		$getCrntCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$Thefile ."' and user_id='" .$userID. "'");
        
								if (!$getCrntCat) {	   
								echo $currOpts_defcat;	
								}else{
								echo $getCrntCat;
								}
		    echo'</td>';	 
		
         
	 
        if (strpos(curPageName(),'?') ==false){ 
		$dnlLink = curPageName().'?theDLfile='.$userID.'/'.$Thefile;
		$DelLink = curPageName().'?deletefile='.$userID.'/'.$Thefile; 
		}else{

            if(!$post->ID){ 
            $DelLink = curPageName().'&deletefile='.$userID.'/'.$Thefile;
		    $dnlLink = curPageName().'&theDLfile='.$userID.'/'.$Thefile; 

            }else{
        
            $DelLink = '?page_id='.$post->ID.'&deletefile='.$userID.'/'.$Thefile;
            $dnlLink = '?page_id='.$post->ID.'&theDLfile='.$userID.'/'.$Thefile;
            }
		
	} 
		
		echo '<td class="'.$TheClass.'" align="right"><a rel="download.png" href="'.$dnlLink.'">     <img title="Download '.$Thefile.'" src="'.plugins_url( '/user-files/img/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>';
		
		
		
		
		if(get_option('file_manger_allow_del')=='yes') {

		
		
		echo '     |     <a href="'.$DelLink.'">     <img title="Delete '.$Thefile.'" src="'.plugins_url( '/user-files/img/delete.png ' , dirname(__FILE__) ). '" alt="" width="20" height="20" /></a> </td></tr>';
		}else{
		echo '</td></tr>';
		
		}//end if

}


###################################
# List Filtered Admin Files       #
###################################
function ListFilteredFiles($Thefile,$userID) {

global $wpdb;
global $tp;	
global $wp_query;
$upload_dir = wp_upload_dir();		
		$ext = pathinfo($Thefile, PATHINFO_EXTENSION);
		  
		$tExt =  SetIcon($ext);
		
								
		echo '<tr><td  width="60%" ><input type="checkbox" name="change_cat'.$tp .'" value="addit" /> <input type="hidden" name="file'.$tp.'" value="'.$Thefile.'" ><input type="hidden" name="changecat_user'.$tp.'" value="'.$userNum.'"><img src="'. $tExt.'" width="20" > '.pathinfo($Thefile, PATHINFO_FILENAME) .'</td>';
        
        $getDescr = $wpdb->get_var("SELECT description FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$Thefile ."' and user_id='" .$userID. "'");
      
        
        echo '<td><textarea name="notes'.$tp.'" rows=3 cols=30>'. 	$getDescr .'</textarea></td>';				

        
        echo '<td>'.GetTimeStamp($Thefile,$userNum)   .'</td>';
		
		
		
		    echo '<td>';
					$currOpts_defcat = get_option('file_manger_defaultcat');
							$getCrntCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$Thefile ."' and user_id='" .$userID. "'");	 
								 
								if (!$getCrntCat) {	   
								echo $currOpts_defcat;	
								}else{
								echo $getCrntCat;
								}
		    echo'</td>';	
		
		
		
		
		echo '<td align="right"><a rel="download.png" href="admin.php?page=manage-files-main&theDLfile='.$userID.'/'.$Thefile.'">     <img title="Download '.$Thefile.'" src="'.plugins_url( '/user-files/img/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>';
		
		
		
		
		if(get_option('file_manger_allow_del')=='yes') {
		
		
		
		echo '     |     <a href="admin.php?page=manage-files-main&deletefile='.$userID.'/'.$files.'">     <img title="Delete '.$Thefile.'" src="'.plugins_url( '/user-files/img/delete.png ' , dirname(__FILE__) ). '" alt="" width="20" height="20" /></a> </td></tr>';
		}else{
		echo '</td></tr>';
		
		}//end if
$tp++; 
}


##########################
# List Admin Files       #
##########################


function ListAdminFiles($userNum) {
$upload_dir = wp_upload_dir();
	global $wpdb;	
	global $tp;
if ($Subhandle = @opendir($upload_dir['basedir'].'/file_uploads/'.$userNum)) {
				
						while (false !== ($files = readdir($Subhandle))) {
						echo '<tr>';
							if ($files!=".") {
								if ($files!="..") {
								
									$ext = pathinfo($files, PATHINFO_EXTENSION);
									$tExt =  SetIcon($ext);
								
								
								echo '<td><input type="checkbox" name="change_cat'.$tp .'" value="addit" /> <input type="hidden" name="file'.$tp.'" value="'.$files.'" ><input type="hidden" name="changecat_user'.$tp.'" value="'.$userNum.'"> <img src="'. $tExt.'" width="20" >   '.pathinfo($files, PATHINFO_FILENAME).'</td>';
                                
								
        $getDescr = $wpdb->get_var("SELECT description FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$files ."' and user_id='" .$userNum. "'");	 
      
        
        echo '<td><textarea name="notes'.$tp.'" rows=3 cols=30>'. 	$getDescr .'</textarea></td>';				

                
				echo '<td>'. GetTimeStamp($files,$userNum) .'</td>';		
												
						echo '<td>'; 
                          $currOpts_defcat = get_option('file_manger_defaultcat');
							$getCrntCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$files ."' and user_id='" .$userNum. "'");	 
								 
								if (!$getCrntCat) {	   
								echo $currOpts_defcat;	
								}else{
								echo $getCrntCat;
								}
						


						
						echo'</td>';	
		
																
						echo '<td align="right"><a rel="download.png" href="admin.php?page=manage-files-main&theDLfile='.$userNum.'/'.$files.'">     <img title="Download '.$files.'" src="'.plugins_url( '/user-files/img/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>   |   <a href="admin.php?page=manage-files-main&deletefile='.$userNum.'/'.$files.'">     <img title="Delete '.$files.'" src="'.plugins_url( '/user-files/img/delete.png ' , dirname(__FILE__) ). '" alt="" width="20" height="20" /></a></td>';
						
					  $tp++;
								}
							}
						echo '</tr>';
						}
					
					}else{
					echo __('No Files','userfiles');
					} 
	
}










function show_FM_pages() {
 
    add_options_page(__('User Files Settings','userfiles'), __('User Files','userfiles'), 'manage_options', 'file_manager_options', 'files_settings_page' );

	add_menu_page( __('Manage Files','userfiles'), __('Manage Files','userfiles'), 'manage_options', 'manage-files-main', 'manage_files_mainpg');

	add_submenu_page('manage-files-main', __('Add Files','userfiles'), __('Add Files','userfiles'), 'manage_options','files-add-files', 'manage_files_upload');
	
	add_submenu_page('manage-files-main', __('Categories','userfiles'), __('Categories','userfiles'), 'manage_options','files-add-cats', 'ShowCategories');
	
	add_submenu_page('manage-files-main', __('Manage Icons','userfiles'), __('Manage Icons','userfiles'), 'manage_options','files-add-icons', 'Icon_management'); 
	
	add_submenu_page('manage-files-main', __('FTP Paths','userfiles'), __('FTP Paths','userfiles'), 'manage_options','files-see-ftp', 'FTP_Paths'); 
	
	
	add_submenu_page('manage-files-main', __('Help','userfiles'), __('Help','userfiles'), 'manage_options','files-help-files', 'file_uploader_help');

$currOpts_menu = get_option('file_manger_show_menu');

	
	if (!current_user_can('manage_options') and $currOpts_menu==yes)  {
	
	add_menu_page( __('Manage Files','userfiles'), __('Manage Files','userfiles'), 'read', 'manage-files-user', 'manage_files_user');
	
	}


}

function manage_files_user() { 
global $wpdb;
global $id;

$currOpts_credits = get_option('file_manger_credit');


		$upload_dir = wp_upload_dir();
		global $current_user;
			  get_currentuserinfo();

if (isset($_GET['deletefile'])){

$isitGone = unlink($upload_dir['basedir'].'/file_uploads/'.$_GET['deletefile']);

$toUsFl=explode ( "/" , $_GET['deletefolder'] );


$wpdb->query("DELETE FROM ".$wpdb->prefix."userfile_data WHERE user_id ='" .$toUsFl[1]. "' AND filename ='".$toUsFl[2]."'");
	
		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo __('The file has been deleted','userfiles');
		echo '</div>';
		} else{ 
		echo '<div id="message" class="error">';
		echo __('There was an error deleting the file, please try again!','userfiles');
		echo '</div>';
		}
		
		
		
	
}

		if (isset($_POST['addfiles'])){	

		$subDir=$current_user->ID;

					$usFolder = file_exists ( $upload_dir['basedir'].'/file_uploads/'.$subDir); 

					if (!$usFolder) {
					mkdir ( $upload_dir['basedir'].'/file_uploads/'. $subDir, 0777 , true );
					chmod($upload_dir['basedir'].'/file_uploads/'. $subDir,0777);
					}
					
					
					$target_path = $upload_dir['basedir'].'/file_uploads/'. $subDir.'/';
					
					$target_path = $target_path . basename($_FILES['uploadedfile']['name']); 

					if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
						echo '<div id="message" class="updated">';
						echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
						" has been uploaded<br />";
				
						$wpdb->insert( $wpdb->prefix . "userfile_data", array( 'id'=> '','user_id'=>$subDir,'category'=>$_POST['curr_cat'],'filename'=>basename( $_FILES['uploadedfile']['name'] )));  
				
						$DoMails = get_option('file_manger_notify'); 
						
						if (!empty($DoMails)){ 
						
						wp_mail( $DoMails, 'New file at '. get_option('blogname'), $current_user->user_login.' has just uploaded '.  basename( $_FILES['uploadedfile']['name']) .' at '. get_option('blogname'));
						echo __('An adminisrator has successfully been notified of your upload.','userfiles');
						
						}
						echo '</div>';
						
					} else{
						echo '<div id="message" class="error">'; 
						echo __('There was an error uploading the file, please try again!','userfiles')."<br />";
						echo '</div>';
					} 
			}

			
			if($_POST['sorted']){
			
			$user_file_search=$_POST['file_search'];
			$user_cat_sort= $_POST['showcatsfilter'];
			
	
			if($user_cat_sort=='chge'){
			unset($user_cat_sort);
			}

			
			
			}
			
	echo '<head>  <link rel="stylesheet" href="'.plugins_url( '/user-files/style.css').'" type="text/css"/></head>';
	?>
<form method="POST" action="admin.php?page=manage-files-user" >


Search: <input type="text" size ="60" name="file_search" value="" />

   <b>----   <?php _e('or','userfiles'); ?>   ----</b>

<select name="showcatsfilter" id="showcatsfilter">
<option value="chge" "selected" ><?php _e('Show all categories','userfiles'); ?>.........</option>
<?php

$getCatList = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "userfile_category");	 

foreach($getCatList as $breakfast) {

echo '<option value="'.$breakfast->category.'">'. $breakfast->category .'</option>';


}

echo '</select>   ';
	?>

<input type="submit" name="sorted" value="<?php _e('Filter','userfiles');?>" /> 
	
	</form>
	<?php
	

		echo '<table class = "widefat" width="100%">';	
		echo'<thead><th>';
        echo __('Your Files','userfiles');
        echo '</th><Date</th><th>Category</th><th></th></thead>';
			if ($handle = @opendir($upload_dir['basedir'].'/file_uploads/'.$current_user->ID)) {
			$rowClass='';
			unset($found);
			$i=1;
			while (false !== ($file = readdir($handle))) {
					
					if ($file!=".") {
						if ($file!="..") {

						if($user_file_search){				
		
							$searchMatch = strpos(strtolower($file),strtolower($user_file_search));
							
							if($searchMatch === 0 || $searchMatch >0){
							ListUserFiles($file,$rowClass,$current_user->ID,$i);
							$found=true;
							}
							
						}elseif($user_cat_sort){
						
						$isCat = CatFilter($file,$user_cat_sort,$current_user->ID);
						
						if($isCat){
						ListUserFiles($file,$rowClass,$current_user->ID,$i);
						$found=true;
						}
						}else{

							ListUserFiles($file,$rowClass,$current_user->ID,$i);
						
							}
						}
					}
					
					$i++;
				}
				
				if($user_file_search && $found != true) {
				echo '<td><br /><b>'.__('No files found for','userfiles').' '.$user_file_search.'</b><br /></td>';
				}
				
				if($user_cat_sort && $found != true) {
				echo '<td><br /><b>'.__('Nothing found for category','userfiles').' '.$user_cat_sort .'</b><br /></td>';
				}
				
				
				
			}else{
			echo '<td><br /><b>'.__('You have no files','userfiles').'</b><br /></td>';
			}

		echo '</table>';
		
			$currOpts_up = get_option('file_manger_allow_up');

				if ($currOpts_up=='yes'){  
				
				echo '<hr class="linebreak" NOSHADE/>';
				
				?> 
								
								
				<table class="widefat"><thead><th><h3><?php _e('Add Files','userfiles'); ?></h3></th></thead>

			<?php

				$max_post = (int)(ini_get('post_max_size'));

				$MaxSet=1000000*(int)$max_post;

				?>

				<tr><td>
				<form enctype="multipart/form-data" action="<?php echo site_url() .'/index.php?p='.$id; ?>" method="POST" >		
				<?php _e('Choose a file to upload, your upload limit is','userfiles').' '; ?> <?php echo $max_post; ?>M <br />
				
				 <input name="uploadedfile" type="file" /><br />
				 <input type="hidden" name="addfiles" value="addfiles" />
				 
				 
				 </td></tr><tr><td> 
				 <?php
					$aCats = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."userfile_category" );
                    ?>    
						Category:  <select name="curr_cat" id="curr_cat">
                        <?php
						foreach ( $aCats  as $iCats) :
						
						
                        ?>
					<option value="<?php echo $iCats->category. '">'. $iCats->category; ?> </option>

					<?php
					endforeach;
					echo '</select><br /><p>';


						?>
				 </td></tr>
				 
				 <tr><td>
				<input type="submit" value="<?php _e('Upload File','userfiles'); ?>" />
				</form>
				</td></tr>
				<?php
				echo '</table>';
				}


if ($currOpts_credits =='yes'){
echo '<p>&nbsp;</p><p>'.userfiles_credit().'</p>';

}


}

#####################
# Category Filter   #
#####################
function CatFilter($file,$cate,$tUserid){
global $wpdb;

if ($cate=='chge'){
return true;
}else{ 
 $currOpts_defcat = get_option('file_manger_defaultcat');

		$IsaCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$file ."' and user_id='" .$tUserid. "' and category='".$cate."'");	 
				 
				 
				 
				if ($IsaCat == $cate) {	   
				return true;	
				}else{
				
				$IsNoCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$file ."' and user_id='" .$tUserid. "'");	
				
					if(empty($IsNoCat) && $currOpts_defcat==$cate){
					return true;
					}else{
					return false;
					}
				
				
				}

}

}


function uploadHelper(){

if (isset($_POST['addfiles'])){	


                  global $wpdb;
		         $upload_dir = wp_upload_dir();
                 $current_user = wp_get_current_user();
                $subDir = $current_user->ID;
                
                if (!empty($_POST['curr_cat'])){
                $SetCat=$_POST['curr_cat'];
                }else{
                $SetCat=$_POST['widge_cat'];
                }
             
		
					$usFolder = file_exists ( $upload_dir['basedir'].'/file_uploads/'.$subDir); 

					if (!$usFolder) {
					mkdir ( $upload_dir['basedir'].'/file_uploads/'. $subDir, 0777 , true );
					chmod($upload_dir['basedir'].'/file_uploads/'. $subDir,0777);
					}
					
					
					$target_path = $upload_dir['basedir'].'/file_uploads/'. $subDir.'/';
					
					$target_path = $target_path . basename($_FILES['uploadedfile']['name']); 

					if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
                   
						$wpdb->query("INSERT INTO ".$wpdb->prefix . "userfile_data VALUES('','".$subDir."','".$SetCat."','".$_FILES['uploadedfile']['name']."')");
                        
                        $messageGo ='<div id="message" class="wrap">';
						$messageGo .= "Your file has been uploaded<br />";
                        	
						$DoMails = get_option('file_manger_notify'); 
                        
                       if (!empty($DoMails) && $DoMails != ""){
					   
								$url = $_SERVER['SERVER_NAME'];
											   
					   
						 $headers[] ='From:"'.get_option('blogname').'" <no-reply@'. str_replace('www.','',$url).'>';
					   
						
						wp_mail($DoMails, __('A new file at','userfiles').' '. get_option('blogname'), $current_user->user_login.' '.__('has just uploaded','userfiles').' '.  basename( $_FILES['uploadedfile']['name']) .' '.__('to category','userfiles').' '. $SetCat,$headers); 
						$messageGo .= __('An administrator has successfully been notified of your upload.','userfiles');
						
						}
						$messageGo .= '</div>';
						
					}else{
                        $messageGo =  '<div id="message" class="wrap">'; 
						$messageGo .=  __('Error with file upload','userfiles');
						$messageGo .=  '</div>';
                    
                    }
			             
                  $_POST['addfiles'] =   $messageGo;   
            
            }

}


function verifyInstall(){

global $instalVersion;

$isInstallOK=get_option('file_manger_upgrade');
if ($isInstallOK!=$instalVersion){

ActivateFileDir(); 

update_option('file_manger_upgrade',$instalVersion);

    }


}

/*
function file( $file_id ) {
global $wpdb;

$GatherFileData = $wpdb->get_var("SELECT * FROM ". $wpdb->prefix . "userfile_meta WHERE file_id = '".$file_id ."' ");	

$file_array = array();

while($rows = mysql_fetch_array($result)){
$tFile=$rows['meta_key'];
$pt=$rows['meta_value'];
$file_array[] = $tFile;
}

return $file_array;

}  
*/


    


?>