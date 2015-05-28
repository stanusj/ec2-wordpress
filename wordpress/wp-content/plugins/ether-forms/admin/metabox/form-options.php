<?php

if ( ! class_exists('ether_metabox_form_options'))
{
	class ether_metabox_form_options extends ether_metabox
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function save($post_id)
		{
			ether::handle_field($_POST, array
			(
				'checkbox' => array
				(
					array
					(
						'name' => 'form_email_notification',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'form_email_copy',
						'relation' => 'meta',
						'value' => ''
					)
				),
				'textarea' => array
				(
					array
					(
						'name' => 'form_description',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'confirmation_message',
						'relation' => 'meta',
						'value' => ''
					)
				),
				'text' => array
				(
					array
					(
						'name' => 'notification_email',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'copy_email',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'confirmation_url',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_text',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_text_color',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_background_color',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_width',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_additional_classes',
						'relation' => 'meta',
						'value' => ''
					)
				),
				'select' => array
				(
					array
					(
							'name' => 'entry_title',
							'relation' => 'meta',
							'value' => ''
					),
					array
					(
							'name' => 'from_email',
							'relation' => 'meta',
							'value' => ''
					),
					array
					(
						'name' => 'confirmation_type',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_style',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'button_align',
						'relation' => 'meta',
						'value' => ''
					),
					array
					(
						'name' => 'form_style',
						'relation' => 'meta',
						'value' => ''
					)
				)
			));
		}

		public static function body()
		{
			global $post;
			
			$buildarray = get_post_meta($post->ID, 'ether_builder_data', true);
			$hmm = ult_array_search($buildarray,'__SLUG__','form-email');
			$tmm = ult_array_search($buildarray,'__SLUG__','form-text-input');
			$emails = array (
					'0' => array('name' => ether::langr('Select'))
			);
			$textfields = array (
					'0' => array('name' => ether::langr('Select'))
			);
			foreach ($hmm as $key=>$value){
				$emails[$key] =array('name'=>$value['label']);
			}
			foreach ($tmm as $key=>$value){
				$textfields[$key] =array('name'=>$value['label']);
			}
			
			$body = '<fieldset class="ether-form">';

			$conf_options = array
			(
				'message' => array('name' => ether::langr('Show message')),
				'redirect' => array('name' => ether::langr('Redirect'))
			);

			$button_styles = array
			(
				'1' => array('name' => ether::langr('Small')),
				'2' => array('name' => ether::langr('Medium')),
				'3' => array('name' => ether::langr('Big'))
			);

			$button_align = array
			(
				'left' => array('name' => ether::langr('Left')),
				'right' => array('name' => ether::langr('Right')),
				'center' => array('name' => ether::langr('Center'))
			);

			$form_style = array
			(
				'' => array('name' => ether::langr('Default')),
				'inline' => array('name' => ether::langr('Inline fields')),
				'short' => array('name' => ether::langr('Short fields'))
			);

			$body .= '
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label>'.ether::langr('Form style').' '.ether::make_field('form_style', array('type' => 'select', 'options' => $form_style, 'relation' => 'meta')).'</label>
					<label>'.ether::langr('Form description').' '.ether::make_field('form_description', array('type' => 'textarea', 'relation' => 'meta')).'</label>
					<label>'.ether::langr('Entry Title').' '.ether::make_field('entry_title', array('type' => 'select', 'options' => $textfields,'relation' => 'meta')).'</label>
					<label>'.ether::make_field('form_email_notification', array('type' => 'checkbox', 'class' => 'ether-cond-field ether-field-form-email-notification', 'relation' => 'meta')).' '.ether::langr('Enable email notification').'</label>
					<label class="ether-cond-group ether-action-show-ether-cond-on-ether-field-form-email-notification">'.ether::langr('Email address for email notification (comma separated)').' '.ether::make_field('notification_email', array('type' => 'text', 'relation' => 'meta')).'</label>
					<label>'.ether::make_field('form_email_copy', array('type' => 'checkbox', 'class' => 'ether-cond-field ether-field-send-form-via-email', 'relation' => 'meta')).' '.ether::langr('Send form entries via email').'</label>
					<label class="ether-cond-group ether-action-show-ether-cond-on-ether-field-send-form-via-email">'.ether::langr('Email address for sent entries (comma separated)').' '.ether::make_field('copy_email', array('type' => 'text', 'relation' => 'meta')).'</label>
					<label class="ether-cond-on ether-group-2">'.ether::langr('Reply to (email field included in form)').' '.ether::make_field('from_email', array('type' => 'select', 'options' => $emails,  'relation' => 'meta')).'</label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Confirmation').'</h2>
				<div class="ether-tab-content">
					<label>'.ether::langr('Confirmation type').' '.ether::make_field('confirmation_type', array('type' => 'select', 'class' => 'ether-cond-field ether-field-confirmation-type', 'options' => $conf_options, 'relation' => 'meta')).'</label>
					<label class="ether-cond-group ether-action-show-ether-cond-message-ether-field-confirmation-type">'.ether::langr('Message').' '.ether::make_field('confirmation_message', array('type' => 'textarea', 'relation' => 'meta')).'</label>
					<label class="ether-cond-group ether-action-show-ether-cond-redirect-ether-field-confirmation-type">'.ether::langr('Redirection URL').' '.ether::make_field('confirmation_url', array('type' => 'text', 'relation' => 'meta')).'</label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Button').'</h2>
				<div class="ether-tab-content">
					<label>'.ether::langr('Button text').' '.ether::make_field('button_text', array('type' => 'text', 'relation' => 'meta')).'</label>
					<label>'.ether::langr('Button style').' '.ether::make_field('button_style', array('type' => 'select', 'options' => $button_styles, 'relation' => 'meta', 'value' => '2', 'use_default' => TRUE)).'</label>
					<label>'.ether::langr('Button alignment').' '.ether::make_field('button_align', array('type' => 'select', 'options' => $button_align, 'relation' => 'meta', 'value' => 'right', 'use_default' => TRUE)).'</label>
					<label>'.ether::langr('Button width').' '.ether::make_field('button_width', array('type' => 'text', 'relation' => 'meta')).'</label>
					<div class="cols-2 cols">
						<div class="col">
							<label class="ether-color"><span class="label-title">'.ether::langr('Background color').'</span> '.ether::make_field('button_background_color', array('type' => 'text', 'relation' => 'meta')).'<small>'.ether::langr('hex, rgb or rgba. Overrides default.').'</small></label>
						</div>
						<div class="col">
							<label class="ether-color"><span class="label-title">'.ether::langr('Text color').'</span> '.ether::make_field('button_text_color', array('type' => 'text', 'relation' => 'meta')).'<small>'.ether::langr('hex, rgb or rgba. Overrides default.').'</small></label>
						</div>
					</div>
					<label>'.ether::langr('Button additional classes').' '.ether::make_field('button_additional_classes', array('type' => 'text', 'relation' => 'meta')).'</label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Shortcode').'</h2>
				<div class="ether-tab-content">
					<pre><code>[form id="'.(isset($post->ID) ? $post->ID : '').'"]</code></pre>
				</div>
			';

			$body .= '</fieldset>';

			return $body;
		}
	}
}

?>
