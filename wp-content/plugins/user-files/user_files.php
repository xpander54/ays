<?php
/*
Plugin Name: User File Manager
Plugin URI: http://www.whereyoursolutionis.com/user-files-plugin/
Description: Plugin to manage files for your users. You can upload files for your users to access, files uploaded to the user account are only viewable by the designated user. Files can be sorted and uploaded by category. Options available for user to add and/or delete files, upload notifications, widgets, and shortcode. You can also use custom icons for files.  
Author: Innovative Solutions
Version: 2.3.2
Author URI: http://www.whereyoursolutionis.com/author/scriptonite/
*/


register_activation_hook(__FILE__,'ActivateFileDir'); 
add_action('admin_menu', 'show_FM_pages');
add_action('wp_dashboard_setup', 'file_manager_dashboard');
add_shortcode( 'user_file_manager' , 'manage_files_userpage' );
add_action('init','getDownloads');
add_action('wp_head','uploadHelper');
add_action('admin_notices','verifyInstall');
add_filter('query_vars', 'getDeleted');


add_action( 'init', 'userfiles_textdomain' );

$instalVersion=5;

function userfiles_textdomain() {

load_plugin_textdomain( 'userfiles', false, 'user-files/lang/' );

}

require(ABSPATH . 'wp-content/plugins/user-files/widget.php');
require(ABSPATH . 'wp-content/plugins/user-files/functions.php');
 

	
function ActivateFileDir() {
global $instalVersion;
$isInstallOK=get_option('file_manger_upgrade');

if($isInstallOK!=$instalVersion){
global $wpdb;
global $wp_roles;
$upload_dir = wp_upload_dir();

$isFolder = file_exists ( $upload_dir['basedir'].'/file_uploads'); 

if (!$isFolder) {
mkdir (  $upload_dir['basedir'].'/file_uploads', 0777 , true );
chmod($upload_dir['basedir'].'/file_uploads', 0777);
}

$isUpFolder = file_exists ( $upload_dir['basedir'].'/userfile_icons/'); 

if (!$isUpFolder) {
mkdir (  $upload_dir['basedir'].'/userfile_icons', 0777 , true );
chmod($upload_dir['basedir'].'/userfile_icons', 0777);
}


   if($wpdb->get_var("show tables like ".$wpdb->prefix . "userfile_icons") != $wpdb->prefix . "userfile_icons") {
      
       
   $sql1 = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "userfile_icons(  
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  extension varchar(10) NOT NULL UNIQUE,
  image varchar (500) NOT NULL
    );"; 
 }

    if($wpdb->get_var("show tables like ".$wpdb->prefix . "userfile_category") != $wpdb->prefix . "userfile_category") {

	
     $sql2 = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "userfile_category(  
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	category varchar(50) NOT NULL,
    UNIQUE (category)
     );"; 
     
     }
	 



	 
	 

    /* 
	
     if($wpdb->get_var("show tables like ".$wpdb->prefix . "userfile_data") != $wpdb->prefix . "userfile_data") {
      
      $sql3 = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "userfile_data(  
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	user_id int NOT NULL,
	category varchar(10) NOT NULL, 
    filename varchar (500) NOT NULL,
     );"; 
     
 
    
    } */
    
   
    if($wpdb->get_var("show tables like ".$wpdb->prefix . "userfile_data") != $wpdb->prefix . "userfile_data") {
    $sql3 = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "userfile_data(  
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	user_id int NOT NULL,
	category varchar(50) NOT NULL, 
    filename varchar (500) NOT NULL,
    description longtext NOT NULL
     );"; 
	
	}
    
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql1);
	  dbDelta($sql2);
	  dbDelta($sql3);
	   
	  
	if($wpdb->get_var("show tables like ".$wpdb->prefix . "userfile_category") == $wpdb->prefix . "userfile_category") {  
	  
	  
	  $DumpFiles = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."userfile_category");
	  
		  foreach ( $DumpFiles as $SaveFiles ) {
		  
		  $wpdb->insert($wpdb->prefix . "userfile_data", array( 'id'=>$SaveFiles->id,'user_id'=>$SaveFiles->user_id,'category'=>$SaveFiles->category,'filename'=>$SaveFiles->filename));
		  
		  
		  } 
	  
	  $wpdb->query("DROP TABLE  ".$wpdb->prefix . "userfile_category");
	  
	 }
	 
	 
	$wpdb->query( "ALTER TABLE   " . $wpdb->prefix . "userfile_category MODIFY category  VARCHAR( 50 )" ); 
	$wpdb->query( "ALTER TABLE   " . $wpdb->prefix . "userfile_data MODIFY category  VARCHAR( 50 )" ); 

	
	
	$wpdb->insert($wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'pdf','image'=>plugins_url( '/user-files/img/pdf.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'doc','image'=>plugins_url( '/user-files/img/word.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'docx','image'=>plugins_url( '/user-files/img/word.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'txt','image'=>plugins_url( '/user-files/img/word.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'jpg','image'=>plugins_url( '/user-files/img/jpg.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'png','image'=>plugins_url( '/user-files/img/jpg.jpg' , dirname(__FILE__) )));
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'gif','image'=>plugins_url( '/user-files/img/jpg.jpg' , dirname(__FILE__) )));
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'jpeg','image'=>plugins_url( '/user-files/img/jpg.jpg' , dirname(__FILE__) )));
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'xls','image'=>plugins_url( '/user-files/img/excel.jpg' , dirname(__FILE__) ))); 
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'zip','image'=>plugins_url( '/user-files/img/zip.jpg' , dirname(__FILE__) )));
	$wpdb->insert( $wpdb->prefix . "userfile_icons", array( 'id'=> '','extension'=>'rar','image'=>plugins_url( '/user-files/img/zip.jpg' , dirname(__FILE__) )));
	$wpdb->insert( $wpdb->prefix . "userfile_category", array( 'id'=> '','category'=>'misc'));

	
add_option('file_manger_show_dash', 'yes');
add_option('file_manger_show_menu', 'yes');
add_option('file_manger_allow_del', 'no');
add_option('file_manger_allow_up', 'no');
add_option('file_manger_notify', '');
add_option('file_manger_credit');
add_option('file_manger_defaultcat','misc');
add_option('file_manger_upgrade',''); 
update_option('file_manger_upgrade',$instalVersion); 
add_option('userfiles_email_subject','New File Upload');
add_option('userfiles_email_message','You have a new file upload. The file is %filename% and has been added to your %category% category.');
 
	

$wp_roles->add_cap( 'administrator', 'manage_userfiles' );
$wp_roles->add_cap( 'administrator', 'manage_userfiles_settings' ); 
}


}

function DectivateFileDir() {
global $wpdb;
global $wp_roles;

$wp_roles->remove_cap( 'administrator', 'manage_userfiles' );
$wp_roles->remove_cap( 'administrator', 'manage_userfiles_settings' ); 


$upload_dir = wp_upload_dir();



$isFolder = file_exists ($upload_dir['basedir'].'/file_uploads/');

	if ($isFolder) {
    
    
   if ($Subhandle = @opendir($isFolder)) {
				
					while (false !== ($subdirs = readdir($Subhandle))) {
										
					    $files = glob( $subdirs . '*', GLOB_MARK );
                           foreach( $files as $file ){
                           if ($file != "." and $file !=".."){
		                   unlink( $file );
                           
		                   }
	                    }
                       rmdir( $subdirs );
										 
									 }
								 }
    
 $isitGone = rmdir( $isFolder );
	
     $dir = file_exists ($upload_dir['basedir'].'/userfile_icons/');
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if ($file != "." and $file !=".."){
		unlink( $file );

		}
	}
rmdir( $dir );
 
	


	$wpdb->query('DROP TABLE '.$wpdb->prefix.'userfile_icons');
	$wpdb->query('DROP TABLE '.$wpdb->prefix.'userfile_category');
	$wpdb->query('DROP TABLE '.$wpdb->prefix.'userfile_data');  
	$wpdb->query('DROP TABLE '.$wpdb->prefix.'userfile_cats');  
	
    delete_option('file_manger_show_dash');
    delete_option('file_manger_show_menu');
    delete_option('file_manger_allow_up');
    delete_option('file_manger_allow_del');
    delete_option('file_manger_notify');
    delete_option('file_manger_credit');
   
	
     	
		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo __('The folder has been deleted','userfiles');
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo __('There was an error deleting the folder, please try again!','userfiles');
		echo '</div>';
		}
	}


}




	
function files_settings_page() {

echo '<h2>'. __('File Manager Options').'</h2>'; 


set_abase();
?>
<p>
 
<?php if ($_GET['full_uninstall']==true) {
DectivateFileDir();

} 

if(isset($_POST['removeit'])){


echo '<div id="message" class="updated highlight">';
echo __('This will delete all files, folders, categories, icons, etc. Are you sure you want to do this?','userfiles');
echo '<br /><a href ="options-general.php?page=file_manager_options&full_uninstall=true"><b>'.__('Yes','userfiles').'</a> | <a href ="options-general.php?page=file_manager_options">'.__('No','userfiles').'</b></a>';

echo '</div>';

}


if ($_POST['update']) {

$currOpts_dash =  get_option('file_manger_show_dash');
$currOpts_menu =  get_option('file_manger_show_menu');
$currOpts_up =    get_option('file_manger_allow_up');
$currOpts_del =   get_option('file_manger_allow_del');
$currOpts_notify = get_option('file_manger_notify');
$currOpts_credits=get_option('file_manger_credit');


	if ($_POST['file_manger_show_dash'] != $currOpts_dash ) {
	
		if($_POST['file_manger_show_dash']=='yes') {
		update_option('file_manger_show_dash','yes' );
		}else{
		update_option('file_manger_show_dash','no' );
		}
	}	
	

	if ($_POST['file_manger_dashcats'] != $currOpts_dash ) {
	
		update_option('file_manger_dashcats',$_POST['file_manger_dashcats'] );
		
	} 
	 
	if($_POST['file_manger_dashcount'] != $currOpts_menu ) {

		update_option('file_manger_dashcount',$_POST['file_manger_dashcount'] );
		
	}
	
	if($_POST['file_manger_allow_del'] != $currOpts_del ) {
		if($_POST['file_manger_allow_del']=='yes') {
		update_option('file_manger_allow_del','yes' );
		}else{
		update_option('file_manger_allow_del','no' );
		}
	}
	
	if($_POST['file_manger_allow_up'] != $currOpts_up ) { 
		if($_POST['file_manger_allow_up']=='yes') {
		update_option('file_manger_allow_up','yes' );
		}else{
		update_option('file_manger_allow_up','no' );
		}
	}
	
	if($_POST['file_manger_credit'] != $currOpts_credits ) { 
		if($_POST['file_manger_credit']=='yes') {
		update_option('file_manger_credit','yes' );
		}else{
		update_option('file_manger_credit','no' );
		}
	}
	
	if($_POST['file_manger_notify'] != $currOpts_notify ) { 
		update_option('file_manger_notify',$_POST['file_manger_notify'] );
	}
	
if($_POST['userfiles_email_subject'] ) { 
		update_option('userfiles_email_subject',$_POST['userfiles_email_subject'] );
	}
    
    if($_POST['userfiles_email_message'] ) { 
		update_option('userfiles_email_message',esc_attr($_POST['userfiles_email_message']) );
	}
	
	
	echo '<div id="message" class="updated fade">'.__('Settings Saved','userfiles').'</div>';
	
}

$currOpts_dash = get_option('file_manger_show_dash');
$currOpts_menu = get_option('file_manger_show_menu');
$currOpts_up = get_option('file_manger_allow_up');
$currOpts_del = get_option('file_manger_allow_del');
$currOpts_notify = get_option('file_manger_notify');
$currOpts_credits = get_option('file_manger_credit');
$currOpts_email_Sub=get_option('userfiles_email_subject');
$currOpts_email_mes=get_option('userfiles_email_message');

 ?>
 <table class="form-table">

	<tr> 
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<td><input type="checkbox" name="file_manger_show_dash" value="yes" <?php if ($currOpts_dash== 'yes'){ echo 'checked'; }?>> <?php _e('Show Dashboard Widget','userfiles'); ?><br></td>
    </tr>  
	
	<tr>
	<td><input type="checkbox" name="file_manger_show_menu" value="yes" <?php if ($currOpts_menu== 'yes'){ echo 'checked'; }?>> <?php _e('Show dashboard menu link','userfiles'); ?><br></td>
    </tr>  
	<tr>
	<td><input type="checkbox" name="file_manger_allow_up" value="yes" <?php if ($currOpts_up== 'yes'){ echo 'checked'; }?>> <?php _e('Allow users to add files','userfiles'); ?><br></td>
    </tr>  
	<tr>
	<td><input type="checkbox" name="file_manger_allow_del" value="yes" <?php if ($currOpts_del== 'yes'){ echo 'checked'; }?>> <?php  _e('Allow users to delete files','userfiles'); ?><br /></td>
    </tr> 

	<tr>
	<td><input type="checkbox" name="file_manger_credit" value="yes" <?php if ($currOpts_credits == 'yes'){ echo 'checked'; }?>> <?php  _e('Show credit link on user page? Not expected, but certainly appreciated!','userfiles'); ?><br /><em> <?php _e('All other banners and ads are visible to user file admins only.','userfiles'); ?></em><br /></td>
    </tr>  
	
	<tr>
 
	
	<tr>
	
	<td><?php echo __('Send email upload notifcations to','userfiles'); ?><input type="text" name="file_manger_notify" value="<?php echo $currOpts_notify; ?>" size="40"><br /> <em><?php echo '('.__('leave blank to not be notified of uploads','userfiles').')'; ?> </em><br></td>
    </tr> 
	
    <tr><td>
     <?php echo __('User File Manager supports custom email notifications to be sent out when you upload a file for a user. Variables allowed are','userfiles'); ?> <br />
    
    <b><i>%user_first%,%user_last%,%user_login%,%filename%,%category%</i></b><br />
    </td></tr><tr><td>
      <?php echo __('Email Subject','userfiles'); ?>:<input type="text" name="userfiles_email_subject" value="<?php echo $currOpts_email_Sub; ?>" />
    
    </td></tr>
    
    <tr><td>
    <textarea name="userfiles_email_message" rows="15" cols="50" > <?php echo $currOpts_email_mes; ?> </textarea>
    </td></tr>
    
    
	 
	<tr><td>
	<input type="hidden" name ="update" value="<?php echo __('update','userfiles');?>">
	<input type="submit" value="<?php _e('Save Options','userfiles'); ?>" class="button-secondary" /></td>

</form>
	
	</tr>
	
</table>
	<p>&nbsp;</p>	
	<p>&nbsp;</p>	
		
	<hr size="3px" />
		
		This will completely uninstall User File Manager. All files, folders, and settings will be lost.  You need to uninstall this before you deactivate the plugin if you wish to remove it completely.
<p>&nbsp;</p>

<form method="POST" action="options-general.php?page=file_manager_options">
	<input type="submit" name="removeit" value="<?php echo __('Uninstall User File Manager','userfiles'); ?>"  />
</form>






<?php
}

function manage_files_mainpg() {  

echo '<h2>'.__('User Files','userfiles').'</h2>'; 

set_abase();
global $wpdb;
$upload_dir = wp_upload_dir();

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

if (isset($_GET['deletefolder'])){

$dir = $_GET['deletefolder'].'/'; 
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if ($file != "." and $file !=".."){
		unlink( $file );

		}
	}
	$cleanDB= $wpdb->query("DELETE FROM ".$wpdb->prefix."userfile_data WHERE user_id ='" .$_GET['deletefolder']. "'");
    $isitGone = rmdir( $dir );


		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo __('The folder has been deleted','userfiles');
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo __('There was an error deleting the folder, please try again!','userfiles');
		echo '</div>';
		}
}
 
						
						
		if(isset($_POST['change-em']) || isset($_POST['submit'])){
			$step=1;
			unset($err);
				$CheckCount=$_POST['CheckedCount'];
                
                
				
				while($step<= $CheckCount) {	
					$upCat=$_POST['change_cat'.$step];
                    $Set_user=$_POST['changecat_user'.$step];
					$Set_file=$_POST['file'.$step];
					$Set_cat=$_POST['newCat'];
					
					$isIn = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."userfile_data WHERE user_id='".$Set_user."' AND filename='".$Set_file."'");
					
                    if($upCat == "addit" && $Set_cat !='chge'){
					
					
							if($isIn){
							
							 
							$stickit = $wpdb->update($wpdb->prefix . "userfile_data", array( 'category'=>$Set_cat),array( 'id'=>$isIn)); 
							
							}else{
							  
							$stickit = $wpdb->insert($wpdb->prefix . "userfile_data", array( 'id'=>'','category'=> $Set_cat,'filename'=>$Set_file,'category'=> get_option('file_manger_defaultcat'),'user_id'=>$_POST['changecat_user'.$step])); 
							} 
					
                                        if (!$stickit){
                                        
                                        $err .=$Set_file.', ';
                                        
                                        }
						 
					              
					
					
					}
                    
                $Set_descr=$_POST['notes'.$step];
                $isSaved = $_POST['fileid'.$step];
                
                if (isset($Set_descr)){
				$isIn = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."userfile_data WHERE user_id='".$Set_user."' AND filename='".$Set_file."'");
               
							if($isIn){
							$stickit = $wpdb->update($wpdb->prefix . "userfile_data", array( 'description'=>$Set_descr),array( 'id'=>$isIn)); 
							
							}else{
							  
							$stickit = $wpdb->insert($wpdb->prefix . "userfile_data", array( 'id'=>'','description'=> $Set_descr,'filename'=>$Set_file,'user_id'=>$_POST['changecat_user'.$step])); 
							} 
                
               
                                 
                 }
				$step++;
				}		
			
			
						if (!$err) {
						echo '<div id="message" class="updated">';
						echo __('Changes Saved','userfiles');
						echo '</div>';
						} else{
						echo '<div id="message" class="error">';
						echo __('There was an error changing','userfiles'). ': ' .$err;
						echo '</div>';
						}
			
			
			
			}
			
	if($_POST['file_search']) {

		$file_search_terms=$_POST['file_search'];

		}	
		
	if($_POST['catsnuser']) {

		$SortByCat=$_POST['showcatsfilter']; 
		
		if($SortByCat=='chge'){
		unset($SortByCat);
		}
		
		
        $SortByUser=$_POST['foruser'];
		
			if($SortByUser=='chge'){
		    unset($SortByUser);
		     }
		
		}	
		

		
		
		
		?>
						
<form method="POST" action="admin.php?page=manage-files-main" >

<input type="text" size ="60" name="file_search" value="" />
<input type="submit" value="<?php _e('Search for File','userfiles');?>" /> 


   <b>----   <?php _e('or'); ?>   ----</b>

<select name="showcatsfilter" id="showcatsfilter">
<option value="chge" "selected" ><?php _e('Show all categories'); ?>.........</option>
<?php

$getCatList = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "userfile_category");	 

foreach($getCatList as $breakfast) {

echo '<option value="'.$breakfast->category.'">'. $breakfast->category .'</option>';


}

echo '</select>   ';
?>

<select name="foruser" id="foruser">
<option value="chge" "selected" ><?php _e('For all users'); ?>.....</option>
<?php

$szSort = "user_login";

$aUsersID = $wpdb->get_col( $wpdb->prepare(
	"SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY %s ASC", $szSort ));	 

foreach($aUsersID as $iUserID) {

$user_info = get_userdata( $iUserID );

echo '<option value="'.$iUserID.'">'. $user_info->user_login;

if($user_info->first_name || $user_info->last_name ){
				echo '(<em>'.$user_info->first_name. ' '.$user_info->last_name .'</em>)';
					}


echo '</option>';


}
echo '</select>';
?>

<input type="submit" name ="catsnuser" value="<?php _e('Filter','userfiles'); ?>" />



</form>

<?php
global $tp;
?>

<form method="POST" action="admin.php?page=manage-files-main" >
<div align="left">
<select name="newCat" id="newCat">
<option value="chge" "selected" >Change Category to.....</option>
<?php

$getCatList = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "userfile_category");	 

foreach($getCatList as $breakfast) {

echo '<option value="'.$breakfast->category.'">'. $breakfast->category .'</option>';


}
?>

<input type="submit" name ="change-em" value="<?php _e('Change','userfiles');?>" />
<?php
 
echo '</select>';


?></div>



<?php

if($file_search_terms){
//Start Search 
unset($foundOne);
if ($handle = @opendir($upload_dir['basedir'].'/file_uploads')) {


while ( ($file = readdir($handle))!== false) { 
		
		if ($file!=".") {
			if ($file!="..") {
				$userNum=(int)$file;
				$user_info = get_userdata($userNum); 			
					$tp=1;
					unset($endTable);
					if ($Subhandle = @opendir($upload_dir['basedir'].'/file_uploads/'.$userNum)) {
						while (false !== ($files = readdir($Subhandle))) {
							if ($files!=".") {
								if ($files!="..") {
								
								$FileCheck = strpos(strtolower($files),strtolower($file_search_terms));
								
					
								if($FileCheck === 0 || $FileCheck > 0){
								$foundOne = true;
								if($tp==1){
								$endTable=true;
								echo '<table class="widefat">';
								$userNum=(int)$file;
								$user_info = get_userdata($userNum); 
								echo '<thead>';
								echo '<th width "70%"><u>'.__('User Login','userfiles').':</u>  '.$user_info->user_login.' | <u>'.__('User Name','userfiles').':</u>  '.$user_info->first_name. ' '.$user_info->last_name .' <span style="font-size:10;"> (<a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'"> '.__('Delete Folder','userfiles').'</a>) </span></th><th></th><th>Date</th><th width="20%">Category</th>';
								echo '<th width ="10%"></th></thead>'; 
																					
								}
								
								$ext = pathinfo($files, PATHINFO_EXTENSION);
								$tExt =  SetIcon($ext);
								
								
								echo '<td><input type="checkbox" name="change_cat'.$tp .'" value="addit" /> <input type="hidden" name="file'.$tp.'" value="'.$files.'" ><input type="hidden" name="changecat_user'.$tp.'" value="'.$userNum.'"> <img src="'. $tExt.'" width="20" >   '.pathinfo($files, PATHINFO_FILENAME).'</td>';
								
								echo '<td>';
                          $currOpts_defcat = get_option('file_manger_defaultcat');
						
						
						
							
							$getCrntCat = $wpdb->get_var("SELECT category FROM ". $wpdb->prefix . "userfile_data WHERE filename = '".$files ."' and user_id='" .$userNum. "'");	 
								 
								if (!$getCrntCat) {	   
																		 
								echo $currOpts_defcat;	
								}else{
								
								echo $getCrntCat;
								}
								
						echo'</td>';			 
																
						echo '<td align="right"><a rel="download.png" href="admin.php?page=manage-files-main&theDLfile='.$files.'">     <img title="Download '.$files.'" src="'.plugins_url( '/user-files/img/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>   |   <a href="admin.php?page=manage-files-main&deletefile='.$files.'">     <img title="Delete '.$files.'" src="'.plugins_url( '/user-files/img/delete.png ' , dirname(__FILE__) ). '" alt="" width="20" height="20" /></a></td></tr>';
						$tp++;
								}
				
						
							} 
			
						}

					}
					
						echo '<input type="hidden" name="CheckedCount" value="'.$tp.'" />';
						if($endTable){
						echo '</table><div align="left"><input type="submit" name="submit" value="'. __('Update','userfiles').'" /></div><p>&nbsp;</p><hr size 3px width="100%"/>';
						}
						
				}
			}
		}
	}
}

if($foundOne!=true){ 
echo '<h3>';
echo __('No Files Found','userfiles');
echo'</h3>';
}

//End Search
}elseif($SortByCat || $SortByUser){


			if($SortByUser){
			//Begin sort by user only
				if(!$SortByCat){
				
				$tp=1;
				echo '<table class="widefat" >';
			
				$userNum=(int)$SortByUser;
				$user_info = get_userdata($userNum); 
				echo '<thead>';
				echo '<th width "70%"><u>'.__('User Login','userfiles').':</u>  '.$user_info->user_login.' | <u>'.__('User Name','userfiles').':</u>  '.$user_info->first_name. ' '.$user_info->last_name .' <span style="font-size:10;"> (<a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'"> '.__('Delete Folder','userfiles').'</a>) </span> </th><th></th><th>Date</th><th width="20%">Category</th>';
				echo '<th width ="10%"></th></thead>'; 
				
								
					ListAdminFiles($userNum);
			 
				echo '</table><div align="left"><input type="submit" name="submit" value="'. __('Update','userfiles').'" /></div><p>&nbsp;</p><hr size 3px width="100%"/>';
                echo '<input type="hidden" name="CheckedCount" value="'.$tp.'" />';
						
				//End sort by user only				
				}else{
				 //Sort by cat and user
				 global $tp;
				$userNum=(int)$SortByUser;
				$user_info = get_userdata($userNum);
				echo '<table class="widefat" >';				
				echo '<thead>';
				echo '<th width "70%"><u>'.__('User Login','userfiles').':</u>  '.$user_info->user_login.' | <u>'.__('User Name','userfiles').':</u>  '.$user_info->first_name. ' '.$user_info->last_name .' <span style="font-size:10;"> (<a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'"> '.__('Delete Folder','userfiles').'</a>) </span> </th><th></th><th>Date</th><th width="20%">Category</th>';
				echo '<th width ="10%"></th></thead>'; 
				 unset($found);
				 if ($Subhandle = @opendir($upload_dir['basedir'].'/file_uploads/'.$userNum)) {
				
						while (false !== ($files = readdir($Subhandle))) {
						echo '<tr>';
							if ($files!=".") {
								if ($files!="..") {
				 
				           $isCat = CatFilter($files,$SortByCat,$userNum);
						
								if($isCat){
								ListFilteredFiles($files,$userNum);
								$found=true;
								
								}
				 
						}
							}
						echo '</tr>';
						}
					
					}else{
					echo '<td><br />'.__('No Files','userfiles').'</td>';
					} 
			
				if($found!=true){
				echo '<td><br />'.__('No files found','userfiles').'<br/></td>';
				}
				echo '<input type="hidden" name="CheckedCount" value="'.$tp.'" />';
				}
				
				
				
					echo '</table><div align="left"><input type="submit" name="submit" value="'. __('Update','userfiles').'" /></div><p>&nbsp;</p><hr size 3px width="100%"/>';			
					}else{
					//Sort by cat only
					
					if ($handle = @opendir($upload_dir['basedir'].'/file_uploads')) {

					
					while ( ($file = readdir($handle))!== false) { 

							$tp=1;
							if ($file!=".") {
								if ($file!="..") { 
								unset($found);
								
									echo '<table class="widefat" >';
								
									$userNum=(int)$file;
									$user_info = get_userdata($userNum); 
									echo '<thead>';
									echo '<th width "70%"><u>'.__('User Login','userfiles').':</u>  '.$user_info->user_login.' | <u>'.__('User Name','userfiles').':</u>  '.$user_info->first_name. ' '.$user_info->last_name .' <span style="font-size:10;"> (<a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'"> '.__('Delete Folder','userfiles').'</a>) </span> </th><th></th><th>Date</th><th width="20%">Category</th>';
									echo '<th width ="10%"></th></thead>'; 
									
		if ($Subhandle = @opendir($upload_dir['basedir'].'/file_uploads/'.$userNum)) {							
										while (false !== ($files = readdir($Subhandle))) {
											echo '<tr>';
												if ($files!=".") {
													if ($files!="..") {
									 
									 $isCat = CatFilter($files,$SortByCat,$userNum);
											
													if($isCat){
													ListFilteredFiles($files,$userNum,$tp);
													$found=true;
													$tp++;
													}
									 
											}
												}
											echo '</tr>';
											}
								if($SortByCat && $found != true){
								echo '<td><br />'.__('No files found for this user in category','userfiles').' '.$SortByCat.'<br /></td>';
								}
								
								
									echo '</table><div align="left"><input type="submit" name="submit" value="'. __('Update','userfiles').'" /></div><p>&nbsp;</p><hr size 3px width="100%"/>';	
                                    
                                

									}
									
									}
								
								}
							}
					 

echo '<input type="hidden" name="CheckedCount" value="'.$tp.'" />';	
					
										
										
					
					}
		}
	
	}else{
	

if ($handle = @opendir($upload_dir['basedir'].'/file_uploads')) {
global $tp;
$tp=1;
while ( ($file = readdir($handle))!== false) { 
		
		if ($file!=".") {
			if ($file!="..") {  
			
			
			echo '<table class="widefat" >';
			
				$userNum=(int)$file;
				$user_info = get_userdata($userNum); 
				echo '<thead>';
				echo '<th width "70%"><u>'.__('User Login','userfiles').':</u>  '.$user_info->user_login.' | <u>'.__('User Name','userfiles').':</u>  '.$user_info->first_name. ' '.$user_info->last_name .' <span style="font-size:10;"> (<a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'"> '.__('Delete Folder','userfiles').'</a>) </span> </th><th></th><th>Date</th><th width="20%">Category</th>';
				echo '<th width ="10%"></th></thead>'; 
				
								
					ListAdminFiles($userNum);
			
				echo '</table><div align="left"><input type="submit" name="submit" value="'. __('Update','userfiles').'" /></div><p>&nbsp;</p><hr size 3px width="100%"/>';		
				
				//End main page list
				}
			
			}
		}
 
echo '<input type="hidden" name="CheckedCount" value="'.$tp.'" />';


}


} //End IF

?> 

</form>
<?php
}

######################
#MANAGE CATEGORIES   #
######################

function ShowCategories() {
 global $wpdb;
 
$currOpts_defcat = get_option('file_manger_defaultcat');
 
if (isset($_GET['deletecat'])){

	if($currOpts_defcat!=$_GET['deletecat']){
	$wpdb->query("DELETE FROM ". $wpdb->prefix . "userfile_category WHERE category='".$_GET['deletecat'] ."'");
    
    $wpdb->query("UPDATE ".$wpdb->prefix."userfile_data set category='".$currOpts_defcat."' WHERE category='".$_GET['deletecat']."'");
   
	}else{
	echo '<div id="message" class="error">';
	echo $currOpts_defcat .' '. __('is the default category. You cannot delete this unless you change the default category.','userfiles');
	echo '</div>';
	
	}
}
$newCat = $_POST['addcat'];



if (isset($newCat)){


$isCat = $wpdb->query( "SELECT * FROM ".$wpdb->prefix."userfile_category WHERE category='" .$newCat. "'");

		if ($isCat){
		$err=1;
			echo '<div id="message" class="error">';
	     	echo __('This is already a cataegory.','userfiles');
	     	echo '</div>';
		
		}else{
		$dCat = $wpdb->insert( $wpdb->prefix . "userfile_category", array( 'id'=> '','category'=>$newCat));

		}

	     	if ($dCat) {
	     	echo '<div id="message" class="updated">';
	     	echo $newCat . __(' has been added','userfiles' );
	     	echo '</div>';
	     	} else{			
						
			
			echo '<div id="message" class="error">';
	     	echo __('There was an error adding the category','userfiles');
	     	echo '</div>';
	     	}
		
		
}
 
$newdefcat=$_GET['defcat'];
if (isset($newdefcat)){


update_option('file_manger_defaultcat',$newdefcat);

	     	echo '<div id="message" class="updated">';
	     	echo $newdefcat.' ' . __('is now default','userfiles' );
	     	echo '</div>';


}

?>



<h3>Manage Catagories</h3>

<?php
$currOpts_defcat = get_option('file_manger_defaultcat');
$getCats = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "userfile_category");	



echo '<table class="widefat">';
foreach ($getCats as $c) {

 
echo '<tr><td>';

echo $c->category.'</td><td align="right">';
if($currOpts_defcat!=$c->category){echo '<a href="admin.php?page=files-add-cats&defcat='.$c->category.'">(Make Default)</a> | ';}
echo '<a href="admin.php?page=files-add-cats&deletecat='.$c->category.'">(Delete)</a></td></tr>';
 

}
echo '</table>';



?>
<form method="POST" action="admin.php?page=files-add-cats">
<input type="text" id="addcat" value="<?php if($err==1){echo $_POST['addcat'];} ?>" name="addcat" />
<input type="submit" name="submit" value="<?php _e('Add Category','userfiles');?>" />
</form>


<?php	
} 


function manage_files_upload() { 
echo '<p><h2>'.__('Upload Files','userfiles') .'</h2></p></p>';


set_abase();
global $wpdb;
$upload_dir = wp_upload_dir();


	if (isset($_POST['curr_user'])) {
	 
	$subDir = $_POST['curr_user'];
	
	$usFolder = file_exists ( $upload_dir['basedir'].'/file_uploads/'.$subDir); 

	if (!$usFolder) {
	mkdir ( $upload_dir['basedir'].'/file_uploads/'. $subDir, 0777 , true );
	chmod($upload_dir['basedir'].'/file_uploads/'. $subDir,0777);
	}
	
	
	$target_path = $upload_dir['basedir'].'/file_uploads/'. $subDir.'/';
	
	$target_path = $target_path . basename($_FILES['uploadedfile']['name']); 

	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
	
	$wpdb->insert( $wpdb->prefix . "userfile_data", array( 'id'=> '','user_id'=>$subDir,'category'=>$_POST['curr_cat'],'filename'=>basename( $_FILES['uploadedfile']['name'] ))); 
	
		
        
        if ($_POST['notify_user'] == 'checked') {
        
     $user_info = get_userdata($subDir);   


$usermailsubject = get_option('userfiles_email_subject');
$usermail = get_option('userfiles_email_message');
$usermail = str_replace('%user_first%',$user_info->first_name,$usermail);
$usermail = str_replace('%user_last%',$user_info->last_name,$usermail);
$usermail = str_replace('%user_login%',$user_info->user_login,$usermail);
$usermail = str_replace('%filename%',basename( $_FILES['uploadedfile']['name']),$usermail);
$usermail = str_replace('%category%',$_POST['curr_cat'],$usermail); 
//$usermail = str_ireplace('%','',$usermail); 
 $headers[] ='From:"'.get_option('blogname').'" <'.get_option('admin_email').'>';

        
        wp_mail($user_info->user_email, $usermailsubject, $usermail, $headers); 
        
        
        
        }
        
        echo '<div id="message" class="updated">';
		echo __("The file ",'userfiles').  basename( $_FILES['uploadedfile']['name']).' '. 
		__("has been uploaded to ",'userfiles').$_POST['curr_cat'];
		echo '</div>';
	} else{
		echo '<div id="message" class="error">';
		echo __("There was an error uploading the file, please try again!",'userfiles').'<br />';
		echo '</div>';
	}

	
	
	}
?> 

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] .'?page=files-add-files' ?>" method="POST">
<?php

$order = 'user_nicename';
$aUsersID = $wpdb->get_col("SELECT ID FROM $wpdb->users ORDER BY $order");

	echo __('Files for user','userfiles').': <br /><select name="curr_user" id="curr_user">';
	foreach ( $aUsersID as $iUserID ) :
	
	$user_info = get_userdata( $iUserID );  ?>


<option value=<?php echo '"'.$iUserID . '">'. $user_info->user_login.':'.$user_info->first_name.' ' .$user_info->last_name; ?> </option>

<?php
endforeach;
echo '</select><br /><p>';
 
$max_post = (int)(ini_get('post_max_size'));

?>
<table><tr><td>
Choose a file to upload, your upload limit is <?php echo $max_post; ?>M <br /> <input name="uploadedfile" type="file" /><br />

</td></tr><tr><td> 
				 <?php
					$aCats = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."userfile_category" );

						echo 'Category:  <select name="curr_cat" id="curr_cat">';
						foreach ( $aCats  as $iCats) :
						
						
                        ?>
					<option value="<?php echo $iCats->category. '">'. $iCats->category; ?> </option>

					<?php
					endforeach;
					echo '</select><br /><p>';


						?>
				 </td></tr>
				 
				 <tr><td>
                 <?php echo __('Notify User of Upload','userfiles'); ?>:<input type="checkbox" name="notify_user" value="checked" /></td></tr>
                 
                 <tr><td> </td></tr>
                 
                 <tr><td>

<input type="submit" value="<?php _e('Upload File','userfiles');?>" />
</form>
</td></tr>
</table>



<?php
}


function manage_files_userpage() {
global $wpdb;
global $id;

ob_start();

wp_enqueue_script('jquery'); 
	   wp_register_script( 'notepop', plugins_url( '/user-files/includes/js/divtoggle.js') );
       wp_enqueue_script( 'notepop' );
       wp_enqueue_style( 'ufpageend',plugins_url( '/user-files/style.css')); 

if(is_user_logged_in()){
$currOpts_credits = get_option('file_manger_credit');



		$upload_dir = wp_upload_dir();
		global $current_user;
			  get_currentuserinfo();
              
		if (isset($_GET['deletefile'])){


			
			
			
				$theDel_file=$_GET['deletefile'];

				$isitGone = @unlink($upload_dir['basedir'].'/file_uploads/'.$theDel_file); 

				$toUsFl=explode ( "/" , $theDel_file );


				$wpdb->query("DELETE FROM ".$wpdb->prefix."userfile_data WHERE user_id ='" .$toUsFl[1]. "' AND filename ='".$toUsFl[2]."'");
				
				
						if(!file_exists($upload_dir['basedir'].'/file_uploads/'.$theDel_file)){	
						
						echo '<div id="message" class="updated">';
						echo __('The file has been deleted','userfiles');
						echo '</div>';
						} else{ 
						echo '<div id="message" class="error">';
						echo __('There was an error deleting the file, please try again!','userfiles');
						echo '</div>';
						}
				
			
			
		}

	            
			
			if($_POST['sorted']){
			
			$user_file_search=$_POST['file_search'];
			$user_cat_sort= $_POST['showcatsfilter'];
			
			if(!$user_file_search){
			unset($user_file_search);
			
			}

			if($user_cat_sort == 'chge'){
			unset($user_cat_sort);
			
			}

			
			
			}

			
    
echo $_POST['addfiles'].'<br /><p>';
?>
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >


Search: <input type="text" size ="60" name="file_search" value="" />

   <b>----   <?php _e('or'); ?>   ----</b>

<select name="showcatsfilter" id="showcatsfilter">
<option value="chge" "selected" ><?php _e('Show all categories'); ?>.........</option>
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
		echo '<table class = "user_files" width="100%">';	
		echo'<thead><th>Your Files</th><th></th><th>Date</th><th>Category</th><th></th></thead>';
			if ($handle = @opendir($upload_dir['basedir'].'/file_uploads/'.$current_user->ID)) {
			$rowClass='even_files';	
			unset($found);
            $count=1;
			while (false !== ($file = readdir($handle))) {
					
					if ($file!=".") {
						if ($file!="..") { 

						if($user_file_search){				
		
							$searchMatch = strpos(strtolower($file),strtolower($user_file_search));
							
							if($searchMatch === 0 || $searchMatch >0){
							
								if ($rowClass == 'even_files'){
								$rowClass='odd_files';
								}else{
								$rowClass='even_files';
								}
							
							ListUserFiles($file,$rowClass,$current_user->ID,$count);
							$found=true;
							}
							
						}elseif($user_cat_sort){
						
						$isCat = CatFilter($file,$user_cat_sort,$current_user->ID);
						
						if($isCat){
						
							if ($rowClass == 'even_files'){
							$rowClass='odd_files';  
							}else{
							$rowClass='even_files';
							}
						 
						ListUserFiles($file,$rowClass,$current_user->ID,$count);
						$found=true;
						}
						}else{
						
							if ($rowClass == 'even_files'){
							$rowClass='odd_files';
							}else{
							$rowClass='even_files';
							}
						    
							ListUserFiles($file,$rowClass,$current_user->ID,$count);	
						
							}
						}
					}
                    $count++;
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
								
				<table class="widefat"><thead><th><h3><?php _e('Add Files'); ?></h3></th></thead>

			<?php

				$max_post = (int)(ini_get('post_max_size'));

				$MaxSet=1000000*(int)$max_post;
				?>
 
				<tr><td> 
				<form enctype="multipart/form-data" action="<?php the_permalink(); ?>" method="POST" >		
				<?php _e('Choose a file to upload, your upload limit is '); ?> <?php echo $max_post; ?>M <br />
				
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
				<input type="submit" value="<?php _e('Upload File'); ?>" />
				</form>
				</td></tr>
				<?php
				echo '</table>';
				}
}else{

echo __('You must be logged in to view files','userfiles');

}

if ($currOpts_credits =='yes'){
echo '<p>&nbsp;</p><p>'.userfiles_credit().'</p>';

}
$sFileManger = ob_get_clean();
	return $sFileManger;
  
}

####################################
#   DASHBOARD  WIDGET              #
####################################

function file_manager_dashboard() {
$currOpts_dash = get_option('file_manger_show_dash');

	if($currOpts_dash=='yes') {
	wp_add_dashboard_widget( 'my_wp_file_manager', __( 'Your Files','userfiles'),'my_wp_file_manager' );
	} 
}


function my_wp_file_manager() {

$upload_dir = wp_upload_dir();
global $current_user;
      get_currentuserinfo(); 


	if ($handle = @opendir($upload_dir['basedir'].'/file_uploads/'.$current_user->ID)) {
		while (false !== ($file = readdir($handle))) {
			
			if ($file!=".") {
				if ($file!="..") {
				
						$ext = pathinfo($file, PATHINFO_EXTENSION); 
						$tExt =  SetIcon($ext);
				
				echo '<table width="100%"><tr><td width="10%" align="left"><img src="'. $tExt.'" width="15" ></td><td width="80%">'.pathinfo($file, PATHINFO_FILENAME).'</td>';	
								
												
																
						echo '<td width="10%" align="right"><a rel="download.png" href="index.php?theDLfile='.$file.'">     <img title="'.__('Download','userfiles').' '.$file.'" src="'.plugins_url( '/user-files/img/download.png' , dirname(__FILE__) ). '"   alt="" width="20" /></td></tr></table> ';
						
						echo '<hr width="100%" size="2px" />';
				}
			} ?>
			
			<?php
		}
	}else{
	echo __('You have no files','userfiles');
	}
  
} 

####################################
#   HELP PAGE                      #
####################################
function file_uploader_help() {


 ?>
 <div class="wrap">
 <h2>Help File</h2>
<table class="widefat">

<thead><tr><td><h2>Quick Help</h2></td></tr></thead>

<tr><td> 
To allow users to upload files or delete files go to the <a href="options-general.php?page=file_manager_options">options page</a> and check the appropriate options.<br /><p>

The options to enable the File manager page and dashboard widgets are in the <a href="options-general.php?page=file_manager_options">options</a>.  The File Manager users menu item is only available in the admin area. If you wish to show the user the file list in your page you can use the shortcode.  If you have options selected for the user upload and/or delete files these options will be available on the page as well. </td></tr>

<tr><td>The sidebar widget can be placed in any area that supports widgets and will list all the files available to the logged in user. No options are available for the widget.</td> </tr>

<tr> <td>In the <a href="options-general.php?page=file_manager_options">options section </a>you can customize an email message to send to users to notify them of an uploaded file.  When you upload a file there is a checkbox that controls wether or not the user is notfified of the file</td></tr>


<tr> <td>More information and documentation can be found <a href="http://www.whereyoursolutionis.com/user-files-plugin/">here.</a></td></tr>

<tr><td>Shortcode for use in template page: [user_file_manager] </td></tr>

<tr>



</tr>
</table>


</div>

<?php

}

function getDownloads(){

if (isset($_GET['theDLfile'])){

if (is_user_logged_in()){	

	
				$upload_dir = wp_upload_dir();
				global $current_user;
					  get_currentuserinfo();
		 

					  
			$theDLfile=$_GET['theDLfile'];		  
					  
			$theDLfile_array=explode("/",$theDLfile);	  
			
			$num=count($theDLfile_array);
			

			
			if($num==1)
			{
			   $file = $_GET['theDLfile'];	
			   
			   $url=$upload_dir['basedir'].'/file_uploads/'.$current_user->ID .'/';
			   
			}
			else 
			{
				if($current_user->ID == $theDLfile_array[0] || current_user_can('manage_userfiles') ){
			
				$file = $theDLfile_array[1];
				
				$url=$upload_dir['basedir'].'/file_uploads/'.$theDLfile_array[0] .'/';
				}else{
				?>
				<SCRIPT>alert( 'This file is not yours, you do not have permission to download it. ');</script>
				<?PHP
				}
			
			}
			

			
			set_time_limit(0);

		output_file($url.$file, $file, '');
			
}else{

echo 'You must <a href="'.site_url.'/wp-admin">login</a> to download this file.';
}	
	   	
	
	}
	
	
return; 



}

/*DOWNLOAD FUNCTION */

function output_file($file, $name, $mime_type='')
{

 if(!is_readable($file)) die('File not found or inaccessible!<br />'.$file.'<br /> '.$name);
 
 $size = filesize($file);
 $name = rawurldecode($name);
 
 $known_mime_types=array(
    "pdf" => "application/pdf",
    "txt" => "text/plain",
    "html" => "text/html",
    "htm" => "text/html",
    "exe" => "application/octet-stream",
    "zip" => "application/zip",
    "doc" => "application/msword",
    "xls" => "application/vnd.ms-excel",
    "ppt" => "application/vnd.ms-powerpoint",
    "gif" => "image/gif",
    "png" => "image/png",
    "jpeg"=> "image/jpg",
    "jpg" =>  "image/jpg",
    "php" => "text/plain"
 );
 
 if($mime_type==''){
     $file_extension = strtolower(substr(strrchr($file,"."),1));
     if(array_key_exists($file_extension, $known_mime_types)){
        $mime_type=$known_mime_types[$file_extension];
     } else {
        $mime_type="application/force-download";
     };
 };
 
 @ob_end_clean(); //turn off output buffering to decrease cpu usage
 
 // required for IE, otherwise Content-Disposition may be ignored
 if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
 
 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');
 
 /* The three lines below basically make the
    download non-cacheable */
 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 
 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 {
    list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
    list($range) = explode(",",$range,2);
    list($range, $range_end) = explode("-", $range);
    $range=intval($range);
    if(!$range_end) {
        $range_end=$size-1;
    } else {
        $range_end=intval($range_end);
    }
 
    $new_length = $range_end-$range+1;
    header("HTTP/1.1 206 Partial Content");
    header("Content-Length: $new_length");
    header("Content-Range: bytes $range-$range_end/$size");
 } else {
    $new_length=$size;
    header("Content-Length: ".$size);
 }
 
 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($file = fopen($file, 'r'))
 {
    if(isset($_SERVER['HTTP_RANGE']))
    fseek($file, $range);
 
    while(!feof($file) &&
        (!connection_aborted()) &&
        ($bytes_send<$new_length)
          )
    {
        $buffer = fread($file, $chunksize);
        print($buffer); //echo($buffer); // is also possible
        flush();
        $bytes_send += strlen($buffer);
    }
 fclose($file);
 } else die('Error - can not open file.');
 
die();
}   





function set_abase(){

	if(current_user_can('manage_userfiles_settings') || current_user_can('manage_userfiles')){

	   wp_register_script( 'widgetstuff', site_url().'/wp-admin/load-scripts.php' );
       wp_enqueue_script( 'widgetstuff' );
	   wp_enqueue_style( 'rwefaervev',site_url().'/wp-admin/load-styles.php'); 

$upload_dir = wp_upload_dir();	
	?>
	
<div class="wrap">
				
			<div id="dashboard-widgets-wrap">



								<div id="dashboard-widgets" class="metabox-holder columns-2">
										<!-- BOX 1-->

								<div id="postbox-container-1" class="postbox-container">

										<div id="normal-sortables" class="meta-box-sortables ui-sortable">

											

											<div id="showverview-main" class="postbox">

												<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>User File Manager Extensions<span class="postbox-title-action"></span></span></h3>

														<div class="inside">
														<table width="100%">
														<tr><th width="50%" align="center">Userfiles Addons</th><th width="50%" align="center" >Custom Plugins and web/PC software</th></tr>
													
 
														<tr><td align="center" style="padding:10px 10px 10px 10px;">
														<br />
														<a href="http://www.whereyoursolutionis.com/user-file-manager-front-end-admin/">User Files Manager Front End Admin</a><br />
														<a href="http://www.whereyoursolutionis.com/group-files-plugin/">Group Files</a>
														<hr />
														<em>We offer custom wordpress plugins and modifications. Want something built on wordpress? We can help! </em>
        <p><a href="http://www.whereyoursolutionis.com/"><img src="http://www.whereyoursolutionis.com/ads/userfiles_img/wp-1.png" alt="WP Support" width="174" height="58" border="0" longdesc="http://www.whereyoursolutionis.com/ads/userfiles_img/wp-1.png" /></a></p>
														 </td>
														<td align="center" style="padding:10px 10px 10px 10px;"> 
														
														    <em>Get sofware made specifically for your needs. If we can't create your software your consultation is free.  </em>
        <h6><a href="http://www.whereyoursolutionis.com/services/custom-software/"><img src="http://www.whereyoursolutionis.com/ads/userfiles_img/is-1.png" alt="Innovative Solutions" width="174" height="58" border="0" longdesc="http://www.whereyoursolutionis.com/ads/userfiles_img/is-1.png" /></a>									
														
														
														
														
														</td></tr></table>
														
														
														
														</div>

												</div>		

											</div>

										</div>
										
										
								<!-- BOX 2-->
								<div id="postbox-container-2" class="postbox-container">

										<div id="normal-sortables" class="meta-box-sortables ui-sortable">

											

											<div id="showverview-main" class="postbox">

												<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Donate To Development<span class="postbox-title-action"></span></span></h3>

														<div class="inside" align="center">
													
														<p ><em>Donate for this plugin</em></p>
														<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
												<input type="hidden" name="cmd" value="_s-xclick">
												<input type="hidden" name="hosted_button_id" value="AT8H7UZ78PMC4">
												<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
												<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
												</form>
												</p>
											
												You can help support our development of this and future free plugins. 
												
  
														
														</div>

												</div>		

											</div>

										</div>
								</div>
				
			</div>	
</div>	
<div class="clear"></div>	
	
	<?php
	
	}

}


####################################
#   ICON FUNCTIONS                 #
####################################

function SetIcon($e){
global $wpdb;



$GetIcon = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix . "userfile_icons WHERE extension = '". $e ."'");	


if ($GetIcon) {

return $GetIcon->image;

}else{
return plugins_url( '/user-files/img/unknown.jpg' , dirname(__FILE__) );

}



}

function Icon_management()  {
global $wpdb;

$upload_dir = wp_upload_dir();

if (isset($_POST['submit'])){
unset($err1);
unset($err2);


		if (isset($_POST['extension']) || isset($_FILES['uploadedicon'])) {
					
					$already = $wpdb->query("SELECT * FROM ".$wpdb->prefix."userfile_icons WHERE extension ='".$_POST['extension']."'");
					
					if (empty($already)){
							$target_path = $upload_dir['basedir'].'/userfile_icons/';
							
							$target_path = $target_path . basename($_FILES['uploadedicon']['name']); 
								
							if(move_uploaded_file($_FILES['uploadedicon']['tmp_name'], $target_path)) {
								echo '<div id="message" class="updated">';
								echo __('The file','userfiles').' '.  basename( $_FILES['uploadedicon']['name']).' '. __('has been uploaded','userfiles').'<br />';
						
								$wpdb->insert($wpdb->prefix . "userfile_icons", array( 'id'=>'','image'=>$upload_dir['baseurl'].'/userfile_icons/'.basename( $_FILES['uploadedicon']['name']),'extension'=>str_replace('.','',$_POST['extension']) )); 
						
								echo '</div>';
								
						
								}else{
								
																
									echo '<div id="message" class="error">'; 
									echo __('There was an error uploading the file, please try again!','userfiles') .'<br />';
									echo '</div>';
									
										if (isset($_POST['extension'])){
										$err2=2;
										}else{
										$err2=1;
										}
										
										if (isset($_FILE['uploadedicon'])){
										$err1=2;
										}else{
										$err1=1;
										}
									
									}
								
 
					}else{
					echo '<div id="message" class="error">'; 
					echo __('Extension already exists, please delete the current extension set before uploading a new one.','userfiles') .'<br />';
					echo '</div>';
					
				if (isset($_POST['extension'])){
				$err2=2;
				}else{
				$err2=1;
				}
				
				if (isset($_FILE['uploadedicon'])){
				$err1=2;
				}else{
				$err1=1;
				}
					
					
					}
					
					
				}else{
				
				if (isset($_POST['extension'])){
				$err2=2;
				}else{
				$err2=1;
				}
				
				if (isset($_FILE['uploadedicon'])){
				$err1=2;
				}else{
				$err1=1;
				}

							
				        echo '<div id="message" class="error">'; 
						echo __('You must have both a file and extension','userfiles');
						echo '</div>';
				
				}
					
} 

/*Delete Icon */
if (isset($_GET['deleteicon'])){

$goAwayIcon = $wpdb->query("DELETE FROM ".$wpdb->prefix."userfile_icons WHERE extension ='".$_GET['deleteicon']."'");

	if ($goAwayIcon) {
	echo '<div id="message" class="updated">'; 
	echo __('File Deleted','userfiles');
	echo '</div>';
	}else{

	echo '<div id="message" class="error">'; 
	echo __('There was an error deleting this extension','userfiles');
	echo '</div>';
	}

}
 

set_abase();
$getIcons = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix . "userfile_icons ORDER BY image");	

echo '<table class="widefat"><tr><thead><th>'.__('Icons','userfiles').'</th><th></th></tr></thead>';
foreach ($getIcons as $icon) {

 
echo '<tr><td><img src="'.$icon->image.'" width="20px" />   ' .$icon->extension.'</td><td align="right"><a href="admin.php?page=files-add-icons&deleteicon='.$icon->extension.'">(Delete)</a></td></tr>';
 

}
echo '</table>';

?>


<hr size="2px" width="100%" /><p>&nbsp;</p>


<table class="widefat">
<thead><tr><th><?php echo __('New Icon','userfiles'); ?> </th><th></th></tr></thead>
<tr><td><?php echo __('Upload Icon file','userfiles'); ?> </td><td></td></tr>	

<form enctype="multipart/form-data" action="admin.php?page=files-add-icons" method="POST">
<tr><td> <input  name="uploadedicon" type="file" <?php if($err1 == 2){echo 'value="'.$_FILE['uploadedicon'].'"';}elseif($err==1){echo 'class="highlight"';}  ?>/></td>

<td> <?php echo __('Extension','userfiles'); ?> :<input type="text" name="extension" <?php if($err2 == 2 ){echo 'value="'.$_POST['extension'].'"';}elseif($err==1){echo 'class="highlight"';} ?> /></td></tr>

<tr><td></td><td><input type="submit" name="submit" value="<?php echo __('Add','userfiles'); ?> " />

</td></tr>
</form>			 
</table>


<?php
}

function FTP_Paths(){
unset($search);

							
set_abase();
?>

<p>&nbsp;</p>
<?php
global $wpdb;
$upload_dir = wp_upload_dir();
echo __('Files can be uploaded to the users folder via FTP as well as through the upload page. If the users has no folder press create folder to create the users file folder.','userfiles').'</p>';



if (isset($_POST['createFolder'])){

mkdir (  $upload_dir['basedir'].'/file_uploads/'.$_POST['createFolder'], 0777 , true );
chmod($upload_dir['basedir'].'/file_uploads/'.$_POST['createFolder'], 0777);

}



echo '<table class="widefat">';
 echo '<thead><th><h4>'.__('User Paths for FTP upload','userfiles').'</h4><h6>'.__('User files folder is located','userfiles').' '.$upload_dir['basedir'].'/file_uploads/</h6></th><th>'.__('Folder Number','userfiles').'</th></tr></thead>';

$szSort = "user_login";

$aUsersID = $wpdb->get_col( $wpdb->prepare(
	"SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY %s ASC", $szSort ));
		

				
	
			foreach ( $aUsersID as $iUserID ) :
			$user_info = get_userdata( $iUserID );

			   echo '<tr><td>'.$user_info->user_login;

				if($user_info->first_name || $user_info->last_name ){
				echo '(<em>'.$user_info->first_name. ' '.$user_info->last_name .'</em>) </td>';
					}

				$isFolder = file_exists ( $upload_dir['basedir'].'/file_uploads/'.$iUserID); 

				if (!$isFolder) {
				
				echo '<td><form method="POST" action="admin.php?page=files-see-ftp"><input type="hidden" name="createFolder" value="'.$iUserID.'" /><input type="submit" value="';echo __('Create Folder','userfiles');
                echo'" /></form></td></tr>';				
				
				}else{
				echo '<td>'.$iUserID.'</td></tr>';
				}
endforeach; 


	echo '</table>';
 
}





function userfiles_credit(){


return 'User File Manger created by <a href="http://www.whereyoursolutionis.com">Innovative Solutions</a>';

}


function getDeleted($aVars) {
    $aVars[] = "deletefile";    
    return $aVars;

}







?>