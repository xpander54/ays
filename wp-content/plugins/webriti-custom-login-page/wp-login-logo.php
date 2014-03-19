<?php
/**
 *
 * Plugin Name: Webriti Custom Login
 * Version: 0.3
 * Description: Webriti Custom Login plugin allows admin to customize WordPress admin login page.
 * Author: Webriti WordPress Themes & Plugins Shop
 * Author URI: http://www.webriti.com
 * Plugin URI: http://www.webriti.com

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

//plugin install script
register_activation_hook( __FILE__, 'WpLogoInstallScript' );
function WpLogoInstallScript() {
    require_once('install-script.php');
}

// Translate all text & labels of plugin ###
add_action('plugins_loaded', 'TranslateWpLoginLogo');
function TranslateWpLoginLogo() {
    load_plugin_textdomain('WebritiCustomLoginTD', FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
}

// Admin dashboard Menu Pages For WP Login Logo Plugin
add_action('admin_menu','wp_login_log_menu');
function wp_login_log_menu() {
    // Wp Login Logo Page in Settings menu
    $SubMenu = add_submenu_page( 'options-general.php', 'Webriti Custom Login', __('Webriti Custom Login', 'WebritiCustomLoginTD'), 'administrator', 'webriti-login', 'webriti_login_page' );
    add_action( 'admin_print_styles-' . $SubMenu, 'logo_css_js' );
}

//load plugin required css and js fiels
function logo_css_js() {
    //js
    wp_enqueue_script('jquery-ui-core', includes_url('/js/jquery/ui/jquery.ui.core.min.js'), array('jquery') );
    wp_enqueue_script('dashboard');
    wp_enqueue_script( 'theme-preview' );
    wp_enqueue_script('media-uploads-js',plugins_url('js/my-media-upload-script.js', __FILE__), array('media-upload','thickbox','jquery'));

    //color-picker css n js
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-color-picker-script', plugins_url('js/my-color-picker-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
 wp_enqueue_style('my-bootstrap', plugins_url('css/wbr_login_bootstrap.css',__FILE__));
    //css
    wp_enqueue_style('dashboard');
    wp_enqueue_style('thickbox');
}

//WP Login Logo plugin admin menu page
function webriti_login_page(){ ?>
    <style type="text/css">
        label {
            margin-right: 20px;
        }
        .logo-prv {
            max-height: 67px;
            max-width: 326px;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #e3e3e3;
            background: #f7f7f7;
            -moz-border-radius: 3px;
            -khtml-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
        }
		.img-nwsltr {
            max-height:100%;
            max-width: 100%;
            padding: 5px;
            margin-top: 10px;
            border: 7px solid #2ea2cc;
            background: #f7f7f7;
            -moz-border-radius: 3px;
            -khtml-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
        }
        .upimg {
            /*for help*/
        }

        .wrap {

            padding: 10px 10px 10px 10px;
            border-radius: 4px 4px;
        }
        .welcome-panel {
            padding: 10px 0px 5px 0px;
            margin: 5px 0;
        }
        .theme-snaps {
            width: 380px;
            height: 180px;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #e3e3e3;
            background: #f7f7f7;
            -moz-border-radius: 3px;
            -khtml-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
        }
        #dashboard_right_now table td {
            padding: 10px 0;
        }
        #TB_inline {
            text-align: center;
        }
    </style>
    <script>
        // hide n show upload button
        jQuery('#enable-custom-logo').click(function(){
            if (jQuery(this).is(':checked', true)) {
                alert(1);
            } else {
                alert(2);
            }
        });

        //settings save js function
        function savesettings() {
            var EnableLogo = jQuery('input[type="radio"]:checked').val();
            var LogoUrl = jQuery("#logo-url").val();
            var CustomBGColor = jQuery("#custom-background-color").val();
            var PostData = "action=save_logo_settings&EnableLogo=" + EnableLogo + "&LogoUrl=" + LogoUrl + "&CustomBGColor=" + CustomBGColor;
            jQuery.ajax({
                dataType : 'html',
                type: 'POST',
                url : ajaxurl,
                cache: false,
                data : PostData,
                complete : function() {  },
                success: function(data) {
                    alert("<?php _e("Settings successfully saved.", "WebritiCustomLoginTD"); ?>");
                }
            });
        }
        //reset plugin settings
        function resetsettings() {
            var PostData = "action=reset_logo_settings";
            jQuery.ajax({
                dataType : 'html',
                type: 'POST',
                url : ajaxurl,
                cache: false,
                data : PostData,
                complete : function() {  },
                success: function(data) {
                    alert("Settings successfully reset.");
                }
            });
        }
    </script>	
	<style>
	.nav-tab-active, .nav-tab-active:hover {
color: black;
background:#fff;
border-color: #CCC;
border-bottom-color: #F1F1F1;
}
.nav-tab{
background:rgb(47, 150, 180);
color: #fff;
}
.nav-tab-active{
color: black;
background:#fff;
border-color: #CCC;
border-bottom-color: #F1F1F1;
}

	</style>
    <div class="wrap">
        <div class="welcome-panel" id="welcome-panel">
            <div class="welcome-panel-content">
                <h3>Webriti Custom Login</h3>
                <p><strong>Super Simple & Fast Way To Customize WordPress Admin Login Page</strong></p>
            </div>
        </div>
		<?php
		 $current = $_GET['tab'];
		if(!$current) $current = "homepage";
		$tabs = array( 'homepage' => 'Custom Login Settings', 'pro' => 'Free Wordpress Themes');
		 echo '<h3 class="nav-tab-wrapper" style="width:100%">';
                                    foreach( $tabs as $tab => $name )
									{
										
										
										$class = ( $tab == $current ) ? ' nav-tab-active' : '';
                                        echo "<a class='nav-tab$class' href='?page=webriti-login&tab=$tab' style='font-weight:200;
font-size:16px;line-height:24px;'>$name</a>&nbsp;";
										
                                    }
                                    echo '</h3>';
		?>
		<?php 
 if(isset($_GET['tab'])) 
	$SeletedTab = $_GET['tab'];
 else
	$SeletedTab = 'homepage';
	
 if ($SeletedTab=='homepage') { ?>
        <div id="dashboard-widgets-wrap">

        <div class="metabox-holder columns-2" id="dashboard-widgets">
            <!--left side panel-->
            <div class="postbox-container" id="postbox-container-1" style="width: 70%">
                <div class="meta-box-sortables ui-sortable" id="normal-sortables"><div class="postbox " id="dashboard_right_now">
                    <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 class="hndle"><span><?php _e("Login Page Customization & Settings", "WebritiCustomLoginTD"); ?></span></h3>
                        <div class="inside" style="padding-right:10px">
                            <?php
                                $Settings = get_option('wp_login_logo_settings');
                                $EnableLogo = $Settings['enable_logo'];
                                $LogoUrl = $Settings['logo_url'];
                                $CustomBGColor = $Settings['custom_bg_color'];
                            ?>
                            <?php add_thickbox(); ?>
                            <div id="check-preview" style="display:none; text-align: center;">
                                <iframe src="<?php echo site_url()."/wp-login.php"; ?>" style="width:500px; height:500px; margin-left: 70px; margin-top: 10px;" ></iframe>
                            </div>
                           
                            <table style="margin-left:10px;margin-right:20px">
                                <tbody>
                                    <tr>
                                        <td><label><?php _e("Enable Customization", "WebritiCustomLoginTD"); ?></label></td>
                                        <td><input id="enable-custom-logo" name="enable-custom-logo" type="radio" value="yes" <?php if($EnableLogo == 'yes') echo "checked='checked'"; ?>> <?php _e("Yes", "WebritiCustomLoginTD"); ?>
                                            <input id="enable-custom-logo" name="enable-custom-logo" type="radio" value="no" <?php if($EnableLogo == 'no') echo "checked='checked'"; ?>> <?php _e("No", "WebritiCustomLoginTD"); ?></td>
                                    </tr>
                                    <tr>
                                        <td><label><?php _e("Upload Custom Logo", "WebritiCustomLoginTD"); ?></label></td>
                                        <td>
                                            <input type="text" id="logo-url" placeholder="No media selected!"  readonly="readonly" value="<?php if($LogoUrl) echo $LogoUrl; ?>" />
                                            <input type="button" id="upload-logo" class="button upimg" value="Upload Logo"/>
                                            <div id="img-prev">
                                                <img src="<?php echo $LogoUrl; ?>" class="logo-prv" id="logo-img-prv" alt="" <?php if($LogoUrl == "") echo "style='display:none;'"; ?>>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label><?php _e("Custom Background Color", "WebritiCustomLoginTD"); ?></label></td>
                                        <td><input id="custom-background-color" name="custom-background-color" type="text" value="<?php echo $CustomBGColor; ?>" class="my-color-field" data-default-color="#ffffff" /></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                            <input id="save-logo-settings" name="save-logo-settings" class="button-primary button-large" onclick="return savesettings();" type="button" value="<?php _e("Save Settings", "WebritiCustomLoginTD"); ?>">
                                            <input id="reset-logo-settings" name="reset-logo-settings" class="button-primary button-large" onclick="return resetsettings();" type="button" value="<?php _e("Reset", "WebritiCustomLoginTD"); ?>">
                                        </td>
                                    </tr>
									<tr>
									<td>
									</td>
									<td>
									 <a  href="#TB_inline?width=500&height=510&inlineId=check-preview" class="button button-primary button-hero load-customize   thickbox"><?php _e("Check Preview", "WebritiCustomLoginTD"); ?></a>
									 </td>
									</tr>
                                </tbody>
                            </table>
                            <br class="clear">
                        </div>
                    </div>
                </div>
            </div>


            <!--rigth side panel-->
            <!--<div class="postbox-container" id="postbox-container-2">
                <div class="meta-box-sortables ui-sortable" id="side-sortables"><div class="postbox " id="dashboard_quick_press">
                        <div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Webriti Premium Plugins & Themes Shop</span></h3>
                        <div class="inside" style="text-align: center;">
                            <p><strong>Our Recently WordPress Premium Themes</strong></p>
                            <ul>
                                <li><h3>Spa Salon</h3></li>
                                <li><img class="theme-snaps" src="<?php /*echo plugins_url("other-snaps/spasalon.png", __FILE__); */?>" /></li>
                                <br>
                                <li><h3>Busiprof</h3></li>
                                <li><img class="theme-snaps" src="<?php /*echo plugins_url("other-snaps/busiprof.png", __FILE__); */?>" /></li>
                                <br>
                                <li><h3>Rambo (coming soon)</h3></li>
                                <li><img class="theme-snaps" src="<?php /*echo plugins_url("other-snaps/rambo.png", __FILE__); */?>" /></li>
                            </ul>
                            <br>
                            <a href="http://www.webriti.com" target="_blank" class="button button-primary button-large">Checkout All Themes At Webriti.Com</a>
                        </div>
                    </div>
                </div>
            </div>-->
        <div class="clear"></div>
        </div>
    </div>
		<?php
		}
		if ($SeletedTab=='pro') {
		?>
		
		<?php require_once('login_pro.php'); ?>
		<?php
		}
}


//save plugin settings
add_action("wp_ajax_save_logo_settings", "savelogosettings");
function savelogosettings() {
    if(isset($_POST['action']) == "save_logo_settings") {
        print_r($_POST);
        $EnableLogo = $_POST['EnableLogo'];
        $LogoUrl = $_POST['LogoUrl'];
        $CustomBGColor = $_POST['CustomBGColor'];
        $Settings = array(
            'enable_logo' => $EnableLogo,
            'logo_url' => $LogoUrl,
            'custom_bg_color' => $CustomBGColor
        );
        update_option('wp_login_logo_settings', $Settings);
    }
}

//reset plugin settings
add_action("wp_ajax_reset_logo_settings", "resetlogosettings");
function resetlogosettings() {
    if(isset($_POST['action']) == "reset_logo_settings") {
        $Settings = array(
            'enable_logo' => "no",
            'logo_url' => "",
            'custom_bg_color' => ""
        );
        update_option('wp_login_logo_settings', $Settings);
    }
}


//loading logo settings
function applying_wp_custom_login_settings() {
    $Settings = get_option('wp_login_logo_settings');
    $EnableLogo = $Settings['enable_logo'];
    $LogoUrl = $Settings['logo_url'];
    $CustomBGColor = $Settings['custom_bg_color'];
    if($EnableLogo == 'yes') { ?>
        <style type="text/css">
        <?php
        if($CustomBGColor != "") { ?>
            body {
                background-color: <?php echo $CustomBGColor; ?> !important;
            }
        <?php
        }
        if($LogoUrl != "") {
        ?>
            body.login div#login h1 a {
                background-image: url('<?php echo $LogoUrl; ?>');
                max-height: 67px;
                max-width: 326px;
                width: auto;
                overflow: hidden;
            }
        <?php
        } ?>
            .login #backtoblog a {
                text-shadow: none;
            }
            .login #nav a {
                text-shadow: none;
            }
        </style><?php
    }
}
add_action( 'login_enqueue_scripts', 'applying_wp_custom_login_settings' );

