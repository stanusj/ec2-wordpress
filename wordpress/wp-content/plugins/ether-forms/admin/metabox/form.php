<?php

if ( ! class_exists('ether_metabox_form'))
{
	class ether_metabox_form extends ether_metabox
	{
		public static function init()
		{

		}

		public static function header()
		{
			if(is_admin() && ether_forms_ult_is_post_type('form'))
			{
				$screen = get_current_screen();

				if ( ! empty($screen->post_type) OR $screen->id == 'widgets')
				{
					ether::script( array
					(
						array
						(
							'path' => 'admin/media/scripts/builder.js',
							'deps' => array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'),
							'version' => ether::config('version')
						),
						array
						(
							'path' => 'admin/media/scripts/forms.js',
							'deps' => array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'),
							'version' => ether::config('version')
						)
					));

					ether::stylesheet( array
					(
						array
						(
							'path' => 'admin/media/stylesheets/builder.css',
							'version' => ether::config('version')
						),
						array
						(
							'path' => 'admin/media/stylesheets/forms.css',
							'version' => ether::config('version')
						)
					));
				}
			}
		}

		public static function save($post_id)
		{
			global $post;
			global $post_type;

			if ($post != NULL)
			{
				if (isset($_POST['ether_builder_widget']['__LOCATION__']))
				{
					unset($_POST['ether_builder_widget']['__LOCATION__']);
				}

				if (isset($_POST['ether_builder_widget']['__ID__']))
				{
					unset($_POST['ether_builder_widget']['__ID__']);
				}

				ether::meta('builder_data', $_POST['ether_builder_widget'], $post->ID, TRUE);
			}
		}

		public static function get_prototypes()
		{
			$col_widgets_separator = FALSE;
			$widgets_output = '';

			$widgets = ether_form::get_widgets();

			// foreach ($widgets as $widget)
			// {
			// 	$widgets_output .= $widget->admin_form(/*$_D['widgets']*/);
			// }

			$widgets_output .= '<h3>Columns:</h3>';

			foreach ($widgets as $widget)
			{
				// if (isset($builder_hidden_widgets[$widget->get_slug()]) AND $builder_hidden_widgets[$widget->get_slug()] == 'on')
				// {
				// 	$widget->hide();
				// }

				if ($col_widgets_separator === FALSE && strpos($widget->get_slug(), 'row') === FALSE)
				{
					// $widgets_output .= '<hr class="builder-widgets-separator" />';
					$widgets_output .= '<h3>Widgets:</h3>';
					$col_widgets_separator = TRUE;
				}

				$widgets_output .= $widget->admin_form(/*$_D['widgets']*/);
			}

			$body = '<div id="builder-widgets" style="display: none;">
				<button name="builder-modal-close" class="builder-modal-close">close</button>
				<fieldset class="ether-form filter-builder-widgets">
					<label class="filter"><input type="text" placeholder="'.ether::langr('Filter widgets').'" name="builder-widget-filter" value="" /></label>
				</fieldset>
				<div class="builder-widgets-wrap">
				'.$widgets_output.'
				</div>
			</div>';

			$body = '<div id="builder-widgets" style="display: none;">
				<button name="builder-modal-close" class="builder-modal-close">'.ether::langr('close').'</button>
				<fieldset class="ether-form filter-builder-widgets">
					<label class="filter"><input type="text" placeholder="'.ether::langr('Filter widgets').'" name="builder-widget-filter" value="" /></label>
				</fieldset>
				<div class="builder-widgets-wrap">
				'.$widgets_output.'
				</div>
			</div>';

			return $body;
		}

		public static function body($builder_data = array(), $parent_id = NULL, $read_only = FALSE)
		{
			global $post;
			global $post_type;

			$body = '';

			add_filter('user_can_richedit', '__return_true');
			ob_start();
			wp_editor('dummy_editor_only_for_tinymce_initialization');
			$dirty_solution = ob_get_clean();
			remove_filter('user_can_richedit', '__return_true');

			//if (($post->post_type == 'section' OR $post->post_type == 'portfolio') OR $force_output)
			{
				$presets = array
				(
					'countries' => ether_forms_countries(),
					'genders' => ether_forms_genders(),
					'us_states' => ether_forms_us_states(),
					'continents' => ether_forms_continents(),
					'days' => ether_forms_days(),
					'months' => ether_forms_months()
				);

				$presets = apply_filters('ether_form_presets', $presets);

				$body .= '<script type="text/javascript">
					ether.form_presets = '.json_encode($presets).'
				</script>';

				$thumb_size = '-'.get_option('thumbnail_size_w').'x'.get_option('thumbnail_size_h');
			    $body .= '<div id="builder-thumb-size" class="'.$thumb_size.'" style="display:none;"></div>';

				$body .= '<div id="builder-location-wrapper" class="builder-location-wrapper'.($read_only ? ' read-only' : '').'"><fieldset class="ether-form">';//.($post_type == 'portfolio' ? '<p class="hint">'.ether::langr('The following layout will be applied to all projects that belong to this portfolio. Make sure gallery widget is included or else you won\'t be able to add images to those projects.').'</p>' : '');

				$widgets_output = '';
				$widgets = ether_form::get_widgets();
				$locations = ether_form::get_locations();
				$form_widgets = array();

				if ( ! is_array($builder_data))
				{
					$builder_data = array();
				}

				$tmp_post = NULL;

				if (isset($_GET['post']) AND $_GET['post'] != $post->ID)
				{
					$tmp_post = $post;

					$post = get_post($_GET['post']);
				}

				$id = $post->ID;

				if ($parent_id != NULL AND $parent_id > 0)
				{
					$id = $parent_id;
				}

				$form_widgets = ether::meta('builder_data', TRUE, $id);

				foreach ($locations as $location => $name)
				{
					$form_widgets_output = '';

					$body .= '<button name="builder-widget-add" class="builder-location-widget-add" style="display:none"><span>'.ether::langr('Add widget').'</span></button>';
					$body .= '<div id="builder-location-'.$location.'" class="builder-location" style="display:none">';
					$body .= ether_form::parse($form_widgets, $location, TRUE, $builder_data);
					$body .= '</div>';
					$body .= '<button name="builder-widget-add" class="builder-location-widget-add" style="display:none"><span>'.ether::langr('Add widget').'</span></button>';
				}

				if ($tmp_post != NULL)
				{
					$post = $tmp_post;
				}

				$body .= '</fieldset></div>';
			}

			return $body;
		}
	}
}

?>
