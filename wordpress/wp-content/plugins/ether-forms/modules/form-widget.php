<?php

require_once('constants.php');

if ( ! class_exists('ether_form_text_input_widget'))
{
	class ether_form_text_input_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-text-input', ether::langr('Text input'));
			$this->label = ether::langr('Text input');
		}

		public static function widget_prototype($context, $widget, $input = array(), $errors = array())
		{
			return '<label for="'.$context->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <input type="text" name="'.$context->get_form_field().'" value="'.$context->get_form_value($widget, $input).'" />'.$context->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public static function form_prototype($context, $widget, $append_confirm_option = FALSE)
		{
			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols '.($append_confirm_option ? 'cols-2' : 'cols-1').'">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$context->field('text', 'label', $widget).'</label>
						</div>
						'.($append_confirm_option ?
						'<div class="col">
							<label>'.$context->field('checkbox', 'send_email', $widget).' <span class="label-title">'.ether::langr('Send form entry copy to this email address').'</span></label>
						</div>' : '').'
					</div>
					<div class="cols-3 cols">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max characters').'</span> '.$context->field('text', 'max_length', $widget).'</label>
						</div>
						<div class="col">
							<label>'.$context->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
						<div class="col">
							<label>'.$context->field('checkbox', 'unique', $widget).' <span class="label-title">'.ether::langr('Unique value').'</span><small>'.ether::langr('Unique value means duplicate entries in different fields will cause a form to not validate').'.</small></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$context->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$context->form_conditional($widget).'
				'.$context->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$context->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$context->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$context->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return self::widget_prototype($this, $widget, $input, $errors);
		}

		public function form($widget)
		{
			return self::form_prototype($this, $widget);
		}
	}
}

if ( ! class_exists('ether_form_file_widget'))
{
	class ether_form_file_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-file', ether::langr('File Uploader'));
			$this->label = ether::langr('File Uploader');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <input type="file" name="'.$this->get_form_field().'" value="'.$this->get_form_value($widget, $input).'" />'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'upload_file' => 'on'
				),
			$widget);

			$this->error_messages = array_merge($this->error_messages, array('upload'.UPLOAD_ERR_FORM_SIZE, 'upload'.UPLOAD_ERR_PARTIAL, 'upload'.UPLOAD_ERR_NO_FILE, 'upload'.UPLOAD_ERR_NO_TMP_DIR, 'upload'.UPLOAD_ERR_CANT_WRITE, 'upload'.UPLOAD_ERR_EXTENSION));

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('File types').'</span> '.$this->field('text', 'extensions', $widget).'<small>'.ether::langr('Comma separated allowed file extensions (e.g. "jpg, png, zip")').'</small></label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
					'.$this->field('hidden', 'upload_file', $widget).'
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_email_widget'))
{
	class ether_form_email_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-email', ether::langr('Email'));
			$this->label = ether::langr('Email Address');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Email'),
					'valid_email' => 'on'
				),
			$widget);

			return ether_form_text_input_widget::widget_prototype($this, $widget, $input, $errors);
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Email'),
					'valid_email' => 'on'
				),
			$widget);

			$this->error_messages[] = 'email';

			return ether_form_text_input_widget::form_prototype($this, $widget, TRUE).$this->field('hidden', 'valid_email', $widget);
		}
	}
}

if ( ! class_exists('ether_form_numeric_widget'))
{
	class ether_form_numeric_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-numeric', ether::langr('Numeric'));
			$this->label = ether::langr('Numeric text field');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend
			(
				array
				(
					'valid_numeric' => 'on',
					'min' => '',
					'max' => ''
				),
			$widget);

			return ether_form_text_input_widget::widget_prototype($this, $widget, $input, $errors);
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'valid_numeric' => 'on'
				),
			$widget);

			$this->error_messages[] = 'numeric';
			$this->error_messages[] = 'min';
			$this->error_messages[] = 'max';

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
					<div class="cols-2 cols">
						<div class="col">
							<label>'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'unique', $widget).' <span class="label-title">'.ether::langr('Unique value').'</span><small>'.ether::langr('Unique value means duplicate entries in different fields will cause a form to not validate').'.</small></label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Min numeric value').'</span> '.$this->field('text', 'min', $widget).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max numeric value').'</span> '.$this->field('text', 'max', $widget).'</label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
					'.$this->field('hidden', 'valid_numeric', $widget).'
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_url_widget'))
{
	class ether_form_url_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-url', ether::langr('URL'));
			$this->label = ether::langr('Address url');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('URL'),
					'valid_url' => 'on'
				),
			$widget);

			return ether_form_text_input_widget::widget_prototype($this, $widget, $input, $errors);
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('URL'),
					'valid_url' => 'on'
				),
			$widget);

			$this->error_messages[] = 'url';

			return ether_form_text_input_widget::form_prototype($this, $widget).$this->field('hidden', 'valid_url', $widget);
		}
	}
}

if ( ! class_exists('ether_form_phone_number_widget'))
{
	class ether_form_phone_number_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-phone-number', ether::langr('Phone number'));
			$this->label = ether::langr('Phone number');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <input type="text" name="'.$this->get_form_field().'" value="'.$this->get_form_value($widget, $input).'" />'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Phone number'),
					'format' => '(XXX) XXX-XXXX'
				),
			$widget);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
					<div class="cols-2 cols">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Format').'</span> '.$this->field('text', 'format', $widget).'<small>'.ether::langr('"X" stands for 0-9 digit. "A" stands for alphanumeric characters. Spaces are always optional. brackets () make group optional. Example: Input of "00 AB" will validate against "AA XX" format.  ').'</small></label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max characters').'</span> '.$this->field('text', 'max_length', $widget).'</label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'unique', $widget).' <span class="label-title">'.ether::langr('Unique value').'</span><small>'.ether::langr('Unique value means duplicate entries in different fields will cause a form to not validate').'.</small></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_zip_code_widget'))
{
	class ether_form_zip_code_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-zip-code', ether::langr('Zip code'));
			$this->label = ether::langr('Zip code');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <input type="text" name="'.$this->get_form_field().'" value="'.$this->get_form_value($widget, $input).'" />'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Zip code'),
					'format' => 'XXXXX'
				),
			$widget);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
					<div class="cols-2 cols">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Format').'</span> '.$this->field('text', 'format', $widget).'<small>'.ether::langr('"X" stands for 0-9 digit. "A" stands for alphanumeric characters. Spaces are always optional. brackets () make group optional. Example: Input of "00 AB" will validate against "AA XX" format.  ').'</small></label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max characters').'</span> '.$this->field('text', 'max_length', $widget).'</label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'unique', $widget).' <span class="label-title">'.ether::langr('Unique value').'</span><small>'.ether::langr('Unique value means duplicate entries in different fields will cause a form to not validate').'.</small></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

/*if ( ! class_exists('ether_form_address_widget'))
{
	class ether_form_address_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-address', ether::langr('Address'));
			$this->label = ether::langr('Address');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].'</span></label><label for="'.$this->get_form_field('address').'">'.ether::langr('Street address').' <input type="text" name="'.$this->get_form_field('address').'" value="'.$this->get_form_value($widget, $input, 'address').'" /></label>
			<label for="'.$this->get_form_field('address2').'">'.ether::langr('Street address (Line #2)').' <input type="text" name="'.$this->get_form_field('address2').'" value="'.$this->get_form_value($widget, $input, 'address2').'" /></label>
			<label for="'.$this->get_form_field('city').'">'.ether::langr('City').' <input type="text" name="'.$this->get_form_field('city').'" value="'.$this->get_form_value($widget, $input, 'city').'" /></label>
			<label for="'.$this->get_form_field('state').'">'.ether::langr('State / Province / Region').' <input type="text" name="'.$this->get_form_field('state').'" value="'.$this->get_form_value($widget, $input, 'state').'" /></label>
			<label for="'.$this->get_form_field('zip').'">'.ether::langr('Zip / Postal code').' <input type="text" name="'.$this->get_form_field('zip').'" value="'.$this->get_form_value($widget, $input, 'zip').'" /></label>
			<label for="'.$this->get_form_field('country').'">'.ether::langr('Country').' '.$this->form_field('select', 'country', $this->get_form_value($widget, $input, 'country'), array('options' => ether_forms_countries())).$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';

		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Address')
				),
			$widget);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max characters').'</span> '.$this->field('text', 'max_length', $widget).'</label>
						</div>
						<div class="col">
							<label>'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>

				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}*/

if ( ! class_exists('ether_form_time_widget'))
{
	class ether_form_time_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-time', ether::langr('Time'));
			$this->label = ether::langr('Time');
		}

		public static function admin_format($value)
		{
			$tails = array
			(
				'am' => ether::langr('AM'),
				'pm' => ether::langr('PM')
			);

			if (empty($value) OR ! is_array($value))
			{
				return '';
			}

			return $value['hour'].':'.$value['minute'].' '.$tails[$value['tail']];
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$hour = array();
			$minute = array();

			for ($i = 1; $i <= 12; $i++)
			{
				$hour[($i < 10 ? '0' : '').$i] = ($i < 10 ? '0' : '').$i;
			}

			for ($i = 0; $i < 60; $i++)
			{
				$minute[($i < 10 ? '0' : '').$i] = ($i < 10 ? '0' : '').$i;
			}

			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].'</span></label><div class="'.ether::config('form_widget_prefix').'inline-labels">
			'.$this->form_field('select', 'hour', $this->get_form_value($widget, $input, 'hour'), array('options' => $hour)).':
			'.$this->form_field('select', 'minute', $this->get_form_value($widget, $input, 'minute'), array('options' => $minute)).'
			'.$this->form_field('select', 'tail', $this->get_form_value($widget, $input, 'tail'), array('options' => array('am' => ether::langr('AM'), 'pm' => ether::langr('PM')))).
			$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label></div>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Time')
				),
			$widget);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_hidden_input_widget'))
{
	class ether_form_hidden_input_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-hidden-input', ether::langr('Hidden input'));
			$this->label = ether::langr('Hidden input');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<input type="hidden" name="'.$this->get_form_field().'" value="'.$widget['value'].'" />';
		}

		public function form($widget)
		{
			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Field value').'</span> '.$this->field('text', 'value', $widget).'</label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_checkbox_widget'))
{
	class ether_form_checkbox_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-checkbox', ether::langr('Checkbox'));
			$this->label = ether::langr('Checkbox');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><input type="checkbox" name="'.$this->get_form_field().'"'.($this->get_form_value($widget, $input) == 'on' ? ' checked="checked"' : '').' /><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span>'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public function form($widget)
		{
			$this->error_messages = array('required');

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_group_widget'))
{
	class ether_form_group_widget extends ether_form_widget
	{
		public function group_item($widget, $i)
		{
			return '<div class="col"'.(empty($widget) ? ' style="display: none;"' : '').'>
				<div class="group-item">
					<div class="group-item-title">'.ether::langr('Item').'</div>
					<div class="group-item-content">
						<label><span class="label-title">'.ether::langr('Option label').'</span> '.$this->group_field('text', 'options_label', $i, $widget).'</label>
						<label><span class="label-title">'.ether::langr('Option value').'</span> '.$this->group_field('text', 'options_value', $i, $widget).'<small>'.ether::langr('Same as label if left blank').'</small></label>
					</div>
					<div class="group-item-actions">
						<button name="builder-widget-tab-remove" class="builder-widget-group-item-remove">'.ether::langr('Remove').'</button>
					</div>
				</div>
			</div>';
		}

		public function form_options($widget)
		{
			$widget = ether::extend(array
			(
				'preset' => ether::langr('Option 1')._n.ether::langr('Option 2')._n.ether::langr('Option 3')
			), $widget);
			$option_presets = array
			(
				'custom' => ether::langr('Custom'),
				'countries' => ether::langr('Countries'),
				'us_states' => ether::langr('US States'),
				'continents' => ether::langr('Continents'),
				'days' => ether::langr('Days'),
				'months' => ether::langr('Months')
			);

			$option_presets = apply_filters('ether_form_preset_options', $option_presets);

			$options = '';

			if (isset($widget['options_label']))
			{
				$column = 0;

				for ($i = 0; $i < count($widget['options_label']); $i++)
				{
					if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
					{
						$options .= $this->group_item($widget, $i);
					}
				}
			}

			$presets = $option_presets;
			unset($presets['custom']);

			$this->error_messages = array();

			/* old options

			<div class="buttonset-1">
				<button name="builder-widget-group-item-add" class="builder-button-classic alignright builder-widget-group-item-add">'.ether::langr('Add option').'</button>
			</div>
			<div class="group-prototype">'.$this->group_item(array(), -1).'</div>
			<div class="group-content">
				<div class="cols-3 cols">
					'.$options.'
				</div>
			</div>
			<div class="buttonset-1">
				<button name="builder-widget-group-item-add" class="builder-button-classic alignright builder-widget-group-item-add">'.ether::langr('Add option').'</button>
			</div>*/

			return '<h2 class="ether-tab-title">'.ether::langr('Add options').'</h2>
			<div class="ether-tab-content">
				<label><span class="label-title">'.ether::langr('Options Presets').'</span> '.$this->field('select', 'options_preset', $widget, array('options' => $option_presets)).'<small>'.ether::langr('One option per line').'</small></label>
				<p></p>
				<div class="ether-form sortable-content group-content-wrap">
					<label>'.$this->field('textarea', 'preset', $widget).'</label>
				</div>
			</div>';
		}
	}
}

if ( ! class_exists('ether_form_radio_widget'))
{
	class ether_form_radio_widget extends ether_form_group_widget
	{
		public function __construct()
		{
			parent::__construct('form-radio', ether::langr('Radio buttons'));
			$this->label = ether::langr('Group of multiple radio buttons');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$output = '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span>'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';

			if ( ! empty($widget['options_preset']) OR $widget['options_preset'] != 'custom')
			{
				$widget['options_value'] = array();
				$widget['options_label'] = explode("\n", $widget['preset']);
			}

			if (isset($widget['options_label']) AND is_array($widget['options_label']))
			{
				$elem_count = count($widget['options_label']);

				if ($widget['options_layout'] !== 'inline')
				{
					$col_count = $widget['options_layout'];
					$cols = array();

					for ($i = 0; $i < $col_count; $i++)
					{
						$cols[$i] = array();
					}

					$output .= $col_count > 1 ? '<div class="'.ether::config('form_widget_prefix').'cols '.ether::config('form_widget_prefix').'cols-'.$col_count.'">' : '';

					$col_id = 0;

					for ($i = 0; $i < $elem_count; $i++)
					{
						if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
						{
							$cols[$col_id % $col_count][$widget['options_label'][$i]] = isset($widget['options_value'][$i]) ? $widget['options_value'][$i] : $widget['options_label'][$i];
							$col_id++;
						}
					}

					for ($i = 0; $i < $col_count; $i++)
					{
						$output .= $col_count > 1 ? '	<div class="'.ether::config('form_widget_prefix').'col">' : '';

						foreach($cols[$i] as $label => $value)
						{
							$value = (empty($value) ? $label : $value);
							$value = trim($value);

							$checked = FALSE;

							if (isset($input[$this->get_id()]))
							{
								if ($input[$this->get_id()] == $value)
								{
									$checked = TRUE;
								}
							}

							$output .= '<label for="'.$this->get_form_field().'"><input type="radio" name="'.$this->get_form_field().'" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /><span class="'.ether::config('form_widget_prefix').'label-title">'.$label.'</span></label>';
						}

						$output .= $col_count > 1 ? '	</div>' : '';
					}

					$output .= $col_count > 1 ? '</div>' : '';
				} else
				{
					$output .= '<div class="'.ether::config('form_widget_prefix').'inline-labels">';

					for ($i = 0; $i < $elem_count; $i++)
					{
						if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
						{
							$value = (empty($widget['options_value'][$i]) ? $widget['options_label'][$i] : $widget['options_value'][$i]);
							$value = trim($value);

							$checked = FALSE;

							if (isset($input[$this->get_id()]))
							{
								if ($input[$this->get_id()] == $value)
								{
									$checked = TRUE;
								}
							}

							$output .= '<label for="'.$this->get_form_field().'"><input type="radio" name="'.$this->get_form_field().'" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['options_label'][$i].'</span></label>';
						}
					}

					$output .= '</div>';
				}
			}

			return $output;
		}

		public function form($widget)
		{
			$options_layout = array(
				'1' => ether::langr('1 Column (Default)'),
				'2' => ether::langr('2 Columns'),
				'3' => ether::langr('3 Columns'),
				'4' => ether::langr('4 Columns'),
				'5' => ether::langr('5 Columns'),
				'6' => ether::langr('6 Columns'),
				'8' => ether::langr('8 Columns'),
				'10' => ether::langr('10 Columns'),
				'inline' => ether::langr('Inline')
			);

			$this->error_messages = array('required');

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
					<label><span class="label-title">'.ether::langr('Options Layout').'</span> '.$this->field('select', 'options_layout', $widget, array('options' => $options_layout)).'</label>
				</div>
				'.$this->form_options($widget).'
				'.$this->form_conditional($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_checkbox_group_widget'))
{
	class ether_form_checkbox_group_widget extends ether_form_group_widget
	{
		public function __construct()
		{
			parent::__construct('form-checkbox-group', ether::langr('Checkbox Group'));
			$this->label = ether::langr('Group of checkboxes');
		}

		public static function admin_format($value)
		{
			if (empty($value) OR ! is_array($value))
			{
				return '';
			}

			return implode(', ', $value);
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$output = '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span>'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';

			if ( ! empty($widget['options_preset']) OR $widget['options_preset'] != 'custom')
			{
				$widget['options_value'] = array();
				$widget['options_label'] = explode("\n", $widget['preset']);
			}

			if (isset($widget['options_label']) AND is_array($widget['options_label']))
			{
				$elem_count = count($widget['options_label']);

				if ($widget['options_layout'] !== 'inline')
				{
					$col_count = $widget['options_layout'];
					$cols = array();

					for ($i = 0; $i < $col_count; $i++)
					{
						$cols[$i] = array();
					}

					$output .= $col_count > 1 ? '<div class="'.ether::config('form_widget_prefix').'cols '.ether::config('form_widget_prefix').'cols-'.$col_count.'">' : '';

					$col_id = 0;

					for ($i = 0; $i < $elem_count; $i++)
					{
						if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
						{
							$cols[$col_id % $col_count][$widget['options_label'][$i]] = isset($widget['options_value'][$i]) ? $widget['options_value'][$i] : $widget['options_label'][$i];
							$col_id++;
						}
					}

					for ($i = 0; $i < $col_count; $i++)
					{
						$output .= $col_count > 1 ? '	<div class="'.ether::config('form_widget_prefix').'col">' : '';

						foreach($cols[$i] as $label => $value)
						{
							$value = (empty($value) ? $label : $value);
							$value = trim($value);

							$checked = FALSE;

							if (isset($input[$this->get_id()]))
							{
								foreach ($input[$this->get_id()] as $checkbox_value)
								{
									$checkbox_value = trim($checkbox_value);

									if ($checkbox_value == $value)
									{
										$checked = TRUE;
										break;
									}
								}
							}

							$output .= '<label for="'.$this->get_form_field().'"><input type="checkbox" name="'.$this->get_form_field().'[]" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /><span class="'.ether::config('form_widget_prefix').'label-title">'.$label.'</span></label>';
						}

						$output .= $col_count > 1 ? '	</div>' : '';
					}

					$output .= $col_count > 1 ? '</div>' : '';
				} else
				{
					$output .= '<div class="'.ether::config('form_widget_prefix').'inline-labels">';

					for ($i = 0; $i < $elem_count; $i++)
					{
						if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
						{
							$value = (empty($widget['options_value'][$i]) ? $widget['options_label'][$i] : $widget['options_value'][$i]);
							$value = trim($value);

							$checked = FALSE;

							if (isset($input[$this->get_id()]))
							{
								foreach ($input[$this->get_id()] as $checkbox_value)
								{
									$checkbox_value = trim($checkbox_value);

									if ($checkbox_value == $value)
									{
										$checked = TRUE;
										break;
									}
								}
							}

							$output .= '<label for="'.$this->get_form_field().'"><input type="checkbox" name="'.$this->get_form_field().'" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['options_label'][$i].'</span></label>';
						}
					}

					$output .= '</div>';
				}
			}

			return $output;
		}

		public function form($widget)
		{
			$options_layout = array(
				'1' => ether::langr('1 Column (Default)'),
				'2' => ether::langr('2 Columns'),
				'3' => ether::langr('3 Columns'),
				'4' => ether::langr('4 Columns'),
				'5' => ether::langr('5 Columns'),
				'6' => ether::langr('6 Columns'),
				'8' => ether::langr('8 Columns'),
				'10' => ether::langr('10 Columns'),
				'inline' => ether::langr('Inline')
			);

			$this->error_messages = array('required');

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
					<label><span class="label-title">'.ether::langr('Options Layout').'</span> '.$this->field('select', 'options_layout', $widget, array('options' => $options_layout)).'</label>
				</div>
				'.$this->form_options($widget).'
				'.$this->form_conditional($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_select_widget'))
{
	class ether_form_select_widget extends ether_form_group_widget
	{
		public function __construct()
		{
			parent::__construct('form-select', ether::langr('Select'));
			$this->label = ether::langr('Select input');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$output = '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <select name="'.$this->get_form_field().'">';

			if ( ! empty($widget['options_preset']) OR $widget['options_preset'] != 'custom')
			{
				$widget['options_value'] = array();
				$widget['options_label'] = explode("\n", $widget['preset']);
			}

			if (isset($widget['options_label']) AND is_array($widget['options_label']))
			{
				for ($i = 0; $i < count($widget['options_label']); $i++)
				{
					if ( ! empty($widget['options_label'][$i]) OR ! empty($widget['options_value'][$i]))
					{
						$val = empty($widget['options_value'][$i]) ? $widget['options_label'][$i] : $widget['options_value'][$i];
						$sel = $this->get_form_value($widget, $input);

						$output .= '<option value="'.trim($val).'"'.(trim($val) == trim($sel) ? ' selected="selected"' : '').'>'.$widget['options_label'][$i].'</option>';
					}
				}
			}

			$output .= '</select>'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';

			return $output;
		}

		public function form($widget)
		{
			$this->error_messages = array('required');

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_options($widget).'
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_textarea_widget'))
{
	class ether_form_textarea_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-textarea', ether::langr('Textarea'));
			$this->label = ether::langr('Textarea');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].((isset($widget['required']) AND $widget['required'] == 'on') ? ' <abbr title="required">*</abbr>': '').'</span> <textarea name="'.$this->get_form_field().'">'.$this->get_form_value($widget, $input).'</textarea>'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
		}

		public function form($widget)
		{
			$this->error_messages = array('required', 'max_length');

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max characters').'</span> '.$this->field('text', 'max_length', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'required', $widget).' <span class="label-title">'.ether::langr('Required').'</span></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				'.$this->form_errors($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_recaptcha_widget'))
{
	class ether_form_recaptcha_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-recaptcha', ether::langr('ReCAPTCHA'));
			$this->label = ether::langr('ReCAPTCHA');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$private_key = ether::option('recaptcha_private_key');
			$public_key = ether::option('recaptcha_public_key');

			if ( ! empty($private_key) AND ! empty($public_key))
			{
				return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].recaptcha_get_html($public_key).'</span><input type="hidden" name="'.$this->get_form_field().'" value="'.$this->get_form_value($widget, $input).'" />'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label>';
			}

			return '<div'.$this->_class(array('widget', 'msg', 'msg-error')).'><span'.$this->_class('msg-icon').'></span><p>'.ether::langr('In order to use ReCAPTCHA widget you have to %sprovide private and public keys%s', '<a href="admin.php?page=ether-ether-forms">', '</a>').' in Ether > Forms section of admin panel</div>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'recaptcha' => 'on'
				),
			$widget);

			$private_key = ether::option('recaptcha_private_key');
			$public_key = ether::option('recaptcha_public_Key');

			$this->error_messages = array();

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>

				<div class="ether-tab-content">
					'.((empty($private_key) OR empty($public_key)) ? '<p class="ether-error">'.ether::langr('In order to use ReCAPTCHA widget you have to %sprovide private and public keys%s', '<a href="admin.php?page=ether-ether-forms">', '</a>').' in Ether > Forms section of the admin panel.</p>' : '').'
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'</label>
					'.$this->field('hidden', 'recaptcha', $widget).'
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_date_widget'))
{
	class ether_form_date_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('form-date', ether::langr('Date'));
			$this->label = ether::langr('Date');
		}

		public static function admin_format($value)
		{
			if (empty($value) OR ! is_array($value))
			{
				return '';
			}

			return implode('.', $value);
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Date'),
					'min_year' => intval(date('Y')) - 100,
					'max_year' => intval(date('Y'))
				),
			$widget);

			$day = array();
			$month = array();
			$year = array();

			for ($i = 1; $i <= 31; $i++)
			{
				$day[$i] = $i;
			}

			$months = ether_forms_months();

			foreach ($months as $i => $m)
			{
				$month[$i + 1] = $m;
			}

			if (empty($widget['min_year']))
			{
				$widget['min_year'] = intval(date('Y')) - 100;
			}

			if (empty($widget['max_year']))
			{
				$widget['max_year'] = intval(date('Y'));
			}

			if ($widget['min_year'] > $widget['max_year'])
			{
				$tmp = $widget['min_year'];
				$widget['min_year'] = $widget['max_year'];
				$widget['max_year'] = $tmp;
			}

			for ($i = intval($widget['max_year']); $i >= intval($widget['min_year']); $i--)
			{
				$year[$i] = $i;
			}

			return '<label for="'.$this->get_form_field().'"><span class="'.ether::config('form_widget_prefix').'label-title">'.$widget['label'].'</span></label><div class="'.ether::config('form_widget_prefix').'inline-labels">
			'.$this->form_field('select', 'day', $this->get_form_value($widget, $input, 'day'), array('options' => $day)).'
			'.$this->form_field('select', 'month', $this->get_form_value($widget, $input, 'month'), array('options' => $month)).'
			'.$this->form_field('select', 'year', $this->get_form_value($widget, $input, 'year'), array('options' => $year)).'
			'.$this->get_form_error($widget, $errors).(isset($widget['description']) ? '<small>'.$widget['description'].'</small>' : '').'</label></div>';
		}

		public function form($widget)
		{
			$widget = ether::extend
			(
				array
				(
					'label' => ether::langr('Date')
				),
			$widget);

			$this->error_messages = array();

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-2d4-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Field label').'</span> '.$this->field('text', 'label', $widget).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Min year range').'</span> '.$this->field('text', 'min_year', $widget).'<small>'.ether::langr('Default value is %s', intval(date('Y')) - 100).'.</small></label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Max year range').'</span> '.$this->field('text', 'max_year', $widget).'<small>'.ether::langr('Default value is %s', date('Y')).'.</small></label>
						</div>
					</div>
					<label><span class="label-title">'.ether::langr('Description').'</span> '.$this->field('textarea', 'description', $widget).'<small>'.ether::langr('Field description can provide some clues for a user filling in a specific input').'.</small></label>
				</div>
				'.$this->form_conditional($widget).'
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Admin field label').'</span> '.$this->field('text', 'admin_label', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_plain_text_widget'))
{
	class ether_form_plain_text_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('plain-text', ether::langr('Plain text'));
			$this->label = ether::langr('Simple plain text widget');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'align' => 'left', //old for text_align
				'text_align' => 'left',
				'width' => '',
				'disable_formatting' => ''
			), $widget);

			$text = isset($widget['text']) ? $widget['text'] : '';

			if ($widget['disable_formatting'] != 'on')
			{
				$text = wpautop($text);
			}

			$classes = array();

			if ($widget['text_align'] != 'left')
			{
				$classes[] = 'text-align'.$widget['align'];
			}

			if ( ! empty($widget['align']))
			{
				$classes[] = 'align'.$widget['align'];
			}

			if ( ! empty($widget['width']))
			{
				$widget['width'] = ether::unit($widget['width'], 'px');
			}

			$text = '<div'.$this->_class($classes).' style="'.( ! empty($widget['width']) ? $widget['width'] : '').'">'.$text.'</div>';

			return $text;
		}

		public function form($widget)
		{
			$text_align = array('left' => ether::langr('Left'), 'right' => ether::langr('Right'), 'center' => ether::langr('Center'));

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
				'.$this->form_widget_general($widget).'
				<label><span class="label-title">'.ether::langr('Text Align').'</span> '.$this->field('select', 'text_align', $widget, array('options' => $text_align)).'</label>
				<label>'.$this->field('checkbox', 'disable_formatting', $widget).' <span class="label-title">'.ether::langr('Disable formatting').'</span></label>
				<label><span class="label-title">'.ether::langr('Plain text').'</span> '.$this->field('textarea', 'text', $widget).'<small>'.ether::langr('Plain text, shortcodes. Default wordpress formatting will be applied.').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_code_widget'))
{
	class ether_form_code_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('code', ether::langr('Syntax Highlighter'));
			$this->label = ether::langr('Syntax highlighter');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			ether::stylesheet('shCoreDefault', 'media/stylesheets/libs/sh/shCoreDefault.css');
			ether::script('shCore', 'media/scripts/libs/sh/shCore.js');
			ether::script('shAutoloader', 'media/scripts/libs/sh/shAutoloader.js', array('shCore'));

			$code = isset($widget['code']) ? $widget['code'] : '';

			return '<pre class="brush: '.$widget['type'].';">'.htmlspecialchars($code).'</pre>';
		}

		public function form($widget)
		{
			$types = array
			(
				'as3' => ether::langr('AS3'),
				'applescript' => ether::langr('Apple Script'),
				'bash' => ether::langr('Bash'),
				'csharp' => ether::langr('C#'),
				'coldfusion' => ether::langr('Cold Fusion'),
				'cpp' => ether::langr('C++'),
				'css' => ether::langr('CSS'),
				'delphi' => ether::langr('Delphi'),
				'diff' => ether::langr('Diff'),
				'erlang' => ether::langr('Erlang'),
				'groovy' => ether::langr('Groovy'),
				'javascript' => ether::langr('Java Script'),
				'java' => ether::langr('Java'),
				'javafx' => ether::langr('JavaFX'),
				'perl' => ether::langr('Perl'),
				'php' => ether::langr('PHP'),
				'plain' => ether::langr('Plain'),
				'powershell' => ether::langr('Power Shell'),
				'python' => ether::langr('Python'),
				'ruby' => ether::langr('Ruby'),
				'sass' => ether::langr('SASS'),
				'scala' => ether::langr('Scala'),
				'sql' => ether::langr('SQL'),
				'vb' => ether::langr('Visual Basic'),
				'xml' => ether::langr('XML')
			);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Code type').'</span> '.$this->field('select', 'type', $widget, array('options' => $types)).'</label>
					<label><span class="label-title">'.ether::langr('Code').'</span> '.$this->field('textarea', 'code', $widget).'</label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_rich_text_widget'))
{
	class ether_form_rich_text_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('rich-text', ether::langr('Rich text'));
			$this->label = ether::langr('Advance text editor powered by TinyMCE');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return wpautop($widget['text']);
		}

		public function form($widget)
		{
			ob_start();
			media_buttons($this->get_field_name('text'));
			$mediabuttons = ob_get_clean();

			return '<fieldset class="ether-form">
				'.( ! user_can_richedit() ? '<p class="ether-error">'.ether::langr('Rich text editor has been disabled. Check your account settings.').'</p>' : '').'
				<div class="wp-editor-tools">
					<!--<a id="content-html" class="hide-if-no-js wp-switch-editor switch-html" onclick="switchEditors.switchto(this);">'.ether::langr('HTML').'</a>
					<a id="content-tmce" class="hide-if-no-js wp-switch-editor switch-tmce" onclick="switchEditors.switchto(this);">'.ether::langr('Visual').'</a>-->
					'.$mediabuttons.'
				</div>
				<div class="wp-editor-wrap">
					<div class="wp-editor-container">
						<textarea'.$this->get_field_atts('text').' name="'.$this->get_field_name('text').'" id="'.$this->get_field_name('text').'" cols="15" class="tinymce">'.(isset($widget['text']) ? wpautop($widget['text']) : '').'</textarea>
					</div>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_html_widget'))
{
	class ether_form_html_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('html', ether::langr('HTML'));
			$this->label = ether::langr('Simple HTML widget');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return (isset($widget['html']) ? $widget['html'] : '');
		}

		public function form($widget)
		{
			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('HTML code').'</span> '.$this->field('textarea', 'html', $widget).'<small>'.ether::langr('HTML code, shortcodes. No code formatting.').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_heading_widget'))
{
	class ether_form_heading_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('heading', ether::langr('Heading'));
			$this->label = ether::langr('Simple heading widget');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'type' => 'h2',
				'title' => '',
				'classes' => ''
			), $widget);

			if ( ! empty($widget['title']))
			{
				$title = $widget['title'];
			} else
			{
				$title = get_the_title(ether::get_id());

				ether::config('hide_title', TRUE);
			}

			return '<'.$widget['type'].$this->_class(array(), $widget['classes']).((isset($widget['id']) AND ! empty($widget['id'])) ? ' id="'.$widget['id'].'"' : '').'>'.$widget['title'].'</'.$widget['type'].'>';
		}

		public function form($widget)
		{
			$types = array
			(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6'
			);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Title').'</span> '.$this->field('text', 'title', $widget).'<small>('.ether::langr('If you leave this field blank, post title will be used').')</small></label>
					<label><span class="label-title">'.ether::langr('Type').'</span> '.$this->field('select', 'type', $widget, array('options' => $types)).'</label>
					<label><span class="label-title">'.ether::langr('ID').'</span> '.$this->field('text', 'id', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_image_widget'))
{
	class ether_form_image_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('image', ether::langr('Image'));
			$this->label = ether::langr('Simple Image widget');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'align' => '',
				'frame' => '',
				'show_img_title' => '',
				'use_lightbox' => '',
				'url' => '',
				'image' => '',
				'description' => '',
				'classes' => ''
			), $widget);

			$classes = array('widget', 'img');

			if ( ! empty($widget['align']))
			{
				$classes[] = 'align'.$widget['align'];
			}

			preg_match('/(\d*)(.*)/', $widget['image_width'], $width_unit);
			$width_unit = $width_unit[2] === '' ? 'px' : $width_unit[2];

			preg_match('/(\d*)(.*)/', $widget['image_height'], $height_unit);
			$height_unit = $height_unit[2] === '' ? 'px' : $height_unit[2];

			$widget['image_width'] = intval($widget['image_width']);
			$widget['image_height'] = intval($widget['image_height']);
			$widget['image_crop_width'] = intval($widget['image_crop_width']);
			$widget['image_crop_height'] = intval($widget['image_crop_height']);

			if ($widget['image_crop_width'] > 0 OR $widget['image_crop_height'] > 0)
			{
				$widget['image'] = ether::get_image_thumbnail(ether::get_image_base($widget['image']), $widget['image_crop_width'], $widget['image_crop_height']);
			}

			if ( ! empty($widget['frame']))
			{
				$classes[] = 'frame';
				$classes[] = 'frame-'.$widget['frame'];
			}

			if ($widget['show_img_title'] == 'on')
			{
				$classes[] = 'show-img-title';
			}

			if ($widget['use_lightbox'] == 'on')
			{
				if (empty($widget['url']))
				{
					$widget['url'] = $widget['image'];
				}
			}

			$output = '';

			if ( ! empty($widget['url']))
			{
				$output .= '<a href="'.$widget['url'].'"'.$this->_class($classes, $widget['classes']);
				$output .= ($widget['use_lightbox'] == 'on' ? ' rel="lightbox"' : '');
				$output .= ' style="'.($widget['image_width'] > 0 ? 'width: '.$widget['image_width'].$width_unit.';' : '').' '.($widget['image_height'] > 0 ? 'height: '.$widget['image_height'].$height_unit.';' : '');
				$output .= '">';
			}

			$output .= '<img src="'.( ! empty($widget['image']) ? ether::img($widget['image'], 'image') : '').'" alt="'.( ! empty($widget['description']) ? $widget['description'] : '').'"'.(empty($widget['url']) ? $this->_class($classes, $widget['classes']) : '').($widget['image_width'] > 0 && $width_unit == 'px' ? ' width="'.$widget['image_width'].'"' : '').($widget['image_height'] > 0 && $height_unit == 'px' ? ' height="'.$widget['image_height'].'"' : '').' style="'.((isset($widget['url']) AND empty($widget['url'])) ? ($widget['image_width'] > 0 && $width_unit != 'px' ? 'width: '.$widget['image_width'].$width_unit : '').' '.(($widget['image_height'] && $height_unit != 'px') ? 'height: '.$widget['image_height'].$height_unit : '') : '').'" />';

			if ( ! empty($widget['url']))
			{
				$output .= '</a>';
			}

			return $output;
		}

		public function form($widget)
		{
			$aligns = array
			(
				'' => ether::langr('Default'),
				'left' => ether::langr('Left'),
				'right' => ether::langr('Right'),
				'center' => ether::langr('Center')
			);

			$frames = apply_filters('ether_image_frames', array
			(
				'' => ether::langr('Theme default'),
				'1' => ether::langr('Ether frame 1'),
				'2' => ether::langr('Ether frame 2'),
				'reset' => ether::langr('Reset')
			));

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Align').'</span> '.$this->field('select', 'align', $widget, array('options' => $aligns)).'</label>
					<label><span class="label-title">'.ether::langr('Frame Style').'</span> '.$this->field('select', 'frame', $widget, array('options' => $frames)).'</label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Choose Image').'</h2>
				<div class="ether-tab-content">
					<div class="cols-2">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Image').' <abbr title="required">*</abbr></span> '.$this->field('text', 'image', $widget, array('class' => 'ether-preview upload_image')).'</label>
							<div class="buttonset-1">
								<button type="submit"'.$this->get_field_atts('upload_image').' name="'.$this->get_field_name('upload_image').'" class="builder-button-classic alignright upload_image single callback-builder_image_widget_change">'.ether::langr('Choose Image').'</button>
							</div>
						</div>
						<div class="col">
							<div class="preview-img-wrap">
								<img src="'.((isset($widget['image']) AND ! empty($widget['image'])) ? $widget['image'] : ether::path('media/images/placeholder.png', TRUE)).'" alt="" class="upload_image" />
							</div>
						</div>
					</div>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Image Settings').'</h2>
				<div class="ether-tab-content">
					<div class="cols-2">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Image Title').' <abbr title="required">*</abbr></span> '.$this->field('text', 'description', $widget).'</label>
						</div>
						<div class="col">
							<label class="label-alt-1">'.$this->field('checkbox', 'show_img_title', $widget).' <span class="label-title">'.ether::langr('Display title bar on hover').'</span></label>
						</div>
					</div>
					<div class="cols-2">
						<div class="col"><label><span class="label-title">'.ether::langr('Link URL').'</span> '.$this->field('text', 'url', $widget).'</label></div>
						<div class="col"><label class="label-alt-1">'.$this->field('checkbox', 'use_lightbox', $widget).' <span class="label-title">'.ether::langr('Open "Link URL" in lightbox').'</span></label></div>
					</div>
					'.$this->form_image_dimensions($widget).'
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_divider_widget'))
{
	class ether_form_divider_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('divider', ether::langr('Divider'));
			$this->label = ether::langr('Horizontal bar for dividing separate sections of the page.');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'back_to_top' => '',
				'clear' => ''
			), $widget);

			$back_to_top = array
			(
				'alignment' => array
				(
					'left' => ether::langr('Left'),
					'right' => ether::langr('Right'),
					'center' => ether::langr('Center')
				),
				'title' => array
				(
					'0' => ether::langr('Back to top'),
					'1' => ether::langr('^ top'),
					'2' => ether::langr('&uarr; top'),
					'3' => ether::langr('Custom')
				)
			);

			$classes = array('divider', 'style-1');

			if ($widget['clear'] == 'on')
			{
				$classes[] = 'clear';
			}

			if ($widget['back_to_top'] == 'on')
			{
				$classes[] = 'clear';
				$classes[] = 'title-align'.$widget['back_to_top_alignment'];

				if ($widget['back_to_top_title'] == '3')
				{
					$back_to_top_title = $widget['back_to_top_custom_title'];
				} else
				{
					$back_to_top_title = $back_to_top['title'][$widget['back_to_top_title']];
				}
			}

			$output = '';

			if ($widget['back_to_top'] == 'on')
			{
				$href = '';

				if (isset($widget['back_to_top_custom_link']) AND ! empty($widget['back_to_top_custom_link']))
				{
					if (substr($widget['back_to_top_custom_link'], 0, 4) == 'http')
					{
						$href = $widget['back_to_top_custom_link'];
					} else
					{
						$href = '#'.$widget['back_to_top_custom_link'];
					}
				}
				$output.= '<a href="'.( ! empty($href) ? $href : '#page').'"'.$this->_class($classes).'><hr /><span'.$this->_class('back-to-top').'>'.$back_to_top_title.'</span></a>';
			} else
			{
				$output.= '<hr'.$this->_class($classes).'" />';
			}

			return $output;
		}

		public function form($widget)
		{
			$back_to_top = array
			(
				'alignment' => array
				(
					'left' => ether::langr('Left'),
					'right' => ether::langr('Right'),
					'center' => ether::langr('Center')
				),
				'title' => array
				(
					'0' => ether::langr('Back to top'),
					'1' => ether::langr('^ top'),
					'2' => ether::langr('&uarr; top'),
					'3' => ether::langr('Custom')
				)
			);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label>'.$this->field('checkbox', 'clear', $widget).'<span class="label-title"> '.ether::langr('Clear divider').'</span> <small>'.ether::langr('Will force divider (and any content that follows) to appear under any left/right aligned object rather than next to it').'</small></label>
					<label>'.$this->field('checkbox', 'back_to_top', $widget, array('class' => 'ether-cond ether-group-1')).' <span class="label-title">'.ether::langr('Include back to top link').'</span></label>
					<div class="cols cols-2 ether-cond-on ether-group-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Back to top link alignment').'</span> '.$this->field('select', 'back_to_top_alignment', $widget, array('options' => $back_to_top['alignment'])).'</label>
						</div>
						<div class="col">
							<label><span class="label-title">'.ether::langr('Back to top link title').'</span> '.$this->field('select', 'back_to_top_title', $widget, array('options' => $back_to_top['title'], 'class' => 'ether-cond ether-group-2')).'</label>
							<label class="ether-cond-3 ether-group-2"><span class="label-title">'.ether::langr('Custom title').'</span> '.$this->field('text', 'back_to_top_custom_title', $widget).'</label>
							<label class="ether-cond-3 ether-group-2"><span class="label-title">'.ether::langr('Custom link').'</span> '.$this->field('text', 'back_to_top_custom_link', $widget).'</label>
						</div>
					</div>
				</div>

				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_message_widget'))
{
	class ether_form_message_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('message', ether::langr('Message'));
			$this->label = ether::langr('6 message box types for special notifications');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'align' => '',
				'width' => '',
				'type' => 'info',
				'classes' => ''
			), $widget);

			$classes = array('widget', 'msg', 'msg-'.$widget['type']);

			if ( ! empty($widget['width']))
			{
				$widget['width'] = ether::unit($widget['width'], 'px');
			}

			if ( ! empty($widget['align']))
			{
				$classes[] = 'align'.$widget['align'];
			}

			return '<div'.$this->_class($classes, $widget['classes']).'style="'.( ! empty($widget['width']) ? 'width: '.$widget['width'] : '').'"><span'.$this->_class('msg-icon').'></span>'.wpautop($widget['text']).'</div>';
		}

		public function form($widget)
		{
			$types = array
			(
				'info' => ether::langr('Info'),
				'warning' => ether::langr('Warning'),
				'error' => ether::langr('Error'),
				'download' => ether::langr('Download'),
				'important-1' => ether::langr('Important'),
				'important-2' => ether::langr('Important alt')
			);

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					'.$this->form_widget_general($widget).'
					<label><span class="label-title">'.ether::langr('Type').' <abbr title="required">*</abbr></span>'.$this->field('select', 'type', $widget, array('options' => $types)).'</label>
					<label><span class="label-title">'.ether::langr('Message').' <abbr title="required">*</abbr></span>'.$this->field('textarea', 'text', $widget).'<small>'.ether::langr('Plain text, shortcodes. Default wordpress formatting will be applied.').'</small></label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_style_widget'))
{
	class ether_form_style_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('style', ether::langr('Style'));
			$this->label = ether::langr('Add custom styles to your page using CSS.');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '<style type="text/css">'.$widget['css'].'</style>';
		}

		public function form($widget)
		{
			$text_align = array('left' => ether::langr('Left'), 'right' => ether::langr('Right'), 'center' => ether::langr('Center'));

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Plain CSS').'</span> '.$this->field('textarea', 'css', $widget).'</label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_link_widget'))
{
	class ether_form_link_widget extends ether_form_widget
	{
		public function __construct()
		{
			parent::__construct('link', ether::langr('Link'));
			$this->label = ether::langr('Basic link element.');
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'align' => '',
				'classes' => ''
			), $widget);

			$classes = array('widget', 'link');

			if ( ! empty($widget['width']))
			{
				$widget['width'] = ether::unit($widget['width'], 'px');
			}

			if ( ! empty($widget['align']))
			{
				$classes[] = 'align'.$widget['align'];
			}

			return '<a href="'.$widget['url'].'"'.$this->_class($classes, $widget['classes']).'style="'.( ! empty($widget['width']) ? 'width: '.$widget['width'] : '').'">'.$widget['title'].'</a>';
		}

		public function form($widget)
		{
			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('General').'</h2>
				<div class="ether-tab-content">
					'.$this->form_widget_general($widget).'
					<label><span class="label-title">'.ether::langr('Title').' <abbr title="required">*</abbr></span>'.$this->field('text', 'title', $widget).'</label>
					<label><span class="label-title">'.ether::langr('URL').' <abbr title="required">*</abbr></span>'.$this->field('text', 'url', $widget).'</label>
				</div>
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Additional classes').'</span> '.$this->field('text', 'classes', $widget).'</label>
					<label><span class="label-title">'.ether::langr('Widget label').'</span> '.$this->field('text', 'admin-label', $widget).'<small>'.ether::langr('Custom widget label which will be shown instead of widget name in the admin view').'</small></label>
				</div>
			</fieldset>';
		}
	}
}

if ( ! class_exists('ether_form_row_base_widget'))
{
	class ether_form_row_base_widget extends ether_form_widget
	{
		protected $cols;
		protected $col_count;

		public function widget($widget, $input = array(), $errors = array())
		{
			$widget = ether::extend( array
			(
				'classes' => ''
			), $widget);

			$output = _t(5).'<div'.$this->_class(array('cols', 'cols-'.$this->cols), $widget['classes']).'>'._n;

			for ($i = 1; $i <= $this->col_count; $i++)
			{
				$classes_col = '';

				if (isset($widget['classes_col_'.$i]))
				{
					$classes_col = $widget['classes_col_'.$i];
				}

				$output .= _t(5).'	<div'.$this->_class('col', $classes_col).'>'.(isset($widget['col-'.$i]) ? $widget['col-'.$i] : '').'</div>'._n;
			}

			$output .= _t(5).'</div>'._n;

			return $output;
		}

		public function form_after($widget)
		{
			$cols = '<div class="builder-widget-row cols-'.$this->cols.'">';
			$options = '<div class="builder-widget-row-options cols-'.$this->cols.'">';

			for ($i = 1; $i <= $this->col_count; $i++)
			{
				$cols .= '<div class="col builder-widget-column">'.(isset($widget['col-'.$i]) ? $widget['col-'.$i] : '').'</div>';
				$options .= ' <div class="col builder-widget-column-options"><button name="builder-widget-add" class="builder-button-classic builder-widget-add"><span>'.ether::langr('Add widget').'</span></button></div>';
			}

			$cols .= '</div>';
			$options .= '</div>';

			return $cols.$options;
		}

		public function form($widget)
		{
			$output = '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('Misc').'</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Additional classes (row)').'</span> '.$this->field('text', 'classes', $widget).'</label>';

			for ($i = 0; $i < $this->col_count; $i++)
			{
				$output .= '<label><span class="label-title">'.ether::langr('Additional classes (column no. %d)', ($i + 1)).'</span> '.$this->field('text', 'classes_col_'.($i + 1), $widget).'</label>';
			}

			$output .= '
				</div>
			</fieldset>';

			return $output;
		}
	}
}

if ( ! class_exists('ether_form_row1_widget'))
{
	class ether_form_row1_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-1', ether::langr('Column'));
			$this->label = ether::langr('Column. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '1';
			$this->col_count = 1;
		}
	}
}


if ( ! class_exists('ether_form_row2_widget'))
{
	class ether_form_row2_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2', ether::langr('1/2 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2';
			$this->col_count = 2;
		}
	}
}

if ( ! class_exists('ether_form_row3_widget'))
{
	class ether_form_row3_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-3', ether::langr('1/3 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '3';
			$this->col_count = 3;
		}
	}
}

if ( ! class_exists('ether_form_row4_widget'))
{
	class ether_form_row4_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-4', ether::langr('1/4 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '4';
			$this->col_count = 4;
		}
	}
}

if ( ! class_exists('ether_form_row5_widget'))
{
	class ether_form_row5_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-5', ether::langr('1/5 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '5';
			$this->col_count = 5;
		}
	}
}

if ( ! class_exists('ether_form_row6_widget'))
{
	class ether_form_row6_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-6', ether::langr('1/6 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '6';
			$this->col_count = 6;
		}
	}
}

if ( ! class_exists('ether_form_row2d3_1_widget'))
{
	class ether_form_row2d3_1_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2d3-1', ether::langr('2/3 + 1/3 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2d3-1';
			$this->col_count = 2;
		}
	}
}

if ( ! class_exists('ether_form_row2d3_2_widget'))
{
	class ether_form_row2d3_2_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2d3-2', ether::langr('1/3 + 2/3 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2d3-2';
			$this->col_count = 2;
		}
	}
}

if ( ! class_exists('ether_form_row3d4_1_widget'))
{
	class ether_form_row3d4_1_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-3d4-1', ether::langr('3/4 + 1/4 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '3d4-1';
			$this->col_count = 2;
		}
	}
}

if ( ! class_exists('ether_form_row3d4_2_widget'))
{
	class ether_form_row3d4_2_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-3d4-2', ether::langr('1/4 + 3/4 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '3d4-2';
			$this->col_count = 2;
		}
	}
}

if ( ! class_exists('ether_form_row2d4_1_widget'))
{
	class ether_form_row2d4_1_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2d4-1', ether::langr('1/2 + 1/4 + 1/4 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2d4-1';
			$this->col_count = 3;
		}
	}
}

if ( ! class_exists('ether_form_row2d4_2_widget'))
{
	class ether_form_row2d4_2_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2d4-2', ether::langr('1/4 + 1/2 + 1/4 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2d4-2';
			$this->col_count = 3;
		}
	}
}

if ( ! class_exists('ether_form_row2d4_3_widget'))
{
	class ether_form_row2d4_3_widget extends ether_form_row_base_widget
	{
		public function __construct()
		{
			parent::__construct('row-2d4-3', ether::langr('1/4 + 1/4 + 1/2 Columns'));
			$this->label = ether::langr('Columns. Place widgets inside them to create advanced layouts');
			$this->core = TRUE;
			$this->cols = '2d4-3';
			$this->col_count = 3;
		}
	}
}

// CORE WIDGETS, responsible for rows / cols

ether_form::register_widget('ether_form_row1_widget');
ether_form::register_widget('ether_form_row2_widget');
ether_form::register_widget('ether_form_row3_widget');
ether_form::register_widget('ether_form_row4_widget');
ether_form::register_widget('ether_form_row5_widget');
ether_form::register_widget('ether_form_row6_widget');
ether_form::register_widget('ether_form_row2d3_1_widget');
ether_form::register_widget('ether_form_row2d3_2_widget');
ether_form::register_widget('ether_form_row3d4_1_widget');
ether_form::register_widget('ether_form_row3d4_2_widget');
ether_form::register_widget('ether_form_row2d4_1_widget');
ether_form::register_widget('ether_form_row2d4_2_widget');
ether_form::register_widget('ether_form_row2d4_3_widget');

// BUILT IN WIDGETS
ether_form::register_widget('ether_form_text_input_widget');
ether_form::register_widget('ether_form_numeric_widget');
ether_form::register_widget('ether_form_hidden_input_widget');
ether_form::register_widget('ether_form_checkbox_widget');
ether_form::register_widget('ether_form_checkbox_group_widget');
ether_form::register_widget('ether_form_radio_widget');
ether_form::register_widget('ether_form_select_widget');
ether_form::register_widget('ether_form_textarea_widget');
ether_form::register_widget('ether_form_file_widget');
ether_form::register_widget('ether_form_email_widget');
ether_form::register_widget('ether_form_url_widget');
ether_form::register_widget('ether_form_time_widget');
ether_form::register_widget('ether_form_date_widget');
ether_form::register_widget('ether_form_phone_number_widget');
ether_form::register_widget('ether_form_zip_code_widget');
//ether_form::register_widget('ether_form_address_widget');
ether_form::register_widget('ether_form_recaptcha_widget');

// WIDGETS FROM BUILDER

ether_form::register_widget('ether_form_divider_widget');
ether_form::register_widget('ether_form_image_widget');
ether_form::register_widget('ether_form_plain_text_widget');
ether_form::register_widget('ether_form_rich_text_widget');
ether_form::register_widget('ether_form_html_widget');
ether_form::register_widget('ether_form_heading_widget');
ether_form::register_widget('ether_form_message_widget');
ether_form::register_widget('ether_form_style_widget');
ether_form::register_widget('ether_form_link_widget');

?>
