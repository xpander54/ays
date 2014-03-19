<?php


class userfilesList extends WP_Widget {
   
    function userfilesList() {
        parent::WP_Widget(false, $name = 'User Files File List');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
			  
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; 
                     
				        $upload_dir = wp_upload_dir();
             $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
     if($user_id != 0){               
	if ($handle = @opendir($upload_dir['basedir'].'/file_uploads/'.$user_id)) {
		while (false !== ($file = readdir($handle))) {
			
			if ($file!=".") {
				if ($file!="..") {
				
						$ext = pathinfo($file, PATHINFO_EXTENSION); 
						$tExt= SetIcon($ext);
                         if (strpos($url,'?') ==false){
                        $dnlLink = curPageName().'?theDLfile='.$file;
                        
                        }else{
                        
                        $dnlLink = curPageName().'&theDLfile='.$file;
                        }
		
				
				echo '<img src="'. $tExt.'" width="15" ><a rel="download.png" href="'.$dnlLink .'"> '.pathinfo($file, PATHINFO_FILENAME).'</a><br />';	
						
						echo '<hr width="100%" size="2px" />';
				}
			}
		}
        
	}else{
	echo __('You have no files','userfiles');
	}  
 
				}else{

			echo __('You must log in to view files','userfiles');
					}
                   
                
                
                
				  	
                          echo $after_widget; ?>
        <?php
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


class userfilesUpload extends WP_Widget {
    
    function userfilesUpload() {
        parent::WP_Widget(false, $name = 'User Files Uploader');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget;?>
              
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; 
                        
                   //global $messageGo;
                     echo $_POST['addfiles'].'<br /><p>';
                             
                    //Begin build uploader

                        
                        $current_user = wp_get_current_user();
                        $user_id = $current_user->ID;
                        if($user_id != 0){ 
?> 

			<?php
                global $wpdb;
				$max_post = (int)(ini_get('post_max_size'));

				$MaxSet=1000000*(int)$max_post;

				?>

				
				<form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="POST" >		
				<?php _e('Choose a file to upload, your upload limit is '); ?> <?php echo $max_post; ?>M <br />
				
				 <p>&nbsp;</p>
                 <input name="uploadedfile" size="10%" type="file" /><br />
				 <input type="hidden" name="addfiles" value="addfiles" />
				 
			
				 <?php
                   $currOpts_defcat = get_option('file_manger_defaultcat');
					$aCats = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."userfile_category" );
                    ?>    
						 <select name="widge_cat" id="widge_cat">
                         <option value="<?php echo $currOpts_defcat; ?>">--Choose Category--</option>
                        <?php
						foreach ( $aCats  as $iCats) :
						
						
                        ?>
					<option value="<?php echo $iCats->category. '">'. $iCats->category; ?> </option>

					<?php
					endforeach;
					echo '</select><br /><p>';
                    

						?>
				
				<div align="right"><input type="submit" value="<?php _e('Upload File'); ?>" /></div>
				</form>
				
				<?php
			
                          }else{
                          
                          echo __('You must login to upload files','userfiles');
                          }
                       //End Build uploader
                        echo $after_widget; ?>
        <?php
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






add_action('widgets_init', create_function('', 'return register_widget("userfilesUpload");'));
add_action('widgets_init', create_function('', 'return register_widget("userfilesList");'));