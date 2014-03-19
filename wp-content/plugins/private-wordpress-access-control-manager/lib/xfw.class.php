<?php

/*
	This is the X-Blogs Plugin Framwork
*/

class xfw
{
	public $defaults;
	public $options;
	public $menu;
	public $menu_name;
	public $name;
	public $nice_name;
	public $level;
	public $error;
	public $hook;

	function load_lang()
	{
		load_plugin_textdomain($this->name, XFW_REL_PATH.'/lang');
	}

	function admin_menu()
	{
		add_submenu_page($this->menu, $this->nice_name, $this->menu_name, $this->level, $this->hook, array(&$this, 'settings'));
	}

	function get_options()
	{
		$this->options = get_option($this->name, $this->defaults);

		if(!is_array($this->options) && unserialize($this->options))
			$this->options = unserialize($this->options);
		else
			$this->options = $this->defaults;
	}

	function set_options()
	{
		update_option($this->name,serialize($this->options));
	}

	function form_row($type, $name, $value=false, $label=false, $description=false, $options=false, $inactive=false, $version=false)
	{
		if($type=='version')
		{
			echo '<input type="hidden" name="' . $this->name . '_' . $value . '" value="' . $this->defaults['version'] . '" />';
			return;
		}

		$new = ($this->options['version'] != '' && $version >= $this->options['version']) ? ' <span style="color:red">' . __('NEW!', $this->name) . '</span>' : '';

		$inactive = ($inactive != false) ? ' style="opacity:.25"' : '';
		$disabled = ($inactive != false) ? ' disabled="disabled"' : '';
		echo '<tr valign="top"'.$inactive.'><td scope="row" style="width:25%;"><strong>' . $name . $new . '</strong></td><td>';
		if($type=='submit')
			echo '<p style="text-align:right"><input type="submit" class="button-primary" value="' . $value . '" /></p>';
		if($type=='altsubmit')
			echo '<p style="text-align:right"><input type="submit" class="button-secondary" value="' . $value . '" /></p>';
		if($type=='checkbox')
			echo '<input type="hidden" name="' . $this->name . '_' . $value . '" value="0" /><input id="' . $this->name . '_' . $value . '" type="checkbox" name="' . $this->name . '_' . $value . '" value="1"' . ($this->options[$value]==true?' checked="checked"':'') . $disabled.' />';
		else if($type=='text')
			echo '<input id="' . $this->name . '_' . $value . '" type="text" name="' . $this->name . '_' . $value . '" value="' . __($this->options[$value], $this->name) . '" style="width:99%" />';
		else if($type=='text_small')
			echo '<input id="' . $this->name . '_' . $value . '" type="text" name="' . $this->name . '_' . $value . '" value="' . __($this->options[$value], $this->name) . '" style="width:100px" />';
		else if($type=='textarea')
			echo '<textarea id="' . $this->name . '_' . $value . '" type="text" name="' . $this->name . '_' . $value . '" style="width:99%">' . __(stripslashes($this->options[$value]), $this->name) . '</textarea>';
		else if($type=='custom')
			echo $value;
		else if($type=='select')
		{
			echo '<select id="' . $this->name . '_' . $value . '" name="' . $this->name . '_' . $value . '">';
			foreach($options AS $k => $v)
				echo '<option value="'.$k.'"'.(isset($this->options[$value]) && $k==$this->options[$value]?' selected="selected"':'').'>'.$v.'</option>';
			echo '</select>';
		}
		if($label!=false)
			echo ' <label for="' . $this->name . '_' . $value . '" />' . $label . '</label>';
		if($description != false)
			echo '<p class="setting-description" style="font-style: italic;">' . $description . '</p>';
		echo '</td></tr>';
	}

	function form_header_list($list)
	{
		echo '<div class="wrap"><h2 id="private_headline">' . $this->nice_name . '</h2>';
		echo '<ul class="subsubsub">';
		$i = 1;
		foreach($list AS $k => $v)
		{
			$current = ($i==1) ? ' class="current"' : '';
			$last = ($i==count($list)) ? '' : '|';
			$v = ($v != false) ? ' <span class="count">('.$v.')</span>' : '';
			echo '<li><a href="#tab'.$i.'"'.$current.'>'.$k.''.$v.'</a>'.$last.'</li>';
			$i++;
		}
		echo '</ul><br /><br />';
	}

	function form_footer_list($list)
	{
		echo '</table><ul class="subsubsub">';
		$i = 1;
		foreach($list AS $k => $v)
		{
			$current = ($i==1) ? ' class="current"' : '';
			$last = ($i==count($list)) ? '' : '|';
			$v = ($v != false) ? ' <span class="count">('.$v.')</span>' : '';
			echo '<li><a href="#tab'.$i.'"'.$current.'>'.__($k, $this->name).''.$v.'</a>'.$last.'</li>';
			$i++;
		}
		echo '</ul><br /><br /><br />';
	}

	function form_page_top($headline, $id=false)
	{
		echo '<form ' . ($id!=false ? ' id="form_'.$id.'"' : '') . 'action="' . $_SERVER["REQUEST_URI"] . '" method="post"><table class="widefat"'.($id!=false ? ' id="tab'.$id.'"' : '').'><thead><tr><th scope="col" colspan="2" class="manage-column">' . $headline . '</th></tr></thead>';
	}

	function form_page_bottom()
	{
		echo '</table></form><br /><br />';
	}

	function form_break($headline, $num=false, $list=false)
	{
		$tab = ($num!=false) ? ' id="tab'.$num.'"' : '';
		$notab = ($num!=false) ? '' : ' notab';
		$break = ($num!=false) ? '' : '<br /><br />';
		$break = ($list!=false) ? $list : '';
		echo '</table>'.$break.'<table class="widefat'.$notab.'"'.$tab.'><thead><tr><th scope="col" colspan="2" class="manage-column">' . $headline . '</th></tr></thead>';
	}

	function process()
	{
		global $wpdb;

		if(!current_user_can($this->level))
			wp_die('<strong>' .__('Who are you and what are you doing in here?', $this->name) . '</strong>');

		$skip = false;
		foreach($_POST AS $option => $value)
		{
			$option = str_replace($this->name . '_', '', $option);

			if(is_array($value))
			{
				foreach($value as $k => $v)
				{
					if($v == '')
					{
						unset($value[$key]);
					}
				} 
				$value = serialize(array_values($value));
			}

			if($option == 'remove' && $value == '1')
			{
				$this->options=$this->defaults;
				delete_option($this->name);
				$this->message(__("Settings deleted", $this->name));
				$skip = true;
			}
			else if($option == 'remove' && $value == '2')
			{
				$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = 'allow_private_".$this->blog->id."'");
				$this->message(__("User settings deleted", $this->name));
			}
			else if($option == 'remove' && $value == '3')
			{
				delete_option('brute_log_'.$this->blog->id);
				$this->message(__("Brute force data deleted", $this->name));
			}
			else if($option == 'remove' && $value == '4')
			{
				$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = 'feed_key_".$this->blog->id."'");
				$this->message(__("Feed keys deleted", $this->name));
			}
			else if($option == 'remove' && $value == '5')
			{
				$this->options=$this->defaults;
				delete_option($this->name);
				$skip = true;
				$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = 'allow_private_".$this->blog->id."'");
				$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = 'feed_key_".$this->blog->id."'");
				delete_option('brute_log_'.$this->blog->id);
				$this->message(__("All data deleted", $this->name).'<br />'.__("User settings deleted", $this->name));
			}

			if(!isset($this->defaults[$option]))
				continue;

			if($skip != true)
				$this->options[$option] = $value;
		}
		$this->set_options();
		if($skip != true)
			$this->message(__("Settings updated", $this->name));
	}

	function message($message)
	{
		echo '<div class="fade updated"><p>' . $message . '</p></div>';
	}
}

if(!function_exists('get_current_site'))
{
	function get_current_site()
	{
		$site = (object)'site';
		$site->id = 0;
		return $site;
	}
}