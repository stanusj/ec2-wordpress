<?php

if ( ! class_exists('ether_panel_ether_license'))
{
	class ether_panel_ether_license extends ether_panel
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function reset()
		{
			ether::handle_field_group(array(), array
			(
				'license_key' => array()
			));

			ether::handle_field_group(array(), array
			(
				'license_service' => array()
			));
		}

		public static function save()
		{
			$branches = ether::branches();

			$branches_fields = array();

			foreach ($branches as $branch_name => $branch_data)
			{
				$branches_fields[] = array
				(
					'name' => ''.$branch_name.'_key',
					'value' => ''
				);

				$branches_fields[] = array
				(
					'name' => ''.$branch_name.'_service',
					'value' => ''
				);
			}

			ether::handle_field_group($_POST, array
			(
				'license' => array_merge(array('relation' => 'option'), $branches_fields)
			));
		}

		public static function body()
		{
			$services = array
			(
				'envato' => array('name' => ether::langr('Envato')),
				'ether' => array('name' => ether::langr('Ether'))
			);

			$license = ether::option('license');

			if ( ! empty($license))
			{
				$license = $license[0];
			}

			$branches = ether::branches();

			$licenses = '';

			foreach ($branches as $branch_name => $branch_data)
			{
				$licenses .= '
				<hr class="ether-divider">
				<h3 class="title">'.$branch_data['name'].'</h3><div class="cols cols-2">
					<div class="col">
						<label>'.$branch_data['name'].' '.ether::langr('license key').' '.ether::make_field($branch_name.'_key[]', array('type' => 'text', 'relation' => 'custom'), $license).'</label>
					</div>
					<div class="col">
						<label>'.$branch_data['name'].' '.ether::langr('license service').' '.ether::make_field($branch_name.'_service[]', array('type' => 'select', 'relation' => 'custom', 'options' => $services), $license).'</label>
					</div>
				</div>
				<hr class="ether-divider">';
			}

			return '<fieldset class="ether-form">
				<h2 class="title">'.ether::langr('License').'</h2>
				'.$licenses.'
			</fieldset>';
		}
	}
}

?>
