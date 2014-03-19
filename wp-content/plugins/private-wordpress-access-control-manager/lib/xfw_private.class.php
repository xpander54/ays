<?PHP

/*
	This is the X-Blogs Plugin Framwork
*/

class xfw_private extends xfw
{
	public $brute_log;
	public $blog;
	public $ip;

	function __construct($menu, $name, $nice_name, $level, $menu_name, $hook, $defaults)
	{
		if(!defined('XFW_LOADED'))
			define('XFW_LOADED', true);

		$this->defaults		= $defaults;
		$this->menu		= $menu;
		$this->name		= $name;
		$this->nice_name	= $nice_name;
		$this->level		= $level;
		$this->hook		= $hook;
		$this->blog		= get_current_site();

		$this->load_lang();

		$this->menu_name	= __($menu_name,$this->name);
		$this->get_options($defaults);
		
		if(is_file(XFW_TMP_PATH . '/locksecurity.php'))
		{
			if($this->options['enable'] == '')
				$this->options['norestrictions'] = true;

			$this->options['enable'] = true;
			$this->options['locksecurity'] = true;
			include(XFW_TMP_PATH . '/locksecurity.php');
		}

		if($this->options['enable'] == true)
		{
			if(is_file(XFW_TMP_PATH . '/ip.php'))
				include(XFW_TMP_PATH . '/ip.php');
		
			if($this->options['requestfilter']==true)
				add_filter('init', array(&$this,'request_filter'));

			if($this->options['norestrictions']!=true)
			{
				if(!defined('SC_NOCACHE'))
					define('SC_NOCACHE', true);

				add_filter('init', array(&$this,'login'));
				add_action('get_header', array(&$this,'auth'), 9);
				add_action('do_robots', array(&$this,'auth'), 0);
				add_action('do_feed_rss2', array(&$this,'auth'), 0);
				add_action('do_feed_rss', array(&$this,'auth'), 0);
				add_action('do_feed_atom', array(&$this,'auth'), 0);
				add_action('do_feed_rdf', array(&$this,'auth'), 0);
				add_action('do_feed_comments_rss2', array(&$this,'auth'), 0);

				if($this->options['hidecat']==true || $this->options['hidetag']==true || $this->options['hidepost']==true)
					add_filter('pre_get_posts', array(&$this, 'filter_posts'));

				if($this->options['cathidden']!=false)
					add_action('widget_categories_args', array(&$this, 'filter_tax'));

				if($this->options['taghidden']!=false)
					add_action('widget_tag_cloud_args', array(&$this, 'filter_tax'));
			}

			if($this->options['loginjs']==true)
			{
				add_action('login_form', array(&$this,'secure_login'));
				if(isset($_POST['secure_password']) && $_POST['pwd'] == '')
				{
					require_once('des.class.php');
					$_POST['key'] = $this->get_login_key();
					$_POST['pwd'] = des ($this->get_login_key(), hexToString($_POST['secure_password']), 0, 1, $this->get_login_iv());
				}
			}

			if($this->options['strongpass']==true)
				if(isset($_POST['pass1']) && $_POST['pass1'] != '' && $this->pass_strengh($_POST['pass1'])!=true)
					wp_die(__('<strong>ERROR</strong>: Your password is weak or unsecure. Please go back and select a better one!', $this->name));

			if($this->options['norealerror'] == true)
				add_action('login_errors', array(&$this,'login_errors'));

			if($this->options['noapprove'] != true)
			{
				add_action('edit_user_profile', array(&$this,'edit_user'));
				add_action('profile_personal_options', array(&$this,'edit_user'));
				add_action('profile_update', array(&$this,'edit_user_submit'));
			}

			if($this->options['bruteforce'] == true)
			{
				add_action('wp_login_failed', array(&$this,'brute_force'));
				add_action('init', array(&$this,'check_brute_force'));
			}

			if($this->options['headerswp'] == '1')
				add_action('wp_head', 'wp_generator');
			else if($this->options['headerswp'] == '2')
			{
				remove_action('wp_head', 'wp_generator');
				add_action('wp_head', array(&$this,'anonym_wp_generator'));
			}
			else if($this->options['headerswp'] == '3')
				remove_action('wp_head', 'wp_generator');

			if($this->options['headerswlw'] == '1')
				add_action('wp_head', 'wlwmanifest_link');
			else if($this->options['headerswlw'] == '2')
				remove_action('wp_head', 'wlwmanifest_link');

			if($this->options['headersrsd'] == '1')
				add_action('wp_head', 'rsd_link');
			else if($this->options['headersrsd'] == '2')
				remove_action('wp_head', 'rsd_link');

			if($this->options['version']!='' && $this->options['version']<$this->defaults['version'])
				add_action('admin_notices', array(&$this,'update'));

			if(is_multisite() && defined('PRIVATE_ALLOW_DIRECT_SUBSCRIPTION') && PRIVATE_ALLOW_DIRECT_SUBSCRIPTION == true)
			{
				add_action('signup_hidden_fields', array(&$this, 'subscribe_to_blog'));
				add_filter('add_signup_meta', array(&$this, 'subscribe_to_blog_meta'));
				add_action('myblogs_allblogs_options', array(&$this,'subscribe_to_new_blogs'));

				if(defined('PRIVATE_FORCE_MAIN_BLOG') && PRIVATE_FORCE_MAIN_BLOG == true)
					add_action('admin_menu', array(&$this,'subscribe_to_main_blog'));
			}
			
			add_filter('login_message', array(&$this,'login'));
		}

		if($this->options['enable']=='')
			add_action('admin_notices', array(&$this,'warning'));

		add_action('admin_menu', array(&$this, 'admin_menu'));
	}

	function auth($guest=false)
	{
		global $current_user;

		if(!is_user_logged_in() || $guest == true)
		{
			if((is_feed() || is_comment_feed()) &&  $guest != true && $this->options['feedkey']==true && isset($_GET['user']) && isset($_GET['auth']))
			{
				$auth = get_user_meta($_GET['user'], 'feed_key_'.$this->blog->id);

				if(isset($auth['0']) && $auth['0']==$_GET['auth'])
				{
					$allowed = get_user_meta($_GET['user'], 'allow_private_'.$this->blog->id);

					if($this->options['noapprove'] == true || ($this->options['noapprove'] != true && isset($allowed['0']) && $allowed['0'] >= $this->local_unix_time()))
					{
						if($this->options['capabilities'] != '' && $this->check_capabilities())
							return;
						else if($this->user_can_at_least('subscriber'))
							return;
						if($this->options['relation'] == true)
							return;
					}
				}
			}

			if(strpos($_SERVER['REQUEST_URI'], '/wp-login.php') === 0 || strpos($_SERVER['REQUEST_URI'], '/wp-signup.php') === 0)
				return true;

			if($this->options['showfeed'] != true && $guest != true)
			{
				remove_action('wp_head', 'feed_links', 2);
				remove_action('wp_head', 'feed_links_extra', 3);
			}

			if($this->options['front'] == true && is_front_page())
				return true;

			if($this->options['home'] == true && is_home())
				return true;

			if($this->options['page'] == true && is_page())
				return true;

			if($this->options['category'] == true && strpos($this->options['categories'], '!') === false && is_category())
				return true;

			if($this->options['tag'] == true && strpos($this->options['tags'], '!') === false && is_tag())
				return true;

			if($this->options['post'] == true && strpos($this->options['posts'], '!') === false && strpos($this->options['categories'], '!') === false && strpos($this->options['tags'], '!') === false && is_single())
				return true;

			if($this->options['attachment'] == true && is_attachment())
				return true;

			if($this->options['archive'] == true && is_date())
				return true;

			if($this->options['search'] == true && is_search())
				return true;

			if($this->options['feed'] == true && (is_feed() || is_comment_feed()))
				return true;

			if(is_single())
			{
				if($this->options['posts'] != '')
				{
					$posts = explode(',',$this->options['posts']);
					foreach($posts AS $post)
					{
						if($post == '')
							continue;

						if(substr($post,0,1)=='!' && is_single((int)$post) && $guest != true)
							$this->deny_access();
						else if(substr($post,0,1)=='!' && is_single((int)$post) && $guest == true)
							return false;
						else if(is_single((int)$post))
							return true;
					}
				}

				if($this->options['categories'] != '')
				{
					$categories = isset($this->options['categories']) ? unserialize($this->options['categories']) : array();

					foreach($categories AS $category)
					{
						if($category == '')
							continue;

						if(substr($category,0,1)=='!' && in_category(substr($category, 1)) && $guest != true)
							$this->deny_access();
						else if(substr($category,0,1)=='!' && in_category(substr($category, 1)) && $guest == true)
							return false;
						else if(in_category($category))
							return true;
					}
				}

				if($this->options['tags'] != '')
				{
					$tags = isset($this->options['tags']) ? unserialize($this->options['tags']) : array();

					foreach($tags AS $tag)
					{
						if($tag == '')
							continue;

						if(substr($tag,0,1)=='!' && has_tag(substr($tag, 1)) && $guest != true)
							$this->deny_access();
						else if(substr($tag,0,1)=='!' && has_tag(substr($tag, 1)) && $guest == true)
							return false;
						else if(has_tag($tag))
							return true;
					}
				}

				if($this->options['post'] == true)
					return true;
			}

			if($this->options['categories'] != '' && is_category())
			{
				$categories = unserialize($this->options['categories']);
				foreach($categories AS $category)
				{
					if($category == '')
						continue;

					if(substr($category,0,1)=='!' && is_category(substr($category, 1)) && $this->options['catsingle'] != true && $guest != true)
						$this->deny_access();
					if(substr($category,0,1)=='!' && is_category(substr($category, 1)) && $this->options['catsingle'] != true && $guest == true)
						return false;
					else if(substr($category,0,1)=='!' && is_category(substr($category, 1)) && $this->options['catsingle'] == true)
						return true;
					else if(is_category($category))
						return true;
				}
			}

			if($this->options['category'] == true && is_category())
				return true;

			if($this->options['tags'] != '' && is_tag())
			{
				$tags = unserialize($this->options['tags']);
				foreach($tags AS $tag)
				{
					if($tag == '')
						continue;

					if(substr($tag,0,1)=='!' && is_tag(substr($tag, 1)) && $this->options['tagsingle'] != true && $guest != true)
						$this->deny_access();
					if(substr($tag,0,1)=='!' && is_tag(substr($tag, 1)) && $this->options['tagsingle'] != true && $guest == true)
						return false;
					if(substr($tag,0,1)=='!' && is_tag(substr($tag, 1)) && $this->options['tagsingle'] == true)
						return true;
					else if(is_tag($tag))
						return true;
				}
			}

			if($this->options['tags'] == true && is_tag())
				return true;

			if($guest == true)
				return false;
		}
		else
		{
			if($this->auth(true) == true)
				return true;

			if((is_feed() || is_comment_feed()) && $this->options['feedkey']==true && (!isset($_GET['user']) || !isset($_GET['auth'])))
			{
				$path = parse_url($_SERVER['QUERY_STRING']);
				$auth = get_user_meta($current_user->ID, 'feed_key_'.$this->blog->id);

				if(!isset($auth['0']))
				{
					$auth = array();
					$auth['0'] = sha1(uniqid(true));
					update_user_meta($current_user->ID, 'feed_key_'.$this->blog->id, $auth['0']);
				}
				wp_redirect(get_bloginfo('url').$path['path'].'?user='.$current_user->ID.'&auth='.$auth['0'], 301);
				exit;
			}

			$allowed = get_user_meta($current_user->ID, 'allow_private_'.$this->blog->id);

			if($this->options['noapprove'] == true || ($this->options['noapprove'] != true && isset($allowed['0']) && $allowed['0'] >= $this->local_unix_time()))
			{
				if($this->options['capabilities'] != '' && $this->check_capabilities())
					return true;
				else if($this->user_can_at_least('subscriber'))
					return true;
				if($this->options['relation'] == true)
					return true;

				$this->deny_access(__('It seems there is something wrong with your capabilities.',$this->name));
			}

			$this->deny_access(__('It seems there is something wrong with your time-based authorization. You have activated this option, but did you edit <a href="/wp-admin/profile.php#allow_private">your personal settings</a>?',$this->name));
		}
		$this->deny_access();
	}

	function deny_access($error=false)
	{
		if(current_user_can($this->level))
			wp_die('<strong>' . sprintf(__('Oh Oh! "Private! Wordpress Access Manager" does not allow you to access your own blog! %s %s Please check your settings!',$this->name), $error, '<a href="/wp-admin/'.$this->menu.'?page=private_wordpress_access_manager">') . '</a></strong>');

		if(is_user_logged_in() && $this->options['denynoredirect']==true)
			wp_die($this->options['denymessage']);

		if($this->options['redirect']!='')
		{
			wp_redirect($this->options['redirect'], 301);
			exit;
		}
		if(function_exists('auth_redirect')) 
			auth_redirect();
		else
		{
			wp_redirect(get_option(‘siteurl’) . ‘/wp-login.php’);
			exit;
		}
		wp_die("You do not have enough rights to access this page, <a href='".get_option('siteurl')."/wp-login.php'>please login first</a>.<script>self.location.href='".get_option('siteurl')."/wp-login.php'</script>");
	}

	function check_capabilities()
	{
		$allowed = explode('||', $this->options['capabilities']);

		foreach($allowed as $cap)
		{
			$cap = trim($cap);

			$allowed2 = explode('&&',$cap);
			$cur_cap = 0;
			foreach($allowed2 as $cap2)
			{
				if(in_array($cap2, array('administrator', 'editor', 'author', 'contributor', 'subscriber')) && $this->user_can_at_least($cap))
					$cur_cap++;
				else if(current_user_can($cap2))
					$cur_cap++;
			}
			if($cur_cap==count($allowed2))
				return true;
		}
		return false;
	}

	function user_can_at_least($role) {
		$roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
		foreach ( $roles as $val ) {
			if ( current_user_can( $val ) )
				return true;

			if($role == $val)
				break;
		}
		return false;
	}

	function login($message)
	{
		if($this->options['link']==true || (isset($_GET['preview_link']) && current_user_can($this->level)))
			$message = $message . '<div style="position:absolute;line-height:30px;right:15px;top:0;text-align:right;z-index:10">This blog is "<a href="http://plugins.x-blogs.org/private/">Private!</a>" protected</div>';

		if($this->options['norestrictions']!=true)
			$message = stripslashes($this->options['message']) . $message;
	
		return $message;
	}

	function get_login_base()
	{
		$ip = $this->get_real_ip();
		$host = gethostbyaddr($ip);
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

		return sha1(AUTH_KEY.$ip.$host.$browser);
	}

	function get_login_key()
	{
		return substr($this->get_login_base(), 0, 24);
	}

	function get_login_iv()
	{
		return substr($this->get_login_base(), -8);
	}

	function secure_login()
	{
		echo '<script type="text/javascript">var key=\''.$this->get_login_key().'\';var iv=\''.$this->get_login_iv().'\';document.write(\'<p style="width:100%;"><label style="font-size:11px;line-height:19px;"><input name="no_secure_login" type="checkbox" id="no_secure_login" value="1" tabindex="100" /> '. addslashes(__('Don\'t encrypt my password', $this->name)) .'</label><input type="hidden" name="secure_password" id="secure_password" /></p>\')</script>';
		echo '<script src="'.get_bloginfo('url').'/'.XFW_PATH.'/js/secure.login.js" type="text/javascript"></script>';
	}

	function login_errors($error)
	{
		if($this->options['bruteforce'] == true)
		{
			$this->brute_force_log();
			$failed = isset($this->brute_log[$this->ip]) ? count($this->brute_log[$this->ip])-1 : 0;
			$attempts = ($this->options['maxattempts']-$failed) > 1 ? ($this->options['maxattempts']-$failed) : 1;
			$this->options['customerror'] = str_replace('%attempts%', $attempts, $this->options['customerror']);
		}
		unset($GLOBALS['user']->errors);
		return $this->options['customerror'];
	}

	function warning()
	{
		if(!current_user_can('manage_options'))
			return;

		if(!isset($_GET['page']) || $_GET['page'] != 'private_wordpress_access_manager')
			echo '<div id="message" class="updated fade"><strong><p><a href="'.$this->menu.'?page=private_wordpress_access_manager">'.$this->nice_name.'</a> '. __(' is active, but must be configured. Your blog may be visible to everyone on the web.', $this->name) . '</p></strong></div>';
	}

	function update()
	{
		if(!current_user_can('manage_options'))
			return;
	
		if(!isset($_GET['page']) || $_GET['page'] != 'private_wordpress_access_manager')
			echo '<div id="message" class="updated fade"><strong><p><a href="'.$this->menu.'?page=private_wordpress_access_manager">'.$this->nice_name.'</a> '. __(' has been updated. There may be some changes. Please visit the settings.', $this->name) . '</p></strong></div>';
	}

	function settings()
	{
		global $wpdb;

		if(!current_user_can($this->level))
			wp_die('<strong>' .__('Who are you and what are you doing in here?', $this->name) . '</strong>');

		if(!is_dir(XFW_TMP_PATH))
			mkdir(XFW_TMP_PATH);

		if(!empty($_POST))
		{
			$this->process();
			$this->write_security_lock();
			
			if(isset($_POST['private_restrictip']) && $_POST['private_restrictip'] == true)
				$this->write_ip_restriction();

			if(isset($_POST['private_protectplugin']) && $_POST['private_protectplugin'] == true)
				$this->write_index(ABSPATH . PLUGINDIR);
			else
				$this->delete_index(ABSPATH . PLUGINDIR);

			if(isset($_POST['private_protectthemes']) && $_POST['private_protectthemes'] == true)
				$this->write_index(WP_CONTENT_DIR . '/themes');
			else
				$this->delete_index(WP_CONTENT_DIR . '/themes');

			if(isset($_POST['private_protectincludes']) && $_POST['private_protectincludes'] == true)
				$this->write_index(ABSPATH . 'wp-includes');
			else
				$this->delete_index(ABSPATH . 'wp-includes');

			if(isset($_POST['private_removereadme']) && $_POST['private_removereadme'] == true && is_file(ABSPATH . 'readme.html'))
				rename(ABSPATH . 'readme.html', XFW_TMP_PATH . '/readme.html');
			else if(is_file(XFW_TMP_PATH . '/readme.html') && $_POST['private_removereadme'] != true)
				rename(XFW_TMP_PATH . '/readme.html', ABSPATH . 'readme.html');
		}

		if(is_file(XFW_TMP_PATH . '/locksecurity.php'))
		{
			if($this->options['enable'] == '')
				$this->options['norestrictions'] = true;

			$this->options['enable'] = true;
			$this->options['locksecurity'] = true;
			include(XFW_TMP_PATH . '/locksecurity.php');
		}

		if(is_file(XFW_TMP_PATH . '/ip.php'))
			include(XFW_TMP_PATH . '/ip.php');

		if($this->options['message'] == 'false')
			$this->options['message'] = $this->defaults['message'] = '<p class="message">' . __("Sorry, please login to view this blog.", $this->name) . '</p>';
		$this->form_header_list(array(
			__('Basic Settings', $this->name) => 8,
			__('Access Rights', $this->name) => 20,
			__('Security Settings', $this->name) => 25,
			__('Advanced Settings', $this->name) => 9,
			__('About', $this->name) => false,
		));
		$this->form_page_top(__('Basic Settings', $this->name), '1');
		$this->form_row('checkbox', __('Privacy Settings', $this->name), 'enable', __('Enable Private! Wordpress Access Control Manager', $this->name), __('Enable Private! to lock down your blog from the public. To get access to your blog, users must register. If you do not allow registrations or you blog is in a multisite environment, You must add or invite them under <strong><a href="user-new.php">Users -> Add</a></strong>. Private! is very restrictive, because it\'s better to lock some people out in error than showing important data to the wrong people. <strong>If you just enable Private! even YOU will not be able to access your blog. Please read all option descriptions carefully!</strong>', $this->name));
		$this->form_row('checkbox', __('Restrictions', $this->name), 'norestrictions', __('Do not restrict access to any page', $this->name), __('You can disable all restrictions with one click. That may be useful for debuging or if you just want to use the other features.', $this->name));
		$this->form_row('checkbox', __('Approvement', $this->name), 'noapprove', __('Do not use time based approvement', $this->name), __('By default you must approve every single user for a certain time under <strong><a href="users.php">Users -> Edit</a></strong> and on the user settings page at the bottom. You can skip this step by allowing every logged in user. If you don\'t and registration is open, make sure to tell your users, that they cannot access your blog immidiately.', $this->name));
		$this->form_row('text', __('Capabilities', $this->name), 'capabilities', '', __('By default only administrators can access your blog. You can change it to every role you like. If you want to allow every logged in user to access your blog, you can <strong>just empty this input</strong>. You can use capabilities like "manage_options" and you can combine multiple capabilities with "&&" or match only one with "||".<br /><br />Examples: "manage_options&&edit_posts&&delete_users" matches users that can manage_options, edit posts AND delete users. "manage_options||edit_posts||delete_users" will match one of the capabilities. "read_posts&&edit_posts||authors" will match any users, who can read AND edit posts OR are at least authors. If you like, you can use userlevels too, by adding "level_X" (X is the userlevel). <a href="http://codex.wordpress.org/Roles_and_Capabilities">Find more infos on the wordpress pages</a>.', $this->name));

		$this->form_row('text', __('Capabilities for editing', $this->name), 'editauth', '', __('You can change, who can edit the authorization time for single users. Please remember: The users will need at least rights to edit user profiles. The standard value is "edit_users".', $this->name), false, false, '170');

		$this->form_row('checkbox', __('Blog network', $this->name), 'relation', __('Do not check the relation to your blog', $this->name), __('This blog is part of a multisite network. You can allow access to all logged in the network, even if they are not directly related to your blog. If you do not check this, you have to add users as described above.', $this->name), false, !is_multisite());
		$this->form_row('checkbox', __('Direct subscription', $this->name), 'directsubscription', __('Allow users to subscribe directly to your blog', $this->name), __('This will change two things. First it will add a hidden field to the signup form. Every user, that signs up from your blog will be automatically added to your blog. Second it will add the ability to add himself to your blog on the "My Blogs" page.', $this->name), false, !(is_multisite() && defined('PRIVATE_ALLOW_DIRECT_SUBSCRIPTION') && PRIVATE_ALLOW_DIRECT_SUBSCRIPTION == true), '160');
		if(is_multisite() && current_user_can('update_core') && (!defined('PRIVATE_ALLOW_DIRECT_SUBSCRIPTION') || PRIVATE_ALLOW_DIRECT_SUBSCRIPTION != true))
			$this->form_row('custom', __('Information on Direct Subscriptions', $this->name), '', __('To enable this feature, you must edit the private.php file in the plugin folder. Read the comments and remove // in front of the settings define(\'PRIVATE_ALLOW_DIRECT_SUBSCRIPTION\', true) and if you want in front of define(\'PRIVATE_FORCE_MAIN_BLOG\', true) too. First option will allow webmasters to get subscriptions directly. Second option will force all users to remain a subscriber of your main blog.', $this->name), false, false, false, '160');

		$this->form_row('checkbox', __('Lock Security settings', $this->name), 'locksecurity', __('Set security settings in the whole network', $this->name), __('Private! gives you a lot of tools to increase security of your blog, but unfortunatly they are useless, if your member blogs don\'t use these features. You can force them to use them, by activating this settings. If the Private is disabled on a member blog, it will be enabled, but without any restrictions.', $this->name), false, !(is_multisite() && !$this->check_security_lock_admin()), '170');

		$this->form_row('submit', '', __('Save Changes', $this->name));
		$this->form_break(__('Allow access to parts of your blog', $this->name), 2);
		$this->form_row('checkbox', __('Front page', $this->name), 'front', __('Allow access to the front page of your blog', $this->name), __('Allow all visitors to view your blog front page. This may be your blog home page or another page you have chosen under <strong><a href="options-reading.php">Settings -> Read</a></strong>.', $this->name));
		$this->form_row('checkbox', __('Blog home page', $this->name), 'home', __('Allow access to the home page of your blog', $this->name), __('Allow all visitors to view your blog home page. This means, they can read all posts or excerpts depending on your posts settings.', $this->name));
		$this->form_row('checkbox', __('Posts', $this->name), 'post', __('Allow access to single posts', $this->name), __('Allow all visitors to access your single posts.', $this->name));
		$this->form_row('checkbox', __('Attachments', $this->name), 'attachment', __('Allow access to attachments', $this->name), __('Allow all visitors to access attachments.', $this->name));
		$this->form_row('checkbox', __('Pages', $this->name), 'page', __('Allow access to pages', $this->name), __('Allow all visitors to view pages.', $this->name));
		$this->form_row('checkbox', __('Categories', $this->name), 'category', __('Allow access to your category archives', $this->name), __('Allow all visitors to view your category archives.', $this->name));
		$this->form_row('checkbox', __('Tags', $this->name), 'tag', __('Allow access to your tag archive', $this->name), __('Allow all visitors to view your tag archives.', $this->name));
		$this->form_row('checkbox', __('Archives', $this->name), 'archive', __('Allow access to your date-based archives', $this->name), __('Allow all visitors to view your date-based archives.', $this->name));
		$this->form_row('checkbox', __('Search', $this->name), 'search', __('Allow access to your search', $this->name), __('Allow all visitors to search your blog.', $this->name));
		$this->form_row('checkbox', __('RSS &amp; Atom feed', $this->name), 'feed', __('Allow access to feeds', $this->name), __('Allow all visitors to access your feeds.', $this->name));
		$this->form_row('checkbox', __('Show feeds', $this->name), 'showfeed', __('Show feeds for not logged in users', $this->name), __('With this options, you can show all rss in your blog header. This is only useful, if they are allowed to access.', $this->name), false, false, '140');
		$this->form_row('checkbox', __('Use feed keys', $this->name), 'feedkey', __('Allow users to access your feeds with special keys', $this->name), __('Some RSS Readers might not be capable of user authentification. You can provide your feeds anyway by generating special urls for your members.', $this->name), false, false, '140');
		$this->form_row('custom', __('Categories', $this->name), $this->show_categories(), '', __('Please chose from the list, if you want to <strong>overide the category settings</strong> above.', $this->name), false, false, '150');
		$this->form_row('checkbox', __('Categories filter', $this->name), 'catsingle', __('Apply category filter only to single posts', $this->name), __('If you check this, you will enable users to browse restricted category archives, but they will not be able to see the full posts in these categories.', $this->name));
		$this->form_row('custom', __('Tags', $this->name), $this->show_tags(), '', __('Please chose from the list, if you want to <strong>overide the tags settings</strong> above.', $this->name));
		$this->form_row('checkbox', __('Tags filter', $this->name), 'tagsingle', __('Apply tag filter only to single posts', $this->name), __('If you check this, you will enable users to browse restricted tags archives, but they will not be able to see the full posts with these tags.', $this->name), false, false, '163');
		$this->form_row('text', __('Posts', $this->name), 'posts', '', __('Add the post ids (comma-seperated), you want to allow for all visitors. You can also restrict posts by prepend "!". If you want to allow access to all posts except id 1 and 2 you can add "!1,!2". <strong>WARNING: This option will overwrite category and tag options above</strong>, so you can make some posts temporarly available for public.', $this->name));
		$this->form_row('checkbox', __('Hide categories', $this->name), 'hidecat', __('Hide posts in forbidden categories on posts list', $this->name), __('Remove all Posts in restricted categories from blog home, front page, search pages etc. If you deny access to categories by default, this will remove all posts except allowed ones. A positive post filter will not apply, that means if you remove category premium and whitelist a post in this category it will not be shown. The best way to show them, is to add a new post, that links to this post or remove this category temporarly. Use this option with care!', $this->name), false, false, '150');
		$this->form_row('checkbox', __('Hide tags', $this->name), 'hidetag', __('Hide posts with forbidden tags on posts list', $this->name), __('Remove all Posts with restricted tags from blog home, front page, search pages etc. Please read information on hide categories.', $this->name), false, false, '150');
		$this->form_row('checkbox', __('Hide posts', $this->name), 'hidepost', __('Hide forbidden posts on posts list', $this->name), __('Remove all Posts that are manually restricted tags from blog home, front page, search pages etc. Please read information on hide categories.', $this->name), false, false, '150');
		$this->form_row('submit', '', __('Save Changes', $this->name));
		$this->form_break(__('Security Features', $this->name), 3);
		if($this->check_security_lock())
		{
			$this->form_row('custom', __('Info'), '', '<strong>' . __('The security options are locked through the network administrator.', $this->name) . '</strong>');
		}

		$this->form_row('checkbox', __('Filter Incoming Requests', $this->name), 'requestfilter', __('Filter incoming get requests for non admins', $this->name), __('Even Wordpress is fairly secure, there may be security problems in your plugins or themes. You can get a little bit of extra security by filtering incoming Get Requests. Some typical <a href="http://en.wikipedia.org/wiki/SQL_injection">sql injection</a> and <a href="http://en.wikipedia.org/wiki/Cross-site_scripting">xss attacks</a> will be eliminated. Normal users will not notice this. Test it here. <a href="/?admin_test=%3Cscript%3Ealert(document.cookie)%3C/script%3E">Test it here</a>.', $this->name), false, $this->check_security_lock(), '140');

		$this->form_row('checkbox', __('Filter long variables', $this->name), 'filterlong', __('Block variables longer than 255 chars', $this->name), __('Private! will block all $_GET variables, that are longer than 255 chars. This will not limit your site, but the possibilities for an attacker.', $this->name), false, $this->check_security_lock(), '170');

		$this->form_row('checkbox', __('Filter XSS Requests', $this->name), 'filterxss', __('Block XSS attacks', $this->name), sprintf(__('Private! will block most common xss requests to your blog like <a href="%s">cookie theft</a> or <a href="%s">session hijacking</a> attemps.', $this->name), 'http://en.wikipedia.org/wiki/HTTP_cookie#Cookie_theft', 'http://en.wikipedia.org/wiki/Session_hijacking'), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Filter SQL Injections', $this->name), 'filtersql', __('Block SQL injection attacks', $this->name), sprintf(__('Private! will block most common <a href="%s">sql injection</a> attempts.', $this->name), 'http://en.wikipedia.org/wiki/SQL_injection'), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Filter WP Prefix', $this->name), 'filterprefix', __('Block Requests with your sql prefix', $this->name), sprintf(__('Private! will block all requests, that contain your wp prefix "%s". This might lead to false positives, BUT it will protect your blog from most of all 0-day vulnerabilies.', $this->name), $wpdb->prefix), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Filter Fields Truncation', $this->name), 'filtertruncation', __('Block Field Truncation Attacks', $this->name), sprintf(__('Private! will block all requests containing uncommon whitespaces or other indications of whitespace or special characters to prevent <a href="%s">Field Truncation</a> Attacks.', $this->name), 'http://www.secgeeks.com/sql_server_truncation_attacks.html'), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Filter Directory Traversal', $this->name), 'filtersql', __('Block Directory Traversal Attacks', $this->name), sprintf(__('Private! will block all requests containing most common path or other <a href="%s">directory traversal</a> attacks.', $this->name), 'http://en.wikipedia.org/wiki/Directory_traversal'), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Filter File Uploads', $this->name), 'filterfiles', __('Block File Upload Attacks', $this->name), __('Private! will block all file uploads with dangerous file extensions. These attacks are quite uncommon, but possible if you use bad written plugins.', $this->name), false, $this->check_security_lock(), '170');

		$this->form_row('checkbox', __('Show 404 Error', $this->name), 'throw404', __('Throw an 404 error on bad request', $this->name), __('If activated, a bad request will lead to a normal 404 error page. An attacker might think, this is the normal behaviour.', $this->name), false, $this->check_security_lock(), '170');
		$this->form_row('checkbox', __('Log filtered requests', $this->name), 'filterlog', __('Log all bad requests', $this->name), __('If you want to know, when someone sends malicious requests to your blog, you should activate this.', $this->name), false, $this->check_security_lock(), '170');

		$this->form_row('checkbox', __('Strong passwords', $this->name), 'strongpass', __('Allow only strong passwords', $this->name), __('Wordpress helps you to create strong passwords, but it does not mind accepting weak passwords. That\'s not very useful. You can force your users to use only strong passwords.', $this->name), false, $this->check_security_lock(), '140');
		$this->form_row('checkbox', __('Login errors', $this->name), 'norealerror', __('Show custom errors on login', $this->name), __('On standard installations Wordpress tells you, if the login username is correct or not. That might be a security problem, as it gives an attacker informations about your usernames. If checked, it will display your custom message. <strong>Unlike other plugins we just don\'t hide the error, but fully remove the original.</strong>', $this->name), false, $this->check_security_lock());
		$this->form_row('text', __('Custom error', $this->name), 'customerror', '', __('This message will displayed to all users, that failed to login. If you enabled brute force protection, you can use <strong>%attempts%</strong> as a variable to display allowed attempts.', $this->name), false, $this->check_security_lock());
		$this->form_row('checkbox', __('Secure login script', $this->name), 'loginjs', __('Secure your login with javascript', $this->name), __('This will add a unobstrusive javascript to your login form. On submitting the loginform, the password will be encoded with <a href="http://en.wikipedia.org/wiki/Data_Encryption_Standard">Triple des</a>. The encryption password will be generated with details of the user and your installation. As the key is not send with your form, it will be not that easy to find out the real password.', $this->name), false, $this->check_security_lock(), '120');
		$this->form_row('checkbox', __('Brute force protection', $this->name), 'bruteforce', __('Enable brute force protection', $this->name), __('This function will protect your blog against <a href="http://en.wikipedia.org/wiki/Brute-force_search">brute force attacks</a>. It will lock your blog down for ip addresses that failed to login multiple times.', $this->name), false, $this->check_security_lock());
		$this->form_row('text_small', __('Max Login Attempts', $this->name), 'maxattempts', __('attempts', $this->name), __('Please define, how often is a user allowed to fail login, before he is locked out.', $this->name), false, $this->check_security_lock());
		$this->form_row('text_small', __('Lockout Time', $this->name), 'lockout', __('seconds', $this->name), __('How long should he be locked out? Default is 600 seconds => 10 minutes.', $this->name), false, $this->check_security_lock());
		$this->form_row('text_small', __('Log Time', $this->name), 'keeplog', __('seconds', $this->name), __('How long should do you want to keep the log? This should be longer than lockout time. If a user is allowed again to login, he has only one attempt and is locked out again. Default is 3600 seconds => 1 hour.', $this->name), false, $this->check_security_lock());
		$this->form_row('text', __('Lockout message', $this->name), 'lockoutmessage', '', __('You can define the error message, that locked out users will see.', $this->name), false, $this->check_security_lock());
		$this->form_row('checkbox', __('Tarpit', $this->name), 'tarpit', __('Enable tarpit protection', $this->name), __('This function is somehow a copy of the <a href="http://en.wikipedia.org/wiki/Tarpit_%28networking%29">tarpit function</a> for emails. With every failed login, it will increase the response time for the potential attacker. This will slow down the attack.', $this->name), false, $this->check_security_lock());
		$this->form_row('text_small', __('Max tarpit timeout', $this->name), 'maxtimeout', __('seconds', $this->name), __('To prevent server or gateway timeouts, you can specify a maximum timeout.', $this->name), false, $this->check_security_lock());

		$this->form_row('checkbox', __('Protect plugin folder', $this->name), 'protectplugin', __('Protect your plugin folder', $this->name), __('In some server configurations it is possible to list files in a folder. This may be a security risk, as an attacker may get direct access to your log files or backups. If checked, it will write an empty index file into your plugin folder. Private! will never overwrite your files or delete files from other plugins. This will work in all server configurations and in other servers than apache. If activated, Private! will test the existance on every change on the settings.', $this->name), false, $this->check_security_lock_admin(), '170');

		$this->form_row('checkbox', __('Protect theme folder', $this->name), 'protectthemes', __('Protect your theme folder', $this->name), __('If checked, it will write an empty index file into your theme folder.', $this->name), false, $this->check_security_lock_admin(), '170');

		$this->form_row('checkbox', __('Protect includes folder', $this->name), 'protectincludes', __('Protect your includes folder', $this->name), __('If checked, it will write an empty index file into your includes folder.', $this->name), false, $this->check_security_lock_admin(), '170');

		$this->form_row('checkbox', __('Restrict Admin', $this->name), 'restrictip', __('Restrict admin pages to your current ip', $this->name), sprintf(__('If you use a static ip and do not have any other users in your blog, you can restrict all admin pages to your <strong>current ip (%s). You can remove this setting ONLY by DELETING the ip.php in the private plugin folder. If you are unsure about anything in this text, don\'t use this feature.</strong>', $this->name), $this->get_real_ip()), false, $this->check_security_lock_admin(), '170');

		$this->form_row('submit', '', __('Save Changes', $this->name));
		$this->form_break(__('Advanced Settings', $this->name), 4);
		$this->form_row('select', __('WP Generator', $this->name), 'headerswp', __('Add or remove extra headers for WP Generator', $this->name),  __('The WP Generator Meta in the blog head is most of the time useless and a security risk. You don\'t need this, you should remove it from your blog.', $this->name), array('0'=>'','1'=>__('Add WP Generator', $this->name),'2'=>__('Remove Version', $this->name),'3'=>__('Remove WP Generator', $this->name)));

		$link = is_file(ABSPATH . 'readme.html') ? get_bloginfo('url') . '/readme.html' : XFW_TMP_URL . '/readme.html';

		if($this->check_security_lock_admin())
			$link = 'javascript:alert(\''.__("This function is restricted to admins!").'\');';

		$this->form_row('checkbox', __('Remove readme.html', $this->name), 'removereadme', __('Remove readme.html from the root folder', $this->name), sprintf(__('If you remove the version from you head, you should also delete the actual readme.html as an attacker can find out with this file, what version you are using. The file will be moved into the Private! tempfolder. <a href="%s">View this file here</a>.', $this->name), $link), false, $this->check_security_lock_admin(), '170');

		$this->form_row('select', __('WLW Manifest', $this->name), 'headerswlw', __('Add or remove extra headers for WLW', $this->name),  __('If you lock down your blog, you might run into problems using software like <a href="http://explore.live.com/windows-live-writer">WLW</a>. You can solve this, by sending special headers in the head of your blog. If you don\'t use, you should remove these headers as it may be a security risk.', $this->name), array('0'=>'','1'=>__('Add WLW manifest', $this->name),'2'=>__('Remove WLW manifest', $this->name)));
		$this->form_row('select', __('RSD Link', $this->name), 'headersrsd', __('Add or remove extra headers for RSD', $this->name),  __('If you lock down your blog, you might run into problems using software like <a href="http://www.blogdesk.org/de/index.htm">Blogdesk</a>. You can solve this, by sending special headers in the head of your blog. If you don\'t use, you should remove these headers as it may be a security risk.', $this->name), array('0'=>'','1'=>__('Add RSD link', $this->name),'2'=>__('Remove RSD link', $this->name)));
		$this->form_row('text', __('Redirection URL', $this->name), 'redirect', '', __('Add a full url to which the unauthorized users will be redirected to. If you leave it blank, the user will be redirected to the login page. You can also add the url to your login, if you have problems with the regular auth redirect.', $this->name));

		$this->form_row('checkbox', __('No redirection', $this->name), 'denynoredirect', __('Do not redirect logged in users', $this->name), __('If you use a time based authorization and don\'t want to add a page for unauthorized users, you can show them a simple error message.', $this->name), false, false, '170');
		$this->form_row('text', __('Deny message', $this->name), 'denymessage', '', __('Please provide an error message for logged in, but unauthorized users, if you do not want them to be redirected to the login.', $this->name), false, false, '170');

		$this->form_row('textarea', __('Login message', $this->name), 'message', false, __('This message will be shown to unauthorized visitors above the login. HTML tags are allowed.', $this->name) . ' ' . __("To apply WordPress's default style, use <code>&lt;p class=\"message\"&gt;</code> and <code>&lt;/p&gt;</code> to wrap your own message.", $this->name));
		$this->form_row('checkbox', __('Link on login page', $this->name), 'link', __('Add a link to the <a href="http://plugins.x-blogs.org/private/">Private! Homepage</a>', $this->name), __('If you like this script and want to tell the world, you can add a link to your login page. <a href="/wp-login.php?preview_link=true">You can preview this setting here.</a> <strong>Thank you for your support!</strong>', $this->name));
		$this->form_row('submit', '', __('Save Changes', $this->name));
		$this->form_break(__('About', $this->name), 5);
		$this->form_row('custom', 'About', '', 'This plugin is written by Oliver Sperke<br />Pluginurl: <a href="http://wordpress.org/extend/plugins/private-wordpress-access-control-manager/">http://wordpress.org/extend/plugins/private-wordpress-access-control-manager/</a><br /><br />If you like, drop me a line to <a href="mailto:oliver@x-blogs.org">oliver@x-blogs.org</a> or give me a good rating. <strong>Thank you!</strong>');
		$this->form_row('custom', 'Changelog', '', '

			<h3>1.7.0</h3>
			<li>Added: Option to display an error message, instead of redirecting users to the login or to another page</li>
			<li>Added: Option to display 404 error page instead of a Bad Request error</li>
			<li>Added: Log all potential malicious url requests (A viewer will be added in one of the next versions)</li>
			<li>Added: Block requests longer than 255 chars</li>
			<li>Added: Block requests containing your db prefix</li>
			<li>Added: Block file uploads with dangerous file endings</li>
			<li>Added: Block directory traversal attacks</li>
			<li>Added: Force security settings on all member blogs in a multisite environment</li>
			<li>Added: Ability to remove readme.html from blog root</li>
			<li>Added: Protect your Plugins and Includes Folder by creating index.php files in these folders</li>
			<li>Added: Capability setting for editing auth time</li>
			<li>Added: Restrict the Admin to your current ip</li>
			<li>Changed: Split detection of xss/sql Attacks</li>
			<li>Changed: Standard capability for editing users changed to edit users</li>
			<li>Fixed: Redirect to login does not work correctly in some situations</li>
			<li>Fixed: Plugin link is now shown, even if there are no restrictions</li>

			<h3>1.6.5</h3>
			<li>Fixed: Typo in login, signup condition, that might give access for anyone [Thanks to sulfsby]</li>

			<h3>1.6.4</h3>
			<li>Fixed: PHP Warning when not hiding tags</li>

			<h3>1.6.3</h3>
			<li>Added: Multisite registration to single blogs is not restricted to subdomain installations anymore</li>
			<li>Added: Tags can also be hidden from the Tag cloud widget</li>
			<li>Changed: Moved multisite options down on the settings page</li>
			<li>Fixed: Register page stays open, even if all pages are restricted</li>

			<h3>1.6.2</h3>
			<li>Added: Brazilian Portugese language file [Big big thanks to Eduardo]</li>
			<li>Fixed: Error in check_auth, which prevents logged in user to see hidden categories [Sorry to Sabeth]</li>

			<h3>1.6.1</h3>
			<li>Fixed: Translation issue in the main menu [Big thanks to Eduardo]</li>

			<h3>1.6.0</h3>
			<ul>
				<li>Added: Allow users to register directly to a blog in the multisite network if webmaster allows</li>
				<li>Added: Ability to add directly to a blog for users if webmaster allows</li>
				<li>Added: PHP Version check for really really old installations</li>
				<li>Fixed: auth date was taken from current user and not from shown user</li>
				<li>Fixed: admin warnings are restricted to admins now</li>
				<li>Fixed: update notices are restricted to admins now</li>
			</ul>
			<h3>1.5.0</h3>
			<ul>
				<li>Added: Remove restricted posts from front page, search, blog home etc. for non authorized users based on restricted categories, tags or posts</li>
				<li>Added: Remove categories from sidebar</li>
			</ul>
			<h3>1.4.0</h3>
			<ul>
				<li>Added: Option to remove feeds from blog headers</li>
				<li>Added: Generation of feed keys, so that RSS Reader can access restricted feeds</li>
				<li>Added: Navigation below Admin</li>
				<li>Added: Ability to remove feed keys from database</li>
				<li>Added: Force users to use only strong passwords</li>
				<li>Changed: Merged the access rights pages</li>
				<li>Fixed: Some improvements on the assistant</li>
			</ul>

			<h3>1.3.1</h3>
			<ul>
				<li>Fixed: A small bug in des.class.php as the password is not decrypted correctly because of unneeded whitespaces</li>
			</ul>

			<h3>1.3.0</h3>
			<ul>
				<li>Added: Easy to use setup assistant</li>
				<li>Added: Option to delete brute force logs</li>
				<li>Fixed: Added english and german translation for user profile field</li>
			</ul>
			<h3>1.2.2</h3>
			<ul>
				<li>Fixed: Wrong parameter count on form_row</li>
			</ul>
			<h3>1.2.1</h3>
			<ul>
				<li>Added: New options are marked as new</li>
				<li>Fixed: Generate key on plugin activation</li>
			</ul>
			<h3>1.2.0</h3>
			<ul>
				<li>Added: Secure login script</li>
			</ul>
			<h3>1.1.1</h3>
			<ul>
				<li>Fixed: wrong datatype in array</li>
			</ul>
			<h3>1.1.0</h3>
			<ul>
				<li>Added: New interface design</li>
				<li>Added: Allow access to attachments</li>
				<li>Added: Disable all restrictions with a single click</li>
				<li>Added: Customize your login error messages</li>
				<li>Added: Prevent against attackers with tarpit technology</li>
				<li>Added: Brute force prevention</li>
				<li>Added: Define maximum login attempts and lockout time for brute force attempts</li>
				<li>Added: Remove or anonymize your Wordpress Generator Meta Tag</li>
				<li>Added: Notification of recent updates</li>
				<li>Changed: Seperate settings for RSD and WLW headers</li>
				<li>Fixed: Some language strings</li>
				<li>Fixed: Some unitialized variables</li>
				<li>Fixed: Allowing single posts also allowed access to pages</li>
				<li>Fixed: logged in users may had lower rights than logged out if they were not approved</li>
				<li>Fixed: fixed some very complicated rules with tags and categories in single posts</li>
				<li>Fixed: A lot of testing with all kinds of rules</li>
			</ul>
			<h3>1.0.4</h3>
			<ul>
				<li>Another fix in multisites</li>
				<li>Deleted some warnings about uninitialized vars</li>
			</ul>
			<h3>1.0.3</h3>
			<ul>
				<li>Fixed a bug in non multisite environment</li>
			</ul>
			<h3>1.0.2</h3>
			<ul>
				<li>Fixed a bug with deleting user settings in multisite environment</li>
			</ul>
			<h3>1.0.1</h3>
			<ul>
				<li>A lot of small bugfixes - mainly wrong paths and forgotten translations</li>
			</ul>
			<h3>1.0.0</h3>
			<ul>
				<li>Initial release - it may let explode your hamster!</li>
			</ul>');
		$this->form_break(__('Uninstall the plugin', $this->name), false,$this->form_footer_list(array(
			__('Basic Settings', $this->name) => 8,
			__('Access Rights', $this->name) => 20,
			__('Security Settings', $this->name) => 25,
			__('Advanced Settings', $this->name) => 9,
			__('About', $this->name) => false,
		)));
		$this->form_row('select', __('Remove settings', $this->name), 'remove', false, __('This will delete all Private! data from your database. If you are not sure about reinstalling this plugin, you should keep user data.', $this->name), array(''=>'','1'=>__('Remove only settings from this page', $this->name),'2'=>__('Remove all user settings from this blog', $this->name),'3'=>__('Remove all brute force logs', $this->name),'4'=>__('Remove all feed keys', $this->name),'5'=>__('Remove all settings and user settings from this blog', $this->name)));
		$this->form_row('version', '', 'version');
		$this->form_row('altsubmit', '', __('Delete Settings', $this->name));
		echo '</div>';
		$this->form_page_bottom();
		echo '<style type="text/css">#private_modal {position:fixed;top: 0;left: 0;height:100%;width:100%;background:#000;z-index: 1000;display:none;}#private_assistant {position:absolute;top: 30px;left: 50%;height:100%;width:780px;margin: 0 0 0 -390px;z-index: 1001;display:none;}</style>';
		echo '<div id="private_modal"></div><div id="private_assistant">';
			$this->form_page_top(__('Please select the profile, that comes close to your needs!', $this->name), __('_private_assistant', $this->name));
			$this->form_row('custom', __('Easy setup assistant', $this->name), '', '
				<p>'.__('Private! will help you, to secure your blog and give access only to people you want to have. To make the setup as easy as possible, you should select a profile from the list below. The plugin will generate a configuration for you. You can of course change every option yourself, to get the best out of Private!', $this->name).'</p>

				<p><input type="radio" name="assistant" id="assistant_1" /> <label for="assistant_1"><strong>'.__('I want to run private blog for my family, my friends or my business', $this->name).'</strong></label></p>
				<p><em>'.__('This might be the most common case. Your blog will be closed to the public without any exceptions. The time based authentification will be turned off and the login will get secured.', $this->name).'</em></p>

				<p><input type="radio" name="assistant" id="assistant_2" /> <label for="assistant_2"><strong>'.__('I want to run a public blog with paid content in certain categories', $this->name).'</strong></label></p>
				<p><em>'.__('If you are for example a designer, who sells tutorials over the web, this is for you. The blog will stay open for anyone to access your free content and there will be a time based authentification for your members. If they order access for a certain time, you can edit their profile to give them what they paid for. The login will get secured.', $this->name).'</em></p>

				<p><input type="radio" name="assistant" id="assistant_3" /> <label for="assistant_3"><strong>'.__('I want to run a members only page', $this->name).'</strong></label></p>
				<p><em>'.__('You have paid content only and want to sell it? Chose this option. In this setup, the whole content will be closed except your Front page. You should add a static page, where you can tell your customers, how they can get access to your treasures.', $this->name).'</em></p>

				<p><input type="radio" name="assistant" id="assistant_4" /> <label for="assistant_4"><strong>'.__('I don\'t need this access voodoo, but I want secure my blog', $this->name).'</strong></label></p>
				<p><em>'.__('Your wish ... The whole access things will be turned off, but the security features will be set up for you.',$this->name).'</em></p>

				<p><input type="radio" name="assistant" id="assistant_5" checked="checked" /> <label for="assistant_5"><strong>'.__('Don\'t change anything, I want to find out myself', $this->name).'</strong></label></p>
				<p><em>'.__('Of couse you want. Private! is well documented, so please read carefully the descriptions. As admin, you cannot lock out yourself (except on multiple failed logins), so feel free to test everything.', $this->name).'</em></p>
			');
			$this->form_row('submit', '', __('Setup this profile and close assistant', $this->name));
			$this->form_page_bottom();
		echo '</div>';

		echo '<script type="text/javascript">jQuery(".subsubsub>li>a").click(function(e){jQuery(".widefat").not(".notab,#tab_private_assistant").hide();jQuery(jQuery(this).attr("href")).hide().fadeIn("normal");jQuery(".subsubsub>li>a").removeClass("current");jQuery(\'.subsubsub>li>a[href="\'+jQuery(this).attr("href")+\'"]\').blur().addClass("current");e.preventDefault();return false;});jQuery(".widefat").not("#tab1").not(".notab,#tab_private_assistant").hide();jQuery(".fade").delay(5000).fadeOut();if(!jQuery.browser.msie||jQuery.browser.version>6){jQuery("#private_headline").append(" (<a id=\"showassistant\" href=\"#\">'.__('Setup Assistant',$this->name).'</a>)");}jQuery("#showassistant").click(function(e){jQuery("#private_modal").show().hide().fadeTo("slow",.5);jQuery("#private_assistant").fadeIn();jQuery("#private_modal").click(function(e){jQuery("#private_assistant,#private_modal").fadeOut();e.preventDefault;return false;});jQuery("#form__private_assistant").submit(function(e){if(jQuery("#assistant_1").is(":checked")){jQuery("#private_enable,#private_noapprove,#private_norealerror,#private_strongpass,#private_requestfilter,#private_filterlong,#private_filtersql,#private_filterxss,#private_filterprefix,#private_filterdirtraversal,#private_filtertruncation,#private_filterfiles,#private_filterlog,#private_throw404,#private_protectplugin,#private_protectthemes,#private_protectincludes,#private_feedkey,#private_loginjs,#private_bruteforce,#private_tarpit").attr("checked","checked");jQuery("#private_norestrictions,#private_front,#private_home,#private_post,#private_attachment,#private_page,#private_category,#private_tag,#private_archive,#private_search,#private_feed,#private_catsingle,#private_tagsingle,#private_showfeed").attr("checked","");jQuery("#private_headerswp").val("3");jQuery("#private_headerswlw,#private_headersrsd").val("2");jQuery("#private_capabilities").val("");}else if(jQuery("#assistant_2").is(":checked")){jQuery("#private_enable,#private_strongpass,#private_requestfilter,#private_filterlong,#private_filtersql,#private_filterxss,#private_filterprefix,#private_filterdirtraversal,#private_filtertruncation,#private_filterfiles,#private_filterlog,#private_throw404,#private_protectplugin,#private_protectthemes,#private_protectincludes,#private_feedkey,#private_norealerror,#private_loginjs,#private_bruteforce,#private_tarpit,#private_front,#private_home,#private_post,#private_attachment,#private_page,#private_category,#private_tag,#private_archive,#private_search,#private_feed,#private_showfeed,#private_catsingle,#private_tagsingle").attr("checked","checked");jQuery("#private_norestrictions,#private_noapprove").attr("checked","");jQuery("#private_headerswp").val("2");jQuery("#private_headerswlw,#private_headersrsd").val("2");jQuery("#private_capabilities").val("");}else if(jQuery("#assistant_3").is(":checked")){jQuery("#private_enable,#private_strongpass,#private_requestfilter,#private_filterlong,#private_filtersql,#private_filterxss,#private_filterprefix,#private_filterdirtraversal,#private_filtertruncation,#private_filterfiles,#private_filterlog,#private_throw404,#private_protectplugin,#private_protectthemes,#private_protectincludes,#private_feedkey,#private_norealerror,#private_loginjs,#private_bruteforce,#private_tarpit,#private_front").attr("checked","checked");jQuery("#private_norestrictions,#private_noapprove,#private_home,#private_post,#private_attachment,#private_page,#private_category,#private_tag,#private_archive,#private_search,#private_feed,#private_showfeed,#private_catsingle,#private_tagsingle").attr("checked","");jQuery("#private_headerswp").val("2");jQuery("#private_headerswlw,#private_headersrsd").val("2");jQuery("#private_capabilities").val("");}else if(jQuery("#assistant_4").is(":checked")){jQuery("#private_enable,#private_norestrictions,#private_strongpass,#private_requestfilter,#private_filterlong,#private_filtersql,#private_filterxss,#private_filterprefix,#private_filterdirtraversal,#private_filtertruncation,#private_filterfiles,#private_filterlog,#private_throw404,#private_protectplugin,#private_protectthemes,#private_protectincludes,#private_norealerror,#private_loginjs,#private_bruteforce,#private_tarpit").attr("checked","checked");jQuery("#private_noapprove,#private_home,#private_post,#private_attachment,#private_page,#private_category,#private_tag,#private_archive,#private_search,#private_feed,#private_feedkey,#private_showfeed,#private_catsingle,#private_tagsingle,#private_front").attr("checked","");jQuery("#private_headerswp").val("2");jQuery("#private_headerswlw,#private_headersrsd").val("2");jQuery("#private_capabilities").val("");}jQuery("#private_assistant,#private_modal").fadeOut();e.preventDefault;return false;});e.preventDefault;return false;});</script>';
	}

	function show_categories()
	{
		$post_categories = get_categories();

		if(empty($post_categories))
			return '<strong>' . __('There are no categories.', $this->name) . '</strong>';

		$return = '<input type="hidden" name="private_cathidden" value="false" /><table style="width:100%;background:rgba(0,0,0,.02);border:1px solid rgba(0,0,0,.1)"><tr><th style="background:rgba(0,0,0,.1);padding:.25em .5em;text-align:center">' . __('Category', $this->name) . '</th><th colspan="4" style="background:rgba(0,0,0,.1);padding:.25em .5em;text-align:center;width:80%">' . __('Special access rights', $this->name) . '</th></tr>';

		$categories = isset($this->options['categories']) && is_serialized($this->options['categories']) ? unserialize($this->options['categories']) : array();
		$hidden = isset($this->options['cathidden']) && is_serialized($this->options['cathidden']) ? unserialize($this->options['cathidden']) : array();
		$odd = true;

		foreach($post_categories AS $cat)
		{
			$return .= '<tr'. ($odd==true?' style="background:rgba(0,0,0,.05)"':'').'><td style="padding:.25em .5em">';
			$return .= '<strong>' . $cat->name . '</strong></td><td style="width:11%"><input type="checkbox" id="private_cathidden_' . $cat->cat_ID . '" name="private_cathidden[]" value="' . $cat->cat_ID . '"'.(in_array($cat->cat_ID,$hidden)?' checked="checked"' : '').' /> <label for="private_cathidden_' . $cat->cat_ID . '">'.__('hide', $this->name).'</label></td><td style="padding:.25em .5em;text-align:center;width:12%"><input id="allow_cat_' . $cat->cat_ID . '" type="radio" name="private_categories[' . $cat->cat_ID . ']" value="' . $cat->cat_ID . '" ' . (in_array($cat->cat_ID, $categories)?'checked="checked" ':'') . '/><label for="allow_cat_' . $cat->cat_ID . '"> ' . __('Allow', $this->name) . '</label></td><td style="padding:.25em .5em;text-align:center;width:12%"><input id="deny_cat_' . $cat->cat_ID . '" type="radio" name="private_categories[' . $cat->cat_ID . ']" value="!' . $cat->cat_ID . '" ' . (in_array('!'.$cat->cat_ID, $categories)?'checked="checked" ':'') . '/><label for="deny_cat_' . $cat->cat_ID . '"> ' . __('Deny', $this->name) . '</label></td><td style="padding:.25em .5em;text-align:center;width:20%"><input id="cat_' . $cat->cat_ID . '" type="radio" name="private_categories[' . $cat->cat_ID . ']" value="" ' . (!in_array($cat->cat_ID, $categories)&&!in_array('!'.$cat->cat_ID, $categories)?'checked="checked" ':'') . '/> <label for="cat_' . $cat->cat_ID . '"> ' . __('Use standard', $this->name) . '</label></td>';
			$return .= '</td></tr>';
			$odd=!$odd;
		}
		$return .= '</table>';
		return $return;
	}

	function show_tags()
	{
		$post_tags = get_tags();

		if(empty($post_tags))
			return '<strong>' . __('There are no tags.', $this->name) . '</strong>';

		$return = '<table style="width:100%;background:rgba(0,0,0,.02);border:1px solid rgba(0,0,0,.1)"><tr><th style="background:rgba(0,0,0,.1);padding:.25em .5em;text-align:center">' . __('Tags', $this->name) . '</th><th colspan="4" style="background:rgba(0,0,0,.1);padding:.25em .5em;text-align:center;width:80%">' . __('Special access rights', $this->name) . '</th></tr>';

		$tags = isset($this->options['tags']) && is_serialized($this->options['tags']) ? unserialize($this->options['tags']) : array();
		$hidden = isset($this->options['taghidden']) && is_serialized($this->options['taghidden']) ? unserialize($this->options['taghidden']) : array();
		$odd = true;

		foreach($post_tags AS $tag)
		{
			$return .= '<tr'. ($odd==true?' style="background:rgba(0,0,0,.05)"':'').'><td style="padding:.25em .5em">';
			$return .= '<strong>' . $tag->name . '</strong></td><td style="width:11%"><input type="checkbox" id="private_taghidden_' . $tag->term_id . '" name="private_taghidden[]" value="' . $tag->term_id . '"'.(in_array($tag->term_id,$hidden)?' checked="checked"' : '').' /> <label for="private_taghidden_' . $tag->term_id . '">'.__('hide', $this->name).'</label></td><td style="padding:.25em .5em;text-align:center;width:12%"><input id="allow_tag_' . $tag->term_id . '" type="radio" name="private_tags[' . $tag->term_id . ']" value="' . $tag->term_id . '" ' . (in_array($tag->term_id, $tags)?'checked="checked" ':'') . '/><label for="allow_tag_' . $tag->term_id . '"> ' . __('Allow', $this->name) . '</label></td><td style="padding:.25em .5em;text-align:center;width:12%"><input id="deny_tag_' . $tag->term_id . '" type="radio" name="private_tags[' . $tag->term_id . ']" value="!' . $tag->term_id . '" ' . (in_array('!'.$tag->term_id, $tags)?'checked="checked" ':'') . '/><label for="deny_tag_' . $tag->term_id . '"> ' . __('Deny', $this->name) . '</label></td><td style="padding:.25em .5em;text-align:center;width:20%"><input id="tag_' . $tag->term_id . '" type="radio" name="private_tags[' . $tag->term_id . ']" value="" ' . (!in_array($tag->term_id, $tags)&&!in_array('!'.$tag->term_id, $tags)?'checked="checked" ':'') . '/> <label for="tag_' . $tag->term_id . '"> ' . __('Use standard', $this->name) . '</label></td>';
			$return .= '</td></tr>';
			$odd=!$odd;
		}
		$return .= '</table>';
		return $return;
	}

	function edit_user()
	{
		if($this->options['editauth'] == '')
			$this->options['editauth'] = 'edit_users';
		
		if(!current_user_can($this->options['editauth']))
			return;

		$allowed = get_user_meta((int)$_GET['user_id'], 'allow_private_'.$this->blog->id, true);

		if($allowed != '')
			$allowed_until = date('Y-m-d H:i:s', $allowed);

		if(!isset($allowed_until) || $allowed < $this->local_unix_time())
			$allowed_until = date('Y-m-d H:i:s', $this->local_unix_time());

		echo '<table class="form-table"><tr><th scope="row"><label for="allow_private">' . __('Private! options', $this->name) . '</label></th><td><input type="text" name="allow_private" id="allow_private" value="'.$allowed_until.'" /> <span class="description">' . __('If you want to allow this user to access your Private! areas, edit this field to a date in the future.', $this->name) . '</span></td></tr></table>';
	}

	function edit_user_submit()
	{
		if($this->options['editauth'] == '')
			$this->options['editauth'] = 'edit_users';
			
		if(!current_user_can($this->options['editauth']))
			return;

		$allowed = strtotime($_POST['allow_private']);
		if(isset($_POST['allow_private']) && $allowed > $this->local_unix_time())
			update_user_meta($_POST['user_id'], 'allow_private_'.$this->blog->id, $allowed);
		else
			delete_user_meta($_POST['user_id'], 'allow_private_'.$this->blog->id);
	}

	function brute_force()
	{
		$this->brute_force_log();
		$this->brute_log[$this->ip][] = $this->brute_log[$this->ip]['last'] = $this->local_unix_time();
		update_option('brute_log_'.$this->blog->id, serialize($this->brute_log));
	}

	function check_brute_force()
	{
		$this->brute_force_log();
		foreach($this->brute_log AS $i => $t)
		{
			if($this->brute_log[$i]['last'] < $this->local_unix_time()-$this->options['keeplog'] || ($i==$this->ip && is_user_logged_in()))
				unset($this->brute_log[$i]);
		}
		update_option('brute_log_'.$this->blog->id, serialize($this->brute_log));

		if($this->options['tarpit']==true && isset($this->brute_log[$this->ip]))
		{
			$tarpit = count($this->brute_log[$this->ip])-1;
			$wait = (pow($tarpit,2) > $this->options['maxtimeout']) ? $this->options['maxtimeout'] : pow($tarpit,2);
			sleep($wait);
		}
 		if(isset($this->brute_log[$this->ip]) && $this->brute_log[$this->ip]['last'] > ($this->local_unix_time()-$this->options['lockout']) && count($this->brute_log[$this->ip]) >= $this->options['maxattempts'])
			wp_die(str_replace(array('%until%'), array(date(get_option('time_format'), ($this->brute_log[$this->ip]['last']+$this->options['lockout'])), $this->brute_log[$this->ip]['last']), $this->options['lockoutmessage']));
	}

	function brute_force_log()
	{
		if(is_array($this->brute_log))
			return;

		$this->ip = $this->get_real_ip();

		$this->brute_log = get_option('brute_log_'.$this->blog->id);
		$this->brute_log = is_serialized($this->brute_log) ? unserialize($this->brute_log) : array();
	}

	function get_real_ip()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']) && $this->valid_ip($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];

		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $this->ip)
				if ($this->valid_ip(trim($this->ip)))
					return $this->ip;

		else if (isset($_SERVER["HTTP_X_FORWARDED"]) &&  $this->valid_ip($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];

		else if (isset($_SERVER["HTTP_FORWARDED_FOR"]) &&  $this->valid_ip($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];

		else if (isset($_SERVER["HTTP_FORWARDED"]) &&  $this->valid_ip($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];

		else if (isset($_SERVER["HTTP_X_FORWARDED"]) &&  $this->valid_ip($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];

		return $_SERVER["REMOTE_ADDR"];
	}

	function valid_ip($ip)
	{
		if (!empty($ip) && ip2long($ip)!=-1)
		{
			$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
			);
		
			foreach ($reserved_ips as $r)
			{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);

				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
			}
			return true;
		}
		else
			return false;
	}

	function anonym_wp_generator()
	{
		echo '<meta name="generator" content="WordPress" />';
	}

	function local_unix_time()
	{
		return gmdate('U',(time()+(get_option('gmt_offset')*3600)));
	}

	function pass_strengh($pass)
	{
		$points = 0;

		if(preg_match('/([0-9]+)/', $pass))
			$points += 10;

		if(preg_match('/([a-z]+)/', $pass))
			$points += 26;

		if(preg_match('/([A-Z]+)/', $pass))
			$points += 26;

		if(preg_match('/([^a-zA-Z0-9]+)/', $pass))
			$points += 31;

		if(strtolower($_POST['nickname']) == strtolower($pass))
			$points = 0;

		$points = log(pow($points, strlen($pass)))/0.693;

		return ($points>56);
	}

	function request_filter()
	{
		if(!current_user_can('administrator') || strpos($_SERVER['REQUEST_URI'], 'admin_test') !== false)
		{
			$request = urldecode($_SERVER['REQUEST_URI']);
			$error = false;
			
			if(strlen($request) > 255 && $this->options['filterlong'])
			{
				$error = 'Request is too long';
			}
			else if(preg_match('/(exec|eval|base64|system|include|require)([\s]|\(|>)/i', $request) && $this->options['filterxss'])
			{
				$error = "Possible xss/inclusion attack";
			}
			else if(preg_match('/(drop|update|select|concat|union|select)([\s]|\(|>)/i', $request) && $this->options['filtersql'])
			{
				$error = "Possible sql injection attack";
			}
			else if(preg_match('/(etc\/|\/passwords|proc\/|www\/|htdocs\/|\.\.)/i', $request) && $this->options['filterdirtraversal'])
			{
				$error = "Possible directory traversal attack";
			}
			else if(preg_match('/(\s{49,}|\x00)/i', $request) && $this->options['filtertruncation'])
			{
				$error = "Possible field truncation attack";
			}
			else if(isset($_FILES) && $this->options['filterfiles'])
			{
				foreach($_FILES AS $file)
				{
					if(preg_match('/\.(dll|rb|py|exe|php|pl|perl|cgi|phtm|phtml)$/i', $file['name']))
					{
						$error = "Possible file upload attack";
					}
				}
			}
			else if(strpos($request, $wpdb->prefix) !== false && $this->options['filterprefix'])
			{
				$error = "Possible sql injection attack (DB Prefix)";
			}

			if($error!=false || strpos($_SERVER['REQUEST_URI'], 'admin_test') !== false)
			{
				if($this->options['filterlog'] == true)
				{
					$attack = array();
					$ip = $this->get_real_ip();
					$attack[] = gmdate('U');
					$attack[] = $error;
					$attack[] = $ip;
					$attack[] = gethostbyaddr($ip);
					$attack[] = serialize($_GET);
					file_put_contents(XFW_TMP_PATH . '/filter.log', serialize($attack)."\n", FILE_APPEND);
				}
				
				if($this->options['throw404'] == true)
				{
					header("HTTP/1.0 404 Not Found - Archive Empty");
					require TEMPLATEPATH.'/404.php';
				}
				else
				{
				header("HTTP/1.1 400 Bad Request");
					header("Status: 400 Bad Request");
					echo "<h1>400 Bad Request</h1>";
				}
				exit;
			}
		}
	}

	function edit_password()
	{
		echo '<div id="message" class="updated fade"><strong><p><a href="#"></a> '. __(' Please edit your password', $this->name) . '</p></strong></div>';
	}

	function security_check()
	{
		// Nothing
	}

	function subscribe_to_new_blogs()
	{
		global $wpdb, $current_user;

		if(isset($_POST['subscribe_to_blog']) && $_POST['subscribe_to_blog'] != '')
		{
			if(SUBDOMAIN_INSTALL == true)
			{
				$blogid = (int)$wpdb->get_var("SELECT DISTINCT blog_id FROM $wpdb->blogs WHERE domain = '".$wpdb->escape($_POST['subscribe_to_blog'].'.'.DOMAIN_CURRENT_SITE)."'");
			}
			else
			{
				$blogid = (int)$wpdb->get_var("SELECT DISTINCT blog_id FROM $wpdb->blogs WHERE domain = '".$wpdb->escape(DOMAIN_CURRENT_SITE)."' AND path = '/".$wpdb->escape($_POST['subscribe_to_blog'])."/'");
			}

			if($blogid != false && $blogid != BLOG_ID_CURRENT_SITE)
			{
				$blogoptions = $wpdb->get_var("SELECT option_value FROM ".$wpdb->prefix.$blogid."_options WHERE option_name = '".$this->name."'");

				while(isset($blogoptions) && is_serialized($blogoptions)) // Todo - fix this sh...
					$blogoptions = unserialize($blogoptions);

				if(isset($blogoptions['enable']) && $blogoptions['enable'] == true && isset($blogoptions['directsubscription']) && $blogoptions['directsubscription'] == true)
				{
					add_user_to_blog($blogid,$current_user->ID,get_site_option('default_user_role','subscriber'));
					echo "<script>self.location.href=self.location.href;</script>";
				}
				else
					echo '<p class="error">'. __('This blog does not allow you to subscribe directly! Please contact the blog admin.', $this->name).'</p>';
			}
			else
				echo '<p class="error">'. __('There is no blog with this name!', $this->name).'</p>';
		}
		echo '<h3>'.__('Subscribe to a blog', $this->name).'</h3><table class="form-table"><tr><th scope="row">'.__('Subscribe to a blog', $this->name).'</th><td><fieldset><legend class="screen-reader-text"><span>';
		if(SUBDOMAIN_INSTALL == true)
			echo __('Type in the subdomain of the blog', $this->name).'</span></legend><input name="subscribe_to_blog" id="subscribe_to_blog" type="text" value=""  /><label for="subscribe_to_blog">.'.DOMAIN_CURRENT_SITE.'</label>';
		else
			echo __('Type in the path of the blog', $this->name).'</span></legend><label for="subscribe_to_blog">'.DOMAIN_CURRENT_SITE.'</label> / <input name="subscribe_to_blog" id="subscribe_to_blog" type="text" value=""  />';
		echo '</fieldset></td></tr></table>';
	}

	function subscribe_to_main_blog()
	{
		global $current_user;

		add_user_to_blog(BLOG_ID_CURRENT_SITE,$current_user->ID,get_site_option('default_user_role','subscriber'));
	}

	function subscribe_to_blog_meta($meta)
	{
		global $wpdb;

		if(isset($_POST['subscribe_to_blog']) && $_POST['subscribe_to_blog'] != BLOG_ID_CURRENT_SITE)
		{
			$blogid = intval($_POST['subscribe_to_blog']);
			$blogoptions = $wpdb->get_var("SELECT option_value FROM ".$wpdb->prefix.$blogid."_options WHERE option_name = '".$this->name."'");

			while(isset($blogoptions) && is_serialized($blogoptions)) // Todo - fix this sh...
				$blogoptions = unserialize($blogoptions);

			if(isset($blogoptions['enable']) && $blogoptions['enable'] == true && isset($blogoptions['directsubscription']) && $blogoptions['directsubscription'] == true)
				return array( 'add_to_blog' => intval($_POST['subscribe_to_blog']), 'new_role' => 'subscriber' );
		}
	}

	function subscribe_to_blog()
	{
		global $wpdb;

		$blogid = isset($_POST['subscribe_to_blog']) ? intval($_POST['subscribe_to_blog']) : false;
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;

		if($ref!=false && $blogid==false && SUBDOMAIN_INSTALL == true)
		{
			$ref = parse_url($ref);
			$blogid = (int)$wpdb->get_var("SELECT DISTINCT blog_id FROM $wpdb->blogs WHERE domain = '".$wpdb->escape($ref['host'])."'");
		}
		else if($ref!=false && $blogid==false && SUBDOMAIN_INSTALL == false)
		{
			$ref = parse_url($ref);
			$path = explode('/', $ref['path']);
			$blogid = (int)$wpdb->get_var("SELECT DISTINCT blog_id FROM $wpdb->blogs WHERE domain = '".$wpdb->escape($ref['host'])."' AND path = '/".$wpdb->escape($path['1'])."/'");
		}

		if($blogid == false || $blogid == BLOG_ID_CURRENT_SITE)
			return;

		$blogoptions = $wpdb->get_var("SELECT option_value FROM ".$wpdb->prefix.$blogid."_options WHERE option_name = '".$this->name."'");

		while(isset($blogoptions) && is_serialized($blogoptions)) // Todo - fix this sh...
			$blogoptions = unserialize($blogoptions);

		if(isset($blogoptions['enable']) && $blogoptions['enable'] == true && isset($blogoptions['directsubscription']) && $blogoptions['directsubscription'] == true)
			echo '<input type="hidden" name="subscribe_to_blog" value="'.$blogid.'" />';
	}

	function check_auth()
	{
		if(is_user_logged_in())
		{
			$current_user=wp_get_current_user();
			$allowed = get_user_meta($current_user->ID, 'allow_private_'.$this->blog->id);

			if($this->options['noapprove'] == true || ($this->options['noapprove'] != true && isset($allowed['0']) && $allowed['0'] >= $this->local_unix_time()))
			{
				if($this->options['capabilities'] != '' && $this->check_capabilities())
					return true;
				else if($this->user_can_at_least('subscriber'))
					return true;
				if($this->options['relation'] == true)
					return true;
			}
		}
		return false;
	}

	function filter_tax($query)
	{
		if($this->check_auth())
			return $query;

		$cats = isset($this->options['cathidden']) && is_serialized($this->options['cathidden']) ? unserialize($this->options['cathidden']) : array();
		$tags = isset($this->options['taghidden']) && is_serialized($this->options['taghidden']) ? unserialize($this->options['taghidden']) : array();

		$query['exclude'] = implode(',',array_merge($cats,$tags));

		return $query;
	}

	function filter_posts($query)
	{
		if(is_admin() || $this->check_auth())
			return $query;

		if(is_user_logged_in())
		{
			if($this->options['noapprove'] == true || ($this->options['noapprove'] != true && isset($allowed['0']) && $allowed['0'] >= $this->local_unix_time()))
			{
				if($this->options['capabilities'] != '' && $this->check_capabilities())
					return $query;
				else if($this->user_can_at_least('subscriber'))
					return $query;
				if($this->options['relation'] == true)
					return $query;
			}
		}
		if($this->options['hidecat'])
		{
			$basic_cat = isset($this->options['category']) && $this->options['category']==true ? true : false;
			$post_categories = get_categories();
			$special_cat = isset($this->options['categories']) && is_serialized($this->options['categories']) ? unserialize($this->options['categories']) : array();
			$forbidden_cat = array();

			foreach($post_categories AS $k => $v)
			{
				if(in_array('!'.$v->cat_ID, $special_cat))
					$forbidden_cat[] = $v->cat_ID;
				else if(!in_array($v->cat_ID, $special_cat) && $basic_cat!=true)
					$forbidden_cat[] = $v->cat_ID;
			}
			$query->query_vars['category__not_in'] = $forbidden_cat;
		}

		if($this->options['hidetag'])
		{
			$post_tags = get_tags();
			$basic_tag = isset($this->options['tag']) && $this->options['tag']==true ? true : false;
			$special_tag = isset($this->options['tags']) && is_serialized($this->options['tags']) ? unserialize($this->options['tags']) : array();
			$forbidden_tag = array();

			foreach($post_tags AS $k => $v)
			{
				if(in_array('!'.$v->term_id, $special_tag))
					$forbidden_tag[] = $v->term_id;
				else if(!in_array($v->term_id, $special_tag) && $basic_tag!=true)
					$forbidden_tag[] = $v->term_id;
			}
			$query->query_vars['tag__not_in'] = $forbidden_tag;
		}
		if($this->options['hidepost'])
		{
			$special_post = isset($this->options['posts']) ? explode(',', $this->options['posts']) : array();
			$forbidden_post = array();

			foreach($special_post AS $k => $v)
			{
				if(substr($v, 0, 1))
					$forbidden_post[] = substr($v, 1);
			}
			$query->query_vars['post__not_in'] = $forbidden_post;
		}
		return $query;
	}
	
	function check_security_lock()
	{
		return !(!is_multisite() || is_multisite() && current_user_can('update_core') || $this->options['locksecurity'] != true);
	}

	function check_security_lock_admin()
	{
		return !(!is_multisite() || is_multisite() && current_user_can('update_core'));
	}
	
	function write_security_lock()
	{
		if(is_multisite() && current_user_can('update_core'))
		{
			if(is_file(XFW_TMP_PATH . '/locksecurity.php'))
				unlink(XFW_TMP_PATH . '/locksecurity.php');

			if(isset($_POST['private_locksecurity']) && $_POST['private_locksecurity'] == "1")
			{
				$file = '<?PHP' . "\n\n/* Do not edit this file */\n\n";
				$file .= '$this->options[\'requestfilter\'] = "'.$_POST['private_requestfilter'].'";'."\n";
				$file .= '$this->options[\'filterlong\'] = "'.$_POST['private_filterlong'].'";'."\n";
				$file .= '$this->options[\'filterxss\'] = "'.$_POST['private_filterxss'].'";'."\n";
				$file .= '$this->options[\'filtersql\'] = "'.$_POST['private_filtersql'].'";'."\n";
				$file .= '$this->options[\'filterprefix\'] = "'.$_POST['private_filterprefix'].'";'."\n";
				$file .= '$this->options[\'filtertruncation\'] = "'.$_POST['private_filtertruncation'].'";'."\n";
				$file .= '$this->options[\'filterfiles\'] = "'.$_POST['private_filterfiles'].'";'."\n";
				$file .= '$this->options[\'throw404\'] = "'.$_POST['private_throw404'].'";'."\n";
				$file .= '$this->options[\'filterlog\'] = "'.$_POST['private_filterlog'].'";'."\n";
				$file .= '$this->options[\'strongpass\'] = "'.$_POST['private_strongpass'].'";'."\n";
				$file .= '$this->options[\'norealerror\'] = "'.$_POST['private_norealerror'].'";'."\n";
				$file .= '$this->options[\'customerror\'] = "'.$_POST['private_customerror'].'";'."\n";
				$file .= '$this->options[\'loginjs\'] = "'.$_POST['private_loginjs'].'";'."\n";
				$file .= '$this->options[\'bruteforce\'] = "'.$_POST['private_bruteforce'].'";'."\n";
				$file .= '$this->options[\'maxattempts\'] = "'.$_POST['private_maxattempts'].'";'."\n";
				$file .= '$this->options[\'lockout\'] = "'.$_POST['private_lockout'].'";'."\n";
				$file .= '$this->options[\'keeplog\'] = "'.$_POST['private_keeplog'].'";'."\n";
				$file .= '$this->options[\'lockoutmessage\'] = "'.$_POST['private_lockoutmessage'].'";'."\n";
				$file .= '$this->options[\'tarpit\'] = "'.$_POST['private_tarpit'].'";'."\n";
				$file .= '$this->options[\'maxtimeout\'] = "'.$_POST['private_maxtimeout'].'";'."\n";

				file_put_contents(XFW_TMP_PATH . '/locksecurity.php', $file);
			}
		}
 	}
	
	function write_index($path)
	{
		if(!is_file($path.'/index.php'))
			file_put_contents($path.'/index.php', '<?PHP /* Private! */ ?>');

		if(!is_file($path.'/index.php'))
			echo '<p class="error fade">'.$path.'/index.php was not written!</p>';
	}
	
	function delete_index($path)
	{
		if(is_file($path.'/index.php') && file_get_contents($path.'/index.php') == '<?PHP /* Private! */ ?>')
			unlink($path.'/index.php');
	}
	
	function write_ip_restriction()
	{
		$file = '<?PHP $this->options[\'restrictip\'] = true; if(is_admin() && $this->get_real_ip() != "'.$this->get_real_ip().'") wp_die("' . __("What do you think you are doing here?") . '") ?>';
		file_put_contents(XFW_TMP_PATH . '/ip.php', $file);
	}
}