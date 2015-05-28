<?php

if ( ! class_exists('ether_panel_ether_forms'))
{
	class ether_panel_ether_forms extends ether_panel
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function reset()
		{
			ether::handle_field(array(), array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'builder_color',
						'value' => ''
					),
					array
					(
						'name' => 'form_style',
						'value' => ''
					)
				)
			));

			ether::handle_field(array(), array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'recaptcha_private_key',
						'value' => ''
					),
					array
					(
						'name' => 'recaptcha_public_key',
						'value' => ''
					)
				)
			));
		}

		public static function save()
		{
			ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'builder_color',
						'value' => ''
					),
					array
					(
						'name' => 'form_style',
						'value' => ''
					)
				)
			));

			ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'recaptcha_private_key',
						'value' => ''
					),
					array
					(
						'name' => 'recaptcha_public_key',
						'value' => ''
					)
				)
			));
		}

		public static function body()
		{
			$colors = array('light' => array('name' => ether::langr('Light')), 'dark' => array('name' => ether::langr('Dark')));

			return '<fieldset class="ether-form">
				<h2 class="title">'.ether::langr('Forms').'</h2>
				<hr class="ether-divider">
				<h3 class="title">'.ether::langr('Styles').'</h3>
				<div class="cols cols-1">
					<div class="col">
						<div class="inline-labels">
							<label><span>'.ether::langr('Color scheme').'</span> '.ether::make_field('builder_color', array('type' => 'select', 'relation' => 'option', 'options' => $colors, 'style' => 'width: 200px')).'</label>
						</div>
					</div>
				</div>
				<hr class="ether-divider">
				<h3 class="title">'.ether::langr('ReCAPTCHA').'</h3>
				<div class="cols cols-2">
					<div class="col">
						<label><span>'.ether::langr('ReCAPTCHA private key').':</span> '.ether::make_field('recaptcha_private_key', array('type' => 'text', 'relation' => 'option')).'</label>
					</div>
					<div class="col">
						<label><span>'.ether::langr('ReCAPTCHA public key').':</span> '.ether::make_field('recaptcha_public_key', array('type' => 'text', 'relation' => 'option')).'</label>
					</div>
				</div>
				<hr class="ether-divider">
				<h3 class="title">'.ether::langr('Custom styles').'</h3>
				<div class="cols cols-1">
					<div class="col">
						<label>'.ether::langr('Custom CSS').' '.ether::make_field('form_style', array('type' => 'textarea', 'rows' => '10')).'</label>
					</div>
				</div>
				<hr class="ether-divider">
			</fieldset>';
		}
	}
}

?>
