<?php

	if ( ! class_exists('ether_post_form'))
	{
		class ether_post_form extends ether_post_type
		{
			public static function init()
			{
				add_action('admin_head', array('ether_post_form', 'admin_head'));
				add_action('load-post-new.php', array('ether_post_form', 'add_new'));
				add_action('admin_menu', array('ether_post_form', 'admin_menu'), 20);
				// post_row_actions for non hierarchical post type and page_row_actions for hierarchical post type :o
				add_filter('page_row_actions', array('ether_post_form', 'row_actions'));
				add_filter('parent_file', array('ether_post_form', 'parent_file'));

				if (is_admin())
				{
					$GLOBALS['wp']->add_query_var('post_parent');
				}

				ether::admin_column('Form', array('permissions' => array('form-entry'), 'callback' => array('ether_post_form', 'post_parent_column'), 'meta' => '', 'order' => 2));
				ether::admin_column('Export', array('permissions' => array('form-entry'), 'callback' => array('ether_post_form', 'post_export_column'), 'meta' => '', 'order' => 3));
				//ether::admin_column('Entries count', array('permissions' => array('form'), 'callback' => array('ether_post_form', 'form_entries_count_column'), 'meta' => '', 'order' => 2));
				ether::admin_column('Entries', array('permissions' => array('form'), 'callback' => array('ether_post_form', 'form_entries_column'), 'meta' => '', 'order' => 2));
				ether::admin_column('Shortcode', array('permissions' => array('form'), 'callback' => array('ether_post_form', 'form_shortcode_column'), 'meta' => '', 'order' => 3));
				ether::admin_column('Export', array('permissions' => array('form'), 'callback' => array('ether_post_form', 'form_export_column'), 'meta' => '', 'order' => 4));

				ether::register_post('Form', array
				(
					'supports' => array('title'),
					'hierarchical' => FALSE,
					'exclude_from_search' => TRUE,
					'show_in_menu' => TRUE,
					'show_in_nav_menus' => FALSE,
					'public' => TRUE,
					'publicly_queryable' => FALSE,
					'rewrite' => FALSE,
					'has_archive' => FALSE,
					'menu_position' => '29',
					'menu_icon' => ether::path('admin/media/images/posts/form.png', TRUE),
					'labels' => array
					(
						'name' => ether::langx('Forms', 'CPT name', TRUE),
						'singular_name' => ether::langx('Form', 'CPT singular name', TRUE),
						'add_new' => ether::langx('New form', 'CPT', TRUE),
						'add_new_item' => ether::langx('Add new form', 'CPT', TRUE),
						'edit_item' => ether::langx('Edit form', 'CPT', TRUE),
						'new_item' => ether::langx('New form', 'CPT', TRUE),
						'view_item' => ether::langx('View form', 'CPT', TRUE),
						'search_items' => ether::langx('Search forms', 'CPT', TRUE),
						'not_found' =>  ether::langx('No forms found', 'CPT', TRUE),
						'not_found_in_trash' => ether::langx('No forms found in Trash', 'CPT', TRUE),
						'menu_name' => ether::langx('Forms', 'CPT menu name', TRUE)
					)
				));

				ether::register_post('Form entry', array
				(
					'supports' => array('page-attributes'),
					'hierarchical' => TRUE,
					'exclude_from_search' => TRUE,
					'show_in_menu' => TRUE,
					'show_in_nav_menus' => FALSE,
					'public' => TRUE,
					'publicly_queryable' => FALSE,
					'rewrite' => FALSE,
					'has_archive' => FALSE,
					'menu_position' => 29.5,
					'menu_icon' => ether::path('admin/media/images/posts/form-entry.png', TRUE),
					'labels' => array
					(
						'name' => ether::langx('Form entries', 'CPT name', TRUE),
						'singular_name' => ether::langx('Form entry', 'CPT singular name', TRUE),
						'add_new' => ether::langx('New form entry', 'CPT', TRUE),
						'add_new_item' => ether::langx('Add new form entry', 'CPT', TRUE),
						'edit_item' => ether::langx('View form entry', 'CPT', TRUE),
						'new_item' => ether::langx('New form', 'CPT', TRUE),
						'view_item' => ether::langx('View form entry', 'CPT', TRUE),
						'search_items' => ether::langx('Search form entries', 'CPT', TRUE),
						'not_found' =>  ether::langx('No form entries found', 'CPT', TRUE),
						'not_found_in_trash' => ether::langx('No form entries found in Trash', 'CPT', TRUE),
						'menu_name' => ether::langx('Entries', 'CPT menu name', TRUE)
					)
				));
			}

			public static function add_new()
			{
				if (get_current_screen()->post_type == 'form-entry')
				{
					wp_die('Access denied!');
				}
			}

			public static function admin_head()
			{
				if (get_current_screen()->post_type == 'form-entry')
				{
					echo '<style type="text/css">.add-new-h2 { display: none; }</style>';
				}
			}

			public static function admin_menu()
			{
				global $submenu;
				global $menu;
				global $_wp_real_parent_file;
				global $_wp_submenu_nopriv;
				global $_registered_pages;
				global $_parent_pages;
				global $parent_file;
				global $submenu_file;

				foreach ($menu as $menu_key => $menu_data)
				{
					if (in_array('edit.php?post_type=form-entry', array_values($menu_data)))
					{
						unset($menu[$menu_key]);
					}
				}

				if (isset($submenu['edit.php?post_type=form']))
				{
					unset($submenu['edit.php?post_type=form-entry'][10]);
					$submenu['edit.php?post_type=form'] = array_merge($submenu['edit.php?post_type=form'], $submenu['edit.php?post_type=form-entry']);
				}
			}

			public static function row_actions($actions)
			{
				global $post;

				if ($post->post_type == 'form-entry')
				{
					unset($actions['edit']);
					unset($actions['inline hide-if-no-js']);
				}

				return $actions;
			}

			public static function parent_file($parent_file)
			{
				if (get_current_screen()->post_type == 'form-entry')
				{
					$parent_file = 'edit.php?post_type=form';
				}

				return $parent_file;
			}

			public static function post_parent_column($name)
			{
				if ($name == 'form')
				{
					global $post;

					echo '<a href="edit.php?post_type=form-entry&post_parent='.$post->post_parent.'">'.ether::langr('View entries from this form').'</a>';
				}
			}

			public static function post_export_column($name)
			{
				if ($name == 'export')
				{
					global $post;

					echo '<a href="'.ether::path('ether/ether.php?form-entry-export&form-entry='.$post->ID, TRUE).'">'.ether::langr('Download this entry (CSV)').'</a>';
				}
			}

			public static function form_entries_count_column($name)
			{
				if ($name == 'entries-count')
				{
					global $post;

					$count = ether::meta('entry_count', TRUE, $post->ID);

					if (empty($count))
					{
						$count = 0;
					}

					echo $count;
				}
			}

			public static function form_entries_column($name)
			{
				if ($name == 'entries')
				{
					global $post;

					echo '<a href="edit.php?post_type=form-entry&post_parent='.$post->ID.'">'.ether::langr('View entries from this form').'</a>';
				}
			}

			public static function form_shortcode_column($name)
			{
				if ($name == 'shortcode')
				{
					global $post;

					echo '[form id="'.$post->ID.'"]';
				}
			}

			public static function form_export_column($name)
			{
				if ($name == 'export')
				{
					global $post;

					echo '<a href="'.ether::path('ether/ether.php?form-entry-export&form='.$post->ID, TRUE).'">'.ether::langr('Download entries from this form (CSV)').'</a>';
				}
			}
		}
	}
?>
