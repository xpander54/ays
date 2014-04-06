<?php



########################
#
# Sidebar widget
#
########################


class groupfilesList extends WP_Widget {
   
    function groupfilesList() {
        parent::WP_Widget(false, $name = 'Group Files List');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
 global $wpdb;
$upload_dir = wp_upload_dir();
global $current_user;
get_currentuserinfo(); 

		echo $before_widget; 
			  
			  if ( $title )
                        echo $before_title . $title . $after_title; 
                     if( is_user_logged_in() ){
					 

$t=1;


		$getGroups = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."groupfile_groups");


			



			foreach ($getGroups as $grpFiles){

				$isPerms=str_replace(' ','_',strtolower($grpFiles->group_name));



					if(current_user_can($isPerms)){
					
					
				
				?>
				<h5><a href="javascript:Void(0);" onclick="DashToggle('groupfiles<? echo $t; ?>');" ><?php echo ucwords($grpFiles->group_name); ?></a></h5>

				<div id="groupfiles<? echo $t; ?>" style="display:none;">
				<table class="widefat">

				<thead><tr><th>File Name</th><th>Actions</th></tr></thead>

				<?php 

				$getFiles = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."groupfile_files WHERE group_id='".$grpFiles->id."'");
				 $folder = $upload_dir['basedir'].'/groupfile_uploads/'.$isPerms.'/';

				$loosefiles = glob($folder.'/*.*');

						foreach ($getFiles as $files){
						
						$ext = pathinfo($files->filename, PATHINFO_EXTENSION);
						
						$tExt =  GFIcon($ext);
						
						echo '<tr><td ><img src="'. $tExt.'" width="'.get_option('groupfile_iconsize').'" >  '. pathinfo($files->filename, PATHINFO_FILENAME) .'</td><td><a rel="download.png" href="'.site_url().'/?p='.$id.'&IamAFile='.$files->filename .'">     <img title="Download '.$files->filename.'" src="'.plugins_url( '/images/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a> ';
						
						
						
						
						if($grpFiles->can_delete=='on'){ echo '<a href="'.site_url().'/?p='.$id.'&deleteFile='.$files->id.'&type=base"><img title="Delete '.$files->filename.'" src="'.plugins_url( '/images/delete.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>'; }
						
						
						echo ' </td></tr>';
						 
						}
						
						foreach ($loosefiles as $file){
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						
						$tExt =  GFIcon($ext);
						
							echo '<tr><td><img src="'. $tExt.'" width="'.get_option('groupfile_iconsize').'" > '. pathinfo($file, PATHINFO_FILENAME) .'</td><td><a rel="download.png" href="'.site_url().'/?p='.$id.'&IamAFile='.$isPerms.'/'.$file .'"">     <img title="Download '. basename($file).'" src="'.plugins_url( '/images/download.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a><td></td>  ';
						
						if($grpFiles->can_delete=='on'){ echo '<a href="'.site_url().'/?p='.$id.'&deleteFile='.$grpFiles->id.'&file='. basename($file) .'"><img title="Delete '.basename($file).'" src="'.plugins_url( '/images/delete.png' , dirname(__FILE__) ). '"   alt="" width="20" height="20" /></a>'; }
						
						
						
						echo '</td></tr>';	
						}
						
						
						
						


				?>








				</table></div>	<br />	
						
					

				<?php
					}



				}



					 
					 
					 
					 
					 
					 
				}else{

			echo __('You must log in to view files','groupfiles');
					}
                   
                
                
                
				  	
                          echo $after_widget; 
						  
	}

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }

} 

?>