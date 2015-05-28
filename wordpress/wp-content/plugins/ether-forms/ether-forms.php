<?php
/*
Plugin Name: Ether Forms
Plugin URI: http://onether.com/ether-forms
Description: Ether Forms WordPress Plugin
Author: ether
Author URI: http://onether.com
Version: 1.3.4
ULT ID: 17
*/
$current_default= get_option( 'stylesheet' );
$theme = wp_get_theme( $current_default );
if(strtolower($theme->get('Template')) =='ultimatum' || strtolower($theme->get('Name')) =='ultimatum'){
	/**
	 * Whether the current request is in post type pages
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type pages
	 */
	function ether_forms_ult_is_post_type($post_types = ''){
		if(isset($_GET['page']) && ($_GET['page']=='ether-ether' || $_GET['page']=='ether-ether-forms') ) {
			return true;		
		}
		if(ether_forms_ult_is_post_type_list($post_types) || ether_forms_ult_is_post_type_new($post_types) || ether_forms_ult_is_post_type_edit($post_types) || ether_forms_ult_is_post_type_post($post_types) || ether_forms_ult_is_post_type_taxonomy($post_types)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * Whether the current request is in post type list page
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type list page
	 */
	function ether_forms_ult_is_post_type_list($post_types = '') {
		if ('edit.php' != basename($_SERVER['PHP_SELF'])) {
			return false;
		}
		if ($post_types == '') {
			return true;
		} else {
			$check = isset($_GET['post_type']) ? $_GET['post_type'] : (isset($_POST['post_type']) ? $_POST['post_type'] : 'post');
			if (is_string($post_types) && $check == $post_types) {
				return true;
			} elseif (is_array($post_types) && in_array($check, $post_types)) {
				return true;
			}
			return false;
		}
	}
	
	/**
	 * Whether the current request is in post type new page
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type new page
	 */
	function ether_forms_ult_is_post_type_new($post_types = '') {
		if ('post-new.php' != basename($_SERVER['PHP_SELF'])) {
			return false;
		}
		if ($post_types == '') {
			return true;
		} else {
			$check = isset($_GET['post_type']) ? $_GET['post_type'] : (isset($_POST['post_type']) ? $_POST['post_type'] : 'post');
			if (is_string($post_types) && $check == $post_types) {
				return true;
			} elseif (is_array($post_types) && in_array($check, $post_types)) {
				return true;
			}
			return false;
		}
	}
	/**
	 * Whether the current request is in post type post page
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type post page
	 */
	function ether_forms_ult_is_post_type_post($post_types = '') {
		if ('post.php' != basename($_SERVER['PHP_SELF'])) {
			return false;
		}
		if ($post_types == '') {
			return true;
		} else {
			$post = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post']) ? $_POST['post'] : false);
			$check = get_post_type($post);
	
			if (is_string($post_types) && $check == $post_types) {
				return true;
			} elseif (is_array($post_types) && in_array($check, $post_types)) {
				return true;
			}
			return false;
		}
	}
	/**
	 * Whether the current request is in post type edit page
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type edit page
	 */
	function ether_forms_ult_is_post_type_edit($post_types = '') {
		if ('post.php' != basename($_SERVER['PHP_SELF'])) {
			return false;
		}
		$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
		if ('edit' != $action) {
			return false;
		}
	
		if ($post_types == '') {
			return true;
		} else {
			$post = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post']) ? $_POST['post'] : false);
			$check = get_post_type($post);
	
			if (is_string($post_types) && $check == $post_types) {
				return true;
			} elseif (is_array($post_types) && in_array($check, $post_types)) {
				return true;
			}
			return false;
		}
	}
	/**
	 * Whether the current request is in post type taxonomy pages
	 *
	 * @param mixed $post_types
	 * @return bool True if inside post type taxonomy pages
	 */
	function ether_forms_ult_is_post_type_taxonomy($post_types = '') {
		if ('edit-tags.php' != basename($_SERVER['PHP_SELF'])) {
			return false;
		}
		if ($post_types == '') {
			return true;
		} else {
			$check = isset($_GET['post_type']) ? $_GET['post_type'] : (isset($_POST['post_type']) ? $_POST['post_type'] : 'post');
			if (is_string($post_types) && $check == $post_types) {
				return true;
			} elseif (is_array($post_types) && in_array($check, $post_types)) {
				return true;
			}
			return false;
		}
	}

if ( ! class_exists('ether'))
{
	include('ether/ether.php');
}

ether::config('name', 'Ether Forms');
ether::config('version', '1.3.3');
ether::config('debug', FALSE);
ether::config('debug_wordpress', FALSE);
ether::config('debug_sql', FALSE);
ether::config('debug_echo', TRUE);
ether::config('fix_formatting', FALSE);
ether::config('image_frame_class', 'ether-frame ether-frame-1');
ether::config('hide_title', FALSE);
ether::config('register_default_sidebar', FALSE);
ether::config('form_widget_prefix', 'ether-');
ether::config('form_lightbox', TRUE);

ether::configjs('builder_lang', array
(
	'quit' => ether::langr('Quit without saving?'),
	'changes' => ether::langr('Lose editor changes?'),
	'sure' => ether::langr('Are you sure?')
));

ether::init();

ether::depend('wp', '3.1.0');
ether::depend('php', '5.2.3');

ether::depend('function', 'imagecreatetruecolor');
ether::depend('function', 'imagecolorallocatealpha');
ether::depend('function', 'imagecolortransparent');
ether::depend('function', 'json_decode');
ether::depend('function', 'file_get_contents');

ether::depend('function', 'curl_init');
ether::depend('class', 'ZipArchive');

$uploads = wp_upload_dir();

ether::depend(array('directory', 'writable'), $uploads['basedir']);
ether::depend(array('directory', 'writable'), $uploads['basedir'].'/'.ether::config('upload_dir'));

ether::module('post.form');
ether::module('form');
ether::module('update');

//ether::admin_panel('Update', array('title' => ether::langx('Update', 'admin panel name', TRUE), 'group' => 'Ether'));
//ether::admin_panel('License', array('title' => ether::langx('License', 'admin panel name', TRUE), 'group' => 'Ether'));
ether::admin_panel('Forms', array('title' => ether::langx('Forms', 'admin panel name', TRUE), 'group' => 'Ether'));

add_action('init', array('ether_form', 'buffer_output'));
add_filter('get_post_metadata', array('ether_form', 'unserialize_fix'), 10, 4);
add_action('admin_head', array('ether_form', 'header'));
add_action('wp_head', array('ether_form', 'header'), 999);
add_action('ether_setup', array('ether_form', 'widgets_init'));
add_action('widgets_init', array('ether_form', 'sidebar_init'));
add_action('admin_footer', array('ether_form', 'form_prototypes'));

add_shortcode('form', array('ether_form', 'form_shortcode'));
add_shortcode('form-entry', array('ether_form', 'form_entry_shortcode'));

if ( ! function_exists('ether_form_init'))
{
	function ether_form_init()
	{
		ether::admin_metabox('Form', array('title' => ether::langx('Form', 'metabox name', TRUE), 'permissions' => array('form')));
		ether::admin_metabox('Form options', array('title' => ether::langx('Options', 'metabox name', TRUE), 'permissions' => array('form')));
		ether::admin_metabox('Form entry', array('title' => ether::langx('Entry', 'metabox name', TRUE), 'permissions' => array('form-entry')));
		ether::admin_metabox('Form meta', array('title' => ether::langx('Meta', 'metabox name', TRUE), 'permissions' => array('form-entry'), 'context' => 'side'));
	}

	add_action('ether_setup', 'ether_form_init');
}

if ( ! function_exists('ether_form_header'))
{
	function ether_form_header()
	{
		$color = ether::option('builder_color');

		if (empty($color))
		{
			$color = 'light';
		}

		ether::stylesheet('ether-forms', 'media/stylesheets/ether-forms-'.$color.'.css', NULL, ether::config('version'));
		ether::stylesheet('ether-forms-ie7', 'media/stylesheets/ether-forms-ie7.css', array('ether-builder'), ether::config('version'), 'IE 7');

		if (ether::config('form_lightbox'))
		{
			ether::stylesheet('jquery.colorbox', 'media/stylesheets/libs/colorbox/colorbox.css');
			ether::script('jquery.colorbox', 'media/scripts/libs/jquery.colorbox.js', array('jquery'));
		}

		ether::script('ether-builder', 'media/scripts/ether-builder.js', array('jquery', 'jquery.colorbox'), ether::config('version'));
		ether::script('ether-forms', 'media/scripts/ether-forms.js', array('jquery', 'jquery.colorbox'), ether::config('version'));
	}
	if(is_admin() && !ether_forms_ult_is_post_type('form')){
	} else {
		add_action('ether_form_header', 'ether_form_header');
	}
}

if ( ! function_exists('ether_forms_backup_config'))
{
	function ether_forms_backup_config()
	{
		if (class_exists('ether_backup'))
		{
			ether_backup::add_url_rule('meta_serialized', 'ether_form_data');
			ether_backup::add_url_rule('meta_serialized', 'ether_form_widget_post_data');
		}
	}

	add_action('ether_setup', 'ether_forms_backup_config');
}




}
?>
