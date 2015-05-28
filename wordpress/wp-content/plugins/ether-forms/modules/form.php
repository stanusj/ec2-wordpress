<?php

require_once('validator.php');
require_once('user_agent.php');
require_once('recaptchalib.php');

if ( ! class_exists('ether_form'))
{
	class ether_form extends ether_module
	{
		protected static $widget_classes = array();
		protected static $widget_data = array();
		protected static $widget_slugs = array();
		protected static $widgets = array();
		protected static $form_id;

		protected static $disabled_fields = array();
		protected static $locations = array();
		protected static $buffer = NULL;

		protected static $generated_content_registry = array();
		protected static $content_output = array();
		protected static $setup_done = FALSE;

		public static function init()
		{
			ether::bind('ether.get', array('ether_form', 'export'));
			self::register_location('main', ether::langr('Main content'));
		}

		public static function widgets_init()
		{
			if(is_admin() && ether_forms_ult_is_post_type('form'))
			{
				wp_enqueue_script('tiny_mce');
				wp_enqueue_style('editor-buttons');
			}

			self::$widget_classes = apply_filters('ether_form_widgets', self::$widget_classes);

			$count = count(self::$widget_classes);

			for ($i = 0; $i < $count; $i++)
			{
				$class = self::$widget_classes[$i];

				if (class_exists($class))
				{
					$object = new $class();

					if ( ! isset(self::$widget_slugs[$object->get_slug()]))
					{
						self::$widgets[] = $object;
						self::$widget_slugs[$object->get_slug()] = $class;
					} else
					{
						unset($obejct);
					}
				}
			}
		}

		public static function sidebar_init()
		{
			register_widget('ether_form_sidebar_widget');
		}

		public static function export($data)
		{
			if (is_user_logged_in() AND current_user_can('administrator') AND isset($data['form-entry-export']))
			{
				$form_id = NULL;

				if (isset($data['form']))
				{
					$form_id = $data['form'];
				} else if (isset($data['form-entry']))
				{
					$p = get_post($data['form-entry']);

					$form_id = $p->post_parent;
				}

				$form_widgets = self::flatten(ether::meta('builder_data', TRUE, $form_id));

				$output = '"id"';

				$fields = array();

				foreach ($form_widgets as $widget_id => $widget_data)
				{
					if ($widget_data['__SLUG__'] == 'form-recaptcha' || substr($widget_data['__SLUG__'], 0, 3) == 'row')
					{
						continue;
					}

					$label = $widget_data['label'];

					if (isset($widget_data['admin_label']) AND ! empty($widget_data['admin_label']))
					{
						$label = $widget_data['admin_label'];
					}

					if (empty($label))
					{
						$label = '[This field has no label]';
					}

					$output .= ',"'.str_replace('"', '\\"', $label).'"';
				}

				$output .= _n;

				$args = array('post_type' => 'form-entry');

				if (isset($data['form-entry']))
				{
					$args['p'] = $data['form-entry'];
				} else
				{
					$args['post_parent'] = $form_id;
				}

				$args['numberposts'] = -1;

				$entries = ether::get_posts($args);

				foreach ($entries as $entry)
				{
					$output .= '"'.$entry['id'].'"';

					foreach ($form_widgets as $widget_id => $widget_data)
					{
						if ($widget_data['__SLUG__'] == 'form-recaptcha' || substr($widget_data['__SLUG__'], 0, 3) == 'row')
						{
							continue;
						}

						$value = ether::meta('field_'.$widget_id, TRUE, $entry['id']);
						$value = call_user_func_array(array(self::get_widget_class($widget_data['__SLUG__']), 'admin_format'), array($value));

						$output .= ',"'.str_replace('"', '\\"', $value).'"';
					}

					$output .= _n;
				}

				header('Content-Type: text/plain');
				header('Content-Length: '.strlen($output));
				header('Content-Disposition: attachment; filename="ether-form-'.time().'.csv"');

				echo $output;

				die;
			}
		}

		public static function header()
		{
			if(is_admin() && ether_forms_ult_is_post_type('form'))
			{
				global $post_type;

				// if not add new post or edit post screen
				if (empty($post_type))
				{
					if (function_exists('add_thickbox'))
					{
						add_thickbox();
					}
				}
			} else
			{
				$style = apply_filters('ether_form_style', stripslashes(ether::option('form_style')));

				if ( ! empty($style))
				{
					echo '<style type="text/css">'.$style.'</style>';
				}
			}
		}

		public static function register_widget($class)
		{
			self::$widget_classes[] = $class;
		}

		public static function register_widget_data($slug, $title)
		{
			$slug = ether::slug($slug);

			self::$widget_data[] = array('slug' => $slug, 'title' => $title);
		}

		public static function get_disabled_fields()
		{
			return apply_filters('ether_form_disabled_fields', self::$disabled_fields);
		}

		public static function disable_field($name)
		{
			if ( ! in_array($name, self::$disabled_fields))
			{
				self::$disabled_fields[] = $name;
			}
		}

		public static function get_widgets()
		{
			return self::$widgets;
		}

		public static function get_widget_class($slug)
		{
			$slug = ether::slug($slug);

			if (isset(self::$widget_slugs[$slug]))
			{
				return self::$widget_slugs[$slug];
			}

			return '';
		}

		public static function get_widget_title($slug)
		{
			$slug = ether::slug($slug);

			foreach (self::$widget_data as $widget_data)
			{
				if ($widget_data['slug'] == $slug)
				{
					return $widget_data['title'];
				}
			}

			return ether::langr('Widget');
		}

		public static function register_location($slug, $name)
		{
			$slug = ether::slug($slug);

			if ( ! isset(self::$locations[$slug]))
			{
				self::$locations[$slug] = $name;

				return TRUE;
			}

			return FALSE;
		}

		public static function get_locations()
		{
			return self::$locations;
		}

		public static function extract_widgets($arr, $skip_fields = array())
		{
			$output = array();

			foreach ($arr as $key => $value)
			{
				if (isset($value['__SLUG__']))
				{
					if ( ! isset($value['__CORE__']))
					{
						if ( ! empty($skip_fields))
						{
							foreach ($skip_fields as $field)
							{
								if (isset($output[$field]))
								{
									unset($output[$field]);
								}
							}
						}

						$output[$key] = $value;
					}
				} else
				{
					$output = array_merge($output, self::extract_widgets($value, $skip_fields));
				}
			}

			return $output;
		}

		public static function flatten($builder, $prefix = '', $parse = TRUE)
		{
			$output = array();

			if (is_array($builder))
			{
				foreach ($builder as $key => $value)
				{
					$entry = ($prefix ? $prefix.'][' : '').$key;

					if (is_array($value))
					{
						$output = array_merge($output, self::flatten($value, $entry, FALSE));
					} else
					{
						$output['['.$entry.']'] = $value;
					}
				}

				if ($parse)
				{
					$builder = $output;
					$output = array();

					foreach ($builder as $k => $v)
					{
						preg_match_all('/\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]/is', $k, $matches);

						if (isset($matches[4][0]) AND ! empty($matches[4][0]) AND isset($matches[5][0]) AND ! empty($matches[5][0]))
						{
							$id = $matches[4][0];
							$field = $matches[5][0];

							if ( ! isset($output[$id]))
							{
								$output[$id] = array();
							}

							$output[$id][$field] = $v;
						}
					}
				}
			}

			return $output;
		}

		public static function is_unique($key, $value)
		{
			global $wpdb;

			$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(pm.meta_id) FROM '.$wpdb->postmeta.' pm LEFT JOIN '.$wpdb->posts.' p ON pm.post_id = p.id WHERE pm.meta_key=%s AND pm.meta_value=%s AND p.post_parent=%d', ether::config('prefix').'field_'.$key, $value, self::$form_id));

			if ($count == 0)
			{
				return TRUE;
			}

			return FALSE;
		}

		public static function is_captcha_valid($key, $value)
		{
			$private_key = ether::option('recaptcha_private_key');

			$response = recaptcha_check_answer($private_key, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

			return $response->is_valid;
		}

		public static function valid($id, &$data, $rules)
		{
			$valid = new ether_validator();

			if (isset($rules['required']) AND $rules['required'] == 'on')
			{
				$valid->rule($id, array('required' => NULL));
			}

			if (isset($rules['unique']) AND $rules['unique'] == 'on')
			{
				$valid->rule($id, array('callback' => array($id, array('ether_form', 'is_unique'))));
			}

			if (isset($rules['recaptcha']) AND $rules['recaptcha'] == 'on')
			{
				$valid->rule($id, array('callback' => array($id, array('ether_form', 'is_captcha_valid'))));
			}

			if (isset($rules['min_length']) AND intval($rules['min_length']) > 0)
			{
				$valid->rule($id, array('min_length' => intval($rules['min_length'])));
			}

			if (isset($rules['max_length']) AND intval($rules['max_length']) > 0)
			{
				$valid->rule($id, array('max_length' => intval($rules['max_length'])));
			}

			if (isset($rules['valid_email']) AND $rules['valid_email'] == 'on')
			{
				$valid->rule($id, array('email' => TRUE));
			}

			if (isset($rules['valid_numeric']) AND $rules['valid_numeric'] == 'on')
			{
				$valid->rule($id, array('numeric' => TRUE));
			}

			if (isset($rules['min']) AND ! empty($rules['min']))
			{
				$valid->rule($id, array('min' => intval($rules['min'])));
			}

			if (isset($rules['max']) AND ! empty($rules['max']))
			{
				$valid->rule($id, array('max' => intval($rules['max'])));
			}

			if (isset($rules['format']) AND ! empty($rules['format']))
			{
				$valid->rule($id, array('format' => $rules['format']));
			}

			$output = $valid->validate($data);

			return $output;
		}

		public static function parse($form_widgets, $location, $admin = FALSE, $data = array(), $id = NULL)
		{
			$form_widgets_output = '';

			$row_begin = FALSE;
			$first_row_data = array();
			$row_id = '__ID__';
			$file_map = array();
			$field_map = array();
			$files = array();
			$input = array();
			$error_count = 0;
			$conditional = array();

			if (isset($_POST['ether_form']))
			{
				$input = $_POST['ether_form'];
			}

			if (isset($_FILES['ether_form']))
			{
				foreach ($_FILES['ether_form']['name'] as $key => $value)
				{
					$file_map[$key] = array('name' => $value, 'type' => $_FILES['ether_form']['type'][$key], 'tmp_name' => $_FILES['ether_form']['tmp_name'][$key], 'error' => $_FILES['ether_form']['error'][$key], 'size' => $_FILES['ether_form']['size'][$key]);
				}
			}

			self::$form_id = $id;

			if ( ! $admin)
			{
				$form_style = ether::meta('form_style', TRUE, $id);

				$form_widgets_output .= wpautop(ether::meta('form_description', TRUE, $id));
				$form_widgets_output .= '<form method="post" class="ether-form'.( ! empty($form_style) ? ' ether-form-'.$form_style : '').'" id="ether-form-'.$id.'" enctype="multipart/form-data" encoding="multipart/form-data"><fieldset>';
			}

			if (isset($form_widgets[$location]) AND is_array($form_widgets[$location]))
			{
				foreach ($form_widgets[$location] as $row_name => $row_data)
				{
					if ($row_name !== '__ROW__')
					{
						$columns = array(array(), array(), array(), array());

						foreach ($row_data as $column_name => $column_data)
						{
							if ($column_name === '__COLUMN__')
							{
								foreach ($column_data as $widget_id => $widget_data)
								{
									if ( ! isset($widget_data['__CORE__']))
									{
										if ($widget_id != '__ID__')
										{
											$class = self::get_widget_class($widget_data['__SLUG__']);

											if ( ! empty($class) AND class_exists($class))
											{
												$object = new $class();
												$object->set_id($widget_id);
												$object->set_location($location);
												$object->set_row($row_name);
												$object->set_column($column_name);

												if ($admin)
												{
													$form_widgets_output .= $object->admin_form(array_merge($widget_data, isset($data[$widget_id]) ? $data[$widget_id] : array(), array('__ID__' => $widget_id)));
												} else
												{
													$widget_data = array_merge($widget_data, isset($data[$widget_id]) ? $data[$widget_id] : array(), array('__ID__' => $widget_id));

													if ( ! isset($_POST[$widget_id]))
													{
														$_POST[$widget_id] = '';
													}

													$errors = array();

													if (isset($_POST['ether_form']))
													{
														if (isset($widget[$widget_id]) AND is_array($input[$widget_id]))
														{
															foreach ($input[$widget_id] as $k => $v)
															{
																echo '!';
																$errors = array_merge($errors, self::valid($k, $input[$widget_id], $widget_data));

																if (isset($errors['error']) AND $errors['error'])
																{
																	$error_count++;
																}

																if ( ! isset($field_map[$widget_id]) OR ! is_array($field_map[$widget_id]))
																{
																	$field_map[$widget_id] = array();
																}

																$field_map[$widget_id][$k] = $input[$widget_id][$k];
															}
														} else if (isset($widget_data['upload_file']))
														{
															if (isset($file_map[$widget_id]))
															{
																$upload = self::handle_upload($file_map[$widget_id], '+'.time().'_', array('ext' => $widget_data['extensions']));

																if ($upload !== FALSE)
																{
																	if (isset($upload['error']))
																	{
																		if ($upload['type'] == 'upload'.UPLOAD_ERR_NO_FILE AND isset($widget_data['required']) AND $widget_data['required'] == 'on')
																		{
																			$upload['type'] = 'required';
																		}

																		if ($upload['type'] != 'upload'.UPLOAD_ERR_NO_FILE)
																		{
																			$error_data = array();

																			if ($upload['type'] == 'ext')
																			{
																				$error_data[] = $widget_data['extensions'];
																			}

																			$errors[$widget_id][] = array($upload['type'], $error_data);
																			$error_count++;
																		}
																	} else
																	{
																		$field_map[$widget_id] = $upload['url'];
																	}
																}
															}
														} else
														{
															$errors = self::valid($widget_id, $input, $widget_data);

															if (isset($errors['error']) AND $errors['error'])
															{
																$error_count++;
															}

															$field_map[$widget_id] = isset($input[$widget_id]) ? $input[$widget_id] : '';
														}
													}

													if (isset($widget_data['cond_rule_field']) AND ! empty($widget_data['cond_rule_field']))
													{
														if ( ! isset($conditional[$widget_id]))
														{
															$conditional[$widget_id] = array('action' => '', 'condition' => '', 'rules' => array());
														}

														for ($i = 0; $i < count($widget_data['cond_rule_field']); $i++)
														{
															if ( ! empty($widget_data['cond_rule_field'][$i]))
															{
																$conditional[$widget_id]['rules'][] = array('field' => $widget_data['cond_rule_field'][$i], 'check' => $widget_data['cond_rule_check'][$i], 'value' => $widget_data['cond_rule_value'][$i]);
															}
														}

														if ( ! empty($conditional[$widget_id]['rules']))
														{
															$conditional[$widget_id]['action'] = $widget_data['cond_action'];
															$conditional[$widget_id]['condition'] = $widget_data['cond_condition'];
														} else
														{
															unset($conditional[$widget_id]);
														}
													}

													$output = $object->widget($widget_data, $input, $errors);
													$output = apply_filters('ether_form_widget', $output, $widget_id, $widget_data, $object->get_slug());
													$output = apply_filters('ether_form_'.$object->get_slug().'_widget', $output, $widget_id, $widget_data, $object->get_slug());

													$form_widgets_output .= $output;
												}

												$row_begin = FALSE;
											}
										}
									} else
									{
										$row_id = $widget_id;
										$row_begin = TRUE;
										$first_row_data = array_merge($widget_data, array('__ID__' => $widget_id));
									}
								}
							} else
							{
								foreach ($column_data as $widget_id => $widget_data)
								{
									$columns[$column_name][] = array_merge($widget_data, array('__ID__' => $widget_id));
								}
							}
						}

						if ($row_begin)
						{
							$row_columns = array();

							foreach ($columns as $column_index => $column_data)
							{
								foreach ($column_data as $widget)
								{
									$class = self::get_widget_class($widget['__SLUG__']);

									if ( ! empty($class) AND class_exists($class))
									{
										$object = new $class();
										$object->set_id($widget['__ID__']);
										$object->set_location($location);
										$object->set_row($row_name);
										$object->set_column($column_index);

										if ( ! isset($row_columns['col-'.($column_index + 1)]))
										{
											$row_columns['col-'.($column_index + 1)] = '';
										}

										if ($admin)
										{
											$row_columns['col-'.($column_index + 1)] .= $object->admin_form(array_merge($widget, isset($data[$widget['__ID__']]) ? $data[$widget['__ID__']] : array()));
										} else
										{
											$widget_id = $widget['__ID__'];
											$widget_data = array_merge($widget, isset($data[$widget['__ID__']]) ? $data[$widget['__ID__']] : array());

											if (isset($widget_data['extensions']) AND isset($input[$widget_id]))
											{
												$input[$widget_id]['extensions'] = $widget_data['extensions'];
											}

											if ( ! isset($_POST[$widget_id]))
											{
												$_POST[$widget_id] = '';
											}

											$errors = array();

											if (isset($_POST['ether_form']))
											{
												if (isset($input[$widget_id]) AND is_array($input[$widget_id]))
												{
													foreach ($input[$widget_id] as $k => $v)
													{
														$errors = array_merge($errors, self::valid($k, $input[$widget_id], $widget_data));

														if (isset($errors['error']) AND $errors['error'])
														{
															$error_count++;
														}

														if ( ! isset($field_map[$widget_id]) OR ! is_array($field_map[$widget_id]))
														{
															$field_map[$widget_id] = array();
														}

														$field_map[$widget_id][$k] = $input[$widget_id][$k];
													}
												} else if (isset($widget_data['upload_file']))
												{
													if (isset($file_map[$widget_id]))
													{
														$upload = self::handle_upload($file_map[$widget_id], '+'.time(), array('ext' => $widget_data['extensions']));

														if ($upload !== FALSE)
														{
															if (isset($upload['error']))
															{
																if ($upload['type'] == 'upload'.UPLOAD_ERR_NO_FILE AND isset($widget_data['required']) AND $widget_data['required'] == 'on')
																{
																	$upload['type'] = 'required';
																}

																if ($upload['type'] != 'upload'.UPLOAD_ERR_NO_FILE)
																{
																	$error_data = array();

																	if ($upload['type'] == 'ext')
																	{
																		$error_data[] = $widget_data['extensions'];
																	}

																	$errors[$widget_id][] = array($upload['type'], $error_data);
																	$error_count++;
																}
															} else
															{
																$field_map[$widget_id] = $upload['url'];
															}
														}
													}
												} else
												{
													$errors = self::valid($widget_id, $input, $widget_data);

													if (isset($errors['error']) AND $errors['error'])
													{
														$error_count++;
													}

													$field_map[$widget_id] = isset($input[$widget_id]) ? $input[$widget_id] : '';
												}
											}

											if (isset($widget_data['cond_rule_field']) AND ! empty($widget_data['cond_rule_field']))
											{
												if ( ! isset($conditional[$widget_id]))
												{
													$conditional[$widget_id] = array('action' => '', 'condition' => '', 'rules' => array());
												}

												for ($i = 0; $i < count($widget_data['cond_rule_field']); $i++)
												{
													if ( ! empty($widget_data['cond_rule_field'][$i]))
													{
														$conditional[$widget_id]['rules'][] = array('field' => $widget_data['cond_rule_field'][$i], 'check' => $widget_data['cond_rule_check'][$i], 'value' => $widget_data['cond_rule_value'][$i]);
													}
												}

												if ( ! empty($conditional[$widget_id]['rules']))
												{
													$conditional[$widget_id]['action'] = $widget_data['cond_action'];
													$conditional[$widget_id]['condition'] = $widget_data['cond_condition'];
												} else
												{
													unset($conditional[$widget_id]);
												}
											}

											$output = $object->widget($widget_data, $input, $errors);
											$output = apply_filters('ether_form_widget', $output, $widget_id, $widget_data, $object->get_slug());
											$output = apply_filters('ether_form_'.$object->get_slug().'_widget', $output, $widget_id, $widget_data, $object->get_slug());

											$row_columns['col-'.($column_index + 1)] .= $output;
										}
									}
								}
							}

							$class = self::get_widget_class($first_row_data['__SLUG__']);

							if ( ! empty($class) AND class_exists($class))
							{
								$object = new $class();

								$object->set_id($row_id);
								$object->set_location($location);
								$object->set_row($row_name);

								if ($admin)
								{
									$form_widgets_output .= $object->admin_form(array_merge(array('__ID__' => $row_id), $row_columns, $first_row_data));
								} else
								{
									$output = $object->widget(array_merge(array('__ID__' => $row_id), $row_columns, $first_row_data));
									$output = apply_filters('ether_form_widget', $output, $row_id, array(), $object->get_slug());
									$output = apply_filters('ether_form_'.$object->get_slug().'_widget', $output, $row_id, array(), $object->get_slug());

									$form_widgets_output .= $output;

								}
							}
						}
					}
				}
			}

			if ( ! $admin)
			{
				$button_text = ether::meta('button_text', TRUE, $id);
				$button_text = trim($button_text);
				$button_text_color = ether::meta('button_text_color', TRUE, $id);
				$button_background_color = ether::meta('button_background_color', TRUE, $id);
				$button_style = ether::meta('button_style', TRUE, $id);
				$button_align = ether::meta('button_align', TRUE, $id);
				$button_additional_classes = ether::meta('button_additional_classes', TRUE, $id);
				$button_additional_classes = trim($button_additional_classes);
				$button_width = ether::meta('button_width', TRUE, $id);

				$button_styles = array('1' => 'small', '2' => 'medium', '3' => 'big');

				if ($button_width !== '')
				{
					preg_match('/(\d*)(.*)/', $button_width, $button_width_unit);
					$button_width_unit = $button_width_unit[2] === '' ? 'px' : $button_width_unit[2];
					$button_width = intval($button_width);
				}

				if (empty($button_text))
				{
					$button_text = ether::langr('Submit');
				}

				$form_widgets_output .= '<div class="'.ether::config('form_widget_prefix').'buttonset-1">';
				$form_widgets_output .= '<button type="submit" class="'.ether::config('form_widget_prefix').'button '.ether::config('form_widget_prefix'). 'button-'.(isset($button_style) && ! empty($button_style) ? $button_styles[$button_style] : 'medium' ).' '
				.ether::config('form_widget_prefix').(isset($button_align) && ! empty($button_align) ? 'align'.$button_align : 'alignright' )
				.' '.(isset($button_additional_classes) && ! empty($button_additional_classes) ? $button_additional_classes : '').'" style="'.((isset($button_text_color) && ! empty($button_text_color)) ? ('color: '.$button_text_color.';') : '' )
				.(isset($button_background_color) && ! empty($button_background_color) ? ('background-color: '. $button_background_color.';') : '' ).' '.($button_width > 0 ? 'width: '.$button_width.$button_width_unit.';' : '').'" name="'.ether::config('prefix').'form[submit]"><span>'
				.$button_text.'</span></button>';
				$form_widgets_output .= '</div>';
				$form_widgets_output .= '<input type="hidden" name="'.ether::config('prefix').'form_id" value="'.$id.'" />';
				$form_widgets_output .= '</fieldset></form>';

				if ( ! empty($conditional))
				{
					$form_widgets_output .= '<script type="text/javascript">if (typeof ether == \'undefined\') window.ether = { form: { forms: {}}}; ether.form.forms['.$id.'] = '.json_encode($conditional).';</script>';
				}

			}

			if (isset($_POST['ether_form']) AND $error_count == 0)
			{
				self::handle_submit($field_map);

				$confirmation_type = ether::meta('confirmation_type', TRUE, $id);
				$confirmation_url = ether::meta('confirmation_url', TRUE, $id);

				if ($confirmation_type == 'redirect' && ! empty($confirmation_url))
				{
					wp_redirect($confirmation_url);
				} else if (empty($confirmation_type) OR $confirmation_type == 'message')
				{
					$message = ether::meta('confirmation_message', TRUE, $id);

					if (empty($message))
					{
						$message = ether::langr('Thank you!');
					}

					return wpautop($message);
				}

				return '';
			} else
			{
				if ( ! $admin)
				{
					if (function_exists('qtrans_init'))
					{
						return ether::langr($form_widgets_output);
					} else
					{
						return $form_widgets_output;
					}
				}
			}

			return $form_widgets_output;
		}

		public static function handle_submit($field_map)
		{
			if (isset($_POST['ether_form_id']))
			{
				$field_map['ip'] = user_agent::ip();
				$field_map['browser'] = user_agent::browser();
				$field_map['system'] = user_agent::system();
				$field_map['lang'] = user_agent::lang();

				return self::insert($_POST['ether_form_id'], get_the_title($_POST['ether_form_id']), $field_map);
			}
		}

		public static function handle_upload($file, $as_file = NULL, $rules = array())
		{
			$rules = array_merge(array('ext' => array(), 'mime' => array(), 'size' => -1), $rules);

			if ($field['error'] == UPLOAD_ERR_EMPTY)
			{
				return FALSE;
			}

			if ($file['error'] != UPLOAD_ERR_OK)
			{
  				return array('error' => TRUE, 'type' => 'upload'.$file['error']);
			}

			if ( ! is_array($rules['ext']))
			{
				$exts = explode(',', $rules['ext']);
				$rules['ext'] = array();

				foreach ($exts as $ext)
				{
					$ext = trim($ext);

					if ( ! empty($ext))
					{
						$rules['ext'][] = $ext;
					}
				}
			}

			$uploads = wp_upload_dir();

			$dir = $uploads['basedir'].'/';
			$url = $uploads['baseurl'].'/';

			if (isset($file['name']) AND ! empty($file['name']))
			{
				if ($as_file == NULL)
				{
					$as_file = basename($file['name']);
				} else
				{
					if (substr($as_file, 0, 1) == '+')
					{
						$as_file = substr($as_file, 1).basename($file['name']);
					}

					if (substr($as_file, -1, 1) == '*')
					{
						$as_file = rtrim($as_file, '*');
						$as_file .= '.'.end(explode('.', $file['name']));
					}
				}

				if ( ! empty($rules['ext']))
				{
					$ext = end(explode('.', $as_file));

					if ( ! in_array($ext, $rules['ext']))
					{
						return array('error' => TRUE, 'type' => 'ext', 'ext' => $ext);
					}
				}

				if ( ! empty($rules['mime']))
				{
					if ( ! in_array($file['type'], $rules['mime']))
					{
						return array('error' => TRUE, 'type' => 'mime', 'mime' => $file['type']);
					}
				}

				if ($rules['size'] > 0)
				{
					if ($_FILES[$group]['size'] > $rules['size'])
					{
						return array('error' => TRUE, 'type' => 'size', 'size' => $file['size']);
					}
				}

				if (file_exists($dir.$as_file))
				{
					return array('path' => $dir.$as_file, 'url' => $url.$as_file, 'filename' => basename($file['name']), 'type' => $file['type'], 'size' => $file['size'], 'exists' => TRUE);
				} else if (move_uploaded_file($file['tmp_name'], $dir.$as_file))
				{
					return array('path' => $dir.$as_file, 'url' => $url.$as_file, 'filename' => basename($file['name']), 'type' => $file['type'], 'size' => $$file['size']);
				}
			}

			return FALSE;
		}

		public static function insert($form_id, $entry_title, $entry_data)
		{
			$entries = intval(ether::meta('entry_count', TRUE, $form_id));

			if ($entries < 0)
			{
				$entries = 0;
			}

			$entries++;

			$post_id = wp_insert_post(array
			(
				'post_author' => is_user_logged_in() ? get_current_user_id() : 0,
				'post_status' => 'publish',
				'post_type' => 'form-entry',
				'post_title' => $entry_title.' #'.$entries,
				'post_parent' => $form_id
			), TRUE);

			foreach ($entry_data as $k => $v)
			{
				ether::meta('field_'.$k, $v, $post_id, TRUE);
			}

			ether::meta('entry_count', $entries, $form_id, TRUE);

			$email_notification = ether::meta('form_email_notification', TRUE, $_POST['ether_form_id']);

			if ($email_notification == 'on')
			{
				$email = ether::meta('notification_email', TRUE, $_POST['ether_form_id']);
				$title = ether::langr('New entry for %s form', $entry_title);

				wp_mail($email, $title, admin_url('post.php?post='.$post_id.'&action=edit'), array('content-type: text/html'));
			}

			$email_copy = ether::meta('form_email_copy', TRUE, $_POST['ether_form_id']);

			if ($email_copy == 'on')
			{
				$email = ether::meta('copy_email', TRUE, $_POST['ether_form_id']);
				$title = ether::langr($entry_title.' #'.$entries);
				$from_field = ether::meta('from_email', TRUE, $_POST['ether_form_id']);
				$headers = '';
				if($from_field!=0){
					$from = $entry_data[$from_field];
					$headers .= "From: ".$from."\r\n";
					$headers .= "Reply-To: ".$from."\r\n";
					$headers .= "Return-Path: ".$from."\r\n";
					$headers .= "content-type: text/html "."\r\n";
				}
				wp_mail($email, $title, self::get_entry_output($post_id, TRUE, TRUE), $headers);
			}

			$form_data = ether_form::flatten(ether::meta('builder_data', TRUE, $form_id));
			$additional_emails = array();

			foreach ($form_data as $id => $widget)
			{
				if ($widget['__SLUG__'] == 'form-email' And isset($widget['send_email']) AND $widget['send_email'] == 'on')
				{
					foreach ($entry_data as $k => $v)
					{
						if ($k == $id AND ! empty($v))
						{
							$additional_emails[] = $v;
						}
					}
				}
			}

			if ( ! empty($additional_emails))
			{
				$title = ether::langr('Confirmation email for %s', $entry_title);

				wp_mail(implode(',', $additional_emails), $title, self::get_entry_output($post_id, TRUE, TRUE), array('content-type: text/html'));
			}

			return $post_id;
		}

		public function get_entry($form_id, $entry_id = NULL, $main_fields = TRUE, $meta_fields = FALSE)
		{
			global $wpdb;

			$form_widgets = self::flatten(ether::meta('builder_data', TRUE, $form_id));

			if (empty($form_widgets))
			{
				return NULL;
			}

			$metadata = $wpdb->get_results($wpdb->prepare('SELECT posts.ID, posts.post_parent, meta.* FROM '.$wpdb->posts.' posts LEFT JOIN '.$wpdb->postmeta.' meta ON (meta.post_id=posts.id) WHERE post_parent = %d'.( ! empty($entry_id) ? ' AND meta.post_id = %d ' : '').' AND post_type=\'form-entry\'', $form_id, $entry_id), ARRAY_A);
			$entries = array();

			foreach ($metadata as $meta)
			{
				$id = $meta['ID'];
				$key = $meta['meta_key'];
				$value = $meta['meta_value'];

				if ( ! isset($entries[$id]))
				{
					$entries[$id] = array();
				}

				$key = str_replace(ether::config('prefix'), '', $key);

				$entries[$id][$key] = maybe_unserialize($value);
			}

			$output = array();

			foreach ($entries as $entry_id => $fields)
			{
				$output[$entry_id] = array();

				if ($main_fields)
				{
					foreach ($form_widgets as $widget_id => $widget_data)
					{
						if ($widget_data['__SLUG__'] == 'form-recaptcha' || substr($widget_data['__SLUG__'], 0, 3) == 'row')
						{
							continue;
						}

						$label = trim($widget_data['label']);

						if (isset($widget_data['admin_label']) AND ! empty($widget_data['admin_label']))
						{
							$label = trim($widget_data['admin_label']);
						}

						$value = $fields['field_'.$widget_id];
						$value = trim(call_user_func_array(array(self::get_widget_class($widget_data['__SLUG__']), 'admin_format'), array($value)));

						if (empty($label) AND empty($value))
						{
							continue;
						}

						if (empty($label))
						{
							$label = '[This field has no label]';
						}

						$output[$entry_id][] = array($label, (empty($value) ? ether::langr('[No value was provided by the user]') : $value), $widget_data['__SLUG__']);
					}
				}

				if ($meta_fields)
				{
					$meta = array('ip' => 'IP', 'browser' => ether::langr('Browser'), 'system' => ether::langr('System'), 'lang' => ether::langr('Language'));

					foreach ($meta as $key => $label)
					{
						$value = $fields['field_'.$key];

						$output[$entry_id][] = array($label, (empty($value) ? ether::langr('[No value was provided by the user]') : $value), 'meta');
					}
				}
			}

			return $output;
		}

		// admin only
		public static function get_entry_output($id, $main_fields, $meta_fields)
		{
			$p = get_post($id);

			$form_id = $p->post_parent;
			$entry_id = $id;

			$entries = self::get_entry($form_id, $entry_id, $main_fields, $meta_fields);

			if ($entries === NULL)
			{
				return ether::langr('Entry data unavaiable. Parent form was deleted.');
			}

			$output = '';

			foreach ($entries as $entry_id => $entry)
			{
				$output .= '<div class="'.ether::config('form_widget_prefix').'form-entry">';

				foreach ($entry as $field)
				{
					$output .= '<p>';
					$output .= '<strong>'.$field[0].'</strong>: ';

					if ($field[2] == 'form-textarea')
					{
						$output .= '<br>';
					}

					$output .= $field[1];

					$output .= '</p>';
				}

				$output .= '</div>';
			}

			return $output;
		}

		public static function get_error($type, $data = array())
		{
			if ( ! empty($data) AND isset($data[1][1]))
			{
				$subtype = $data[1][1];

				if ($subtype == 'is_unique')
				{
					$type = 'unique';
				} else if ($subtype == 'is_captcha_valid')
				{
					$type = 'captcha';
				}
			}

			if ($type == UPLOAD_ERR_INI_SIZE)
			{
				$type = UPLOAD_ERR_FORM_SIZE;
			} else if ($type == 'ext')
			{
				$type = 'upload'.UPLOAD_ERR_EXTENSION;
			}

			switch ($type)
			{
				case 'required':
					return ether::langr('This field is required');
				break;

				case 'min_length':
					return ether::langr('Minimum number of characters is %s', array_pop($data));
				break;

				case 'max_length':
					return ether::langr('Maximum number of characters is %s', array_pop($data));
				break;

				case 'email':
					return ether::langr('Invalid email address format');
				break;

				case 'callback':
					return ether::langr('Unknown error');
				break;

				case 'unique':
					return ether::langr('This field has to be unique. Value already in use');
				break;

				case 'captcha':
					return ether::langr('The Captcha doesn\'t match');
				break;

				case 'url':
					return ether::langr('Invalid URL format');
				break;

				case 'numeric':
					return ether::langr('Numeric value is required');
				break;

				case 'min':
					return ether::langr('Value must be greater than or equal to %s', array_pop($data));
				break;

				case 'max':
					return ether::langr('Value must be less than or equal to %s', array_pop($data));
				break;

				case 'format':
					return ether::langr('Passed value doesn\'t match required format: %s', array_pop($data));
				break;

				case 'upload'.UPLOAD_ERR_FORM_SIZE:
					return ether::langr('File size over the limit');
				break;

				case 'upload'.UPLOAD_ERR_PARTIAL:
					return ether::langr('Partial upload');
				break;

				case 'upload'.UPLOAD_ERR_NO_FILE:
					return ether::langr('No file');
				break;

				case 'upload'.UPLOAD_ERR_NO_TMP_DIR:
					return ether::langr('No temporary directory');
				break;

				case 'upload'.UPLOAD_ERR_CANT_WRITE:
					return ether::langr('Can\'t write to disk');
				break;

				case 'upload'.UPLOAD_ERR_EXTENSION:
					return ether::langr('File upload stopped by extension. Allowed file types: %s', array_pop($data));
				break;

				default:
					return ether::langr('Unknown error');
				break;
			}
		}

		public static function form_shortcode($args)
		{
			$output = '';

			if (isset($args['id']))
			{
				$title = ether::langr('Open form in lightbox');

				if (isset($args['title']))
				{
					$title = $args['title'];
				}

				if (isset($args['popup']) OR in_array('popup', $args))
				{
					$output .= '<a href="#ether-form-popup-'.$args['id'].'" class="ether-form-popup">'.$title.'</a>';

					$output .= '<div class="ether-form-popup-wrapper" id="ether-form-popup-'.$args['id'].'" style="display: none;">';
				}

				$output .= self::get_the_content($args['id']);

				if (isset($args['popup']) OR in_array('popup', $args))
				{
					$output .= '</div>';
				}
			}

			return $output;
		}

		public static function form_entry_shortcode($args)
		{
			extract(shortcode_atts(array
			(
				'id' => 0,
				'entry_id' => NULL,
				'meta_fields' => FALSE,
				'cols' => 1
			), $args));

			$entries = self::get_entry($id, $entry_id, TRUE, $meta_fields);

			if ($entries == NULL)
			{
				return ether::langr('No entries yet.');
			}

			$output = '';

			foreach ($entries as $entry)
			{
				$output .= '<dl>';

				foreach ($entry as $field)
				{
					/*
						0: field label
						1: field value
						2: field widget type
					*/

					$output .= '<dt>'.$field[0].'</dt>';
					$output .= '<dd>'.$field[1].'</dd>';
				}

				$output .= '</dl>';
			}

			return $output;
		}

		// template tag, use it in custom query loops
		public static function get_the_content($id = NULL)
		{
			$data = ether::meta('builder_data', TRUE, $id);

			if ( ! empty($data))
			{
				if ( ! is_admin() AND did_action('ether_form_header') == 0)
				{
					do_action('ether_form_header');
				}

				return self::parse($data, 'main', FALSE, array(), $id);
			}

			return '';
		}

		public static function the_content($id = NULL)
		{
			echo self::get_the_content($id);
		}

		public static function form_prototypes()
		{
			global $post;
			global $post_type;

			$form_metabox = ether::admin_metabox_get('Form');

			if ( ! empty($form_metabox) AND in_array($post_type, $form_metabox['permissions']))
			{
				echo ether_metabox_form::get_prototypes();
			}
		}

		// HACK #1
		public static function editor_tab($content)
		{
			global $post;
			global $post_type;

			$form_metabox = ether::admin_metabox_get('Form');

			if ( ! empty($form_metabox) AND in_array($post_type, $form_metabox['permissions']))
			{
				if (strpos($content, 'editorcontainer') !== FALSE OR strpos($content, 'wp-content-editor-container') !== FALSE)
				{
					echo '<div id="editor-builder-tab" class="hide-if-no-js hide">'.ether_metabox_form::body().'</div>';
				}
			}

			return $content;
		}

		public static function buffer_output()
		{
			ob_start();
		}

		public static function unserialize($data)
		{
			// replace timestamps stored as integers to strings
			return ether::unserialize(preg_replace('!i:([0-9]{13,20}?);!e', "'s:'.strlen('$1').':\"$1\";'", $data));
		}

		public static function unserialize_fix($null, $object_id, $meta_key, $single)
		{
			$meta_type = 'post';

			if ($meta_key == 'ether_builder_data')
			{
				$meta_cache = wp_cache_get($object_id, $meta_type.'_meta');

				if ( ! $meta_cache)
				{
					$meta_cache = update_meta_cache($meta_type, array($object_id));
					$meta_cache = $meta_cache[$object_id];
				}

				if (isset($meta_cache[$meta_key]))
				{
					if ($single)
					{
						if (is_serialized($meta_cache[$meta_key][0]) AND ! maybe_unserialize($meta_cache[$meta_key][0]))
						{
							return array(self::unserialize($meta_cache[$meta_key][0]));
						}
					} else
					{
						foreach ($meta_cache[$meta_key] as $k => $v)
						{
							if (is_serialized($v) AND ! maybe_unserialize($v))
							{
								return array_map(array('ether_form', 'unserialize'), $meta_cache[$meta_key]);
							}
						}
					}
				}
			}
		}
	}
}

if ( ! class_exists('ether_form_widget'))
{
	class ether_form_widget
	{
		protected $id;
		protected $slug;
		protected $title;
		protected $location;
		protected $row;
		protected $column;
		protected $core;
		protected $label;
		protected $excerpt;
		protected $visible;

		// parse this widget at the end
		protected $after;

		// some private data, special options or smth
		protected $data;
		protected $error_messages;

		public function __construct($slug, $title, $id = NULL, $location = NULL, $row = NULL, $column = NULL)
		{
			$this->core = FALSE;
			$this->slug = ether::slug($slug);
			$this->title = $title;

			$this->id = $id;
			$this->location = $location;
			$this->row = $row;
			$this->column = $column;
			$this->label = '';
			$this->excerpt = '';
			$this->visible = TRUE;
			$this->after = FALSE;
			$this->after_id = '';
			$this->error_messages = array('required', 'unique');

			if ($this->id == NULL)
			{
				$this->id = '__ID__';
			}

			if ($this->location == NULL)
			{
				$this->location = '__LOCATION__';
			}

			if ($this->row == NULL)
			{
				$this->row = '__ROW__';
			}

			if ($this->column == NULL)
			{
				$this->column = '__COLUMN__';
			}

			ether_form::register_widget_data($slug, $title);
		}

		public function is_core()
		{
			return $this->core;
		}

		public function get_field_name($name)
		{
			return ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.']['.$name.']';
		}

		public function get_form_field($name = '')
		{
			return ether::config('prefix').'form['.$this->id.']'.( ! empty($name) ? '['.$name.']' : '');
		}

		public function get_form_value($widget, $data, $name = '')
		{
			if (isset($data[$this->id]) AND $name == '')
			{
				return $data[$this->id];
			} else if (isset($data[$this->id]) AND isset($data[$this->id][$name]))
			{
				return $data[$this->id][$name];
			} else if (isset($widget['default_value']))
			{
				return $widget['default_value'];
			}

			return '';
		}

		public function form_field($type, $name, $data = NULL, $options = array())
		{
			$value = '';

			if (is_array($data))
			{
				$key = $name;
				$key = str_replace(array('[', ']'), array('', ''), $key);

				if (isset($data[$name]))
				{
					$value = $data[$name];
				}
			} else
			{
				$value = $data;
			}

			if ($type == 'select')
			{
				$option_list = '';

				if (isset($options['options']))
				{
					foreach ($options['options'] as $k => $v)
					{
						$option_list .= '<option value="'.$k.'"'.($value == $k ? ' selected="selected"' : '').'>'.$v.'</option>';
					}
				}

 				return '<select name="'.$this->get_form_field($name).'" value="'.$value.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').'>'.$option_list.'</select>';
			} else if ($type == 'textarea')
			{
				if ( ! isset($options['rows']) OR empty($options['rows']))
				{
					$options['rows'] = 5;
				}

				return '<textarea name="'.$this->get_form_field($name).'"'.((isset($options['rows']) AND ! empty($options['rows'])) ? ' rows="'.$options['rows'].'"' : '').((isset($options['cols']) AND ! empty($options['cols'])) ? ' cols="'.$options['cols'].'"' : '').((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').'>'.htmlspecialchars($value).'</textarea>';
			} else
			{
				return '<input name="'.$this->get_form_field($name).'" type="'.$type.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').($type == 'checkbox' ? ($value == 'on' ? ' checked="checked"' : '') : ' value="'.$value.'"').' />';
			}
		}

		public function get_form_error($widget, $errors)
		{
			unset($errors['error']);

			$classes = array('widget', 'msg', 'msg-error');

			$output = '<div class="'.ether::config('form_widget_prefix').implode(' '.ether::config('form_widget_prefix'), $classes).'"><span class="'.ether::config('form_widget_prefix').'msg-icon"></span>';

			$messages = array();

			if (isset($errors[$this->id]) AND ! empty($errors[$this->id]))
			{
				foreach ($errors[$this->id] as $error)
				{
					if (isset($widget['error_'.$error[0]]) AND ! empty($widget['error_'.$error[0]]))
					{
						$messages[] = $widget['error_'.$error[0]];
					} else
					{
						$messages[] = ether_form::get_error($error[0], $error[1]);
					}
				}
			}

			$output .= '<p>'.implode('</p><p>', $messages).'</p>';

			$output .= '</div>';

			if (empty($messages))
			{
				return '';
			}

			return $output;
		}

		public function get_field_atts($name)
		{
			$disabled_fields = ether_form::get_disabled_fields();

			if (in_array($name, $disabled_fields))
			{
				return ' disabled="disabled"';
			}

			return '';
		}

		public function strip($data, $valid_tags = NULL)
		{
			$regexp = '#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?\(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';

			return preg_replace($regexp, '<${1}>', strip_tags($data, $valid_tags));
		}

		public function encode($code)
		{
			return htmlentities($code, ENT_NOQUOTES);
		}

		public function decode($code)
		{
			return html_entity_decode($code, ENT_NOQUOTES);
		}

		public function attr($attr = array())
		{
			if (is_array($attr))
			{
				$attrs = '';

				foreach($attr as $key => $value)
				{
					if ($value === TRUE)
					{
						$attrs .= ' '.$key;
					} elseif (is_array($value) OR trim($value) !== '')
					{
						if (is_array($value))
						{
							$attrs .= ' '.$key.'="'.$this->strip(implode(' ', $value)).'"';
						} else
						{
							$attrs .= ' '.$key.'="'.$this->strip($value).'"';
						}
					}
				}
			} else
			{
				$attrs = ' '.$attr;
			}

			$attrs = trim($attrs);

			return ( ! empty($attrs) ? ' ' : '').$attrs;
		}

		public function _class($classes = array(), $no_prefix_classes = array())
		{
			if ( ! is_array($classes))
			{
				$classes = explode(' ', $classes);
			}

			if ( ! is_array($no_prefix_classes))
			{
				$no_prefix_classes = explode(' ', $no_prefix_classes);
			}

			$class = '';

			if ( ! empty($classes))
			{
				$_classes = array();

				foreach ($classes as $c)
				{
					$c = trim($c);

					if ( ! empty($c))
					{
						$_classes[] = $c;
					}
				}

				$class = ether::config('form_widget_prefix').implode(' '.ether::config('form_widget_prefix'), $_classes);
			}

			if ( ! empty($no_prefix_classes))
			{
				$_no_prefix_classes = array();

				foreach ($no_prefix_classes as $c)
				{
					$c = trim($c);

					if ( ! empty($c))
					{
						$_no_prefix_classes[] = $c;
					}
				}

				$class .= ( ! empty($class) ? ' ' : '').implode(' ', $_no_prefix_classes);
			}

			return ( ! empty($class) ? ' class="'.$class.'"' : '');
		}

		public function tag_open($tag, $attr = array(), $single = FALSE)
		{
			return '<'.$tag.$this->attr($attr).($single ? ' /' : '').'>';
		}

		public static function tag_close($tag, $single = FALSE)
		{
			if ($single)
			{
				return '';
			}

			return '</'.$tag.'>';
		}

		public static function tag($tag, $content = '', $attr = array(), $single = FALSE)
		{
			return $this->tag_open($tag, $attr, $single).$content.$this->tag_close($tag, $single);
		}

		public function field($type, $name, $data = NULL, $options = array())
		{
			$value = '';

			$default_class = str_replace(array('_', '[', ']'), array('-'), 'builder-'.$this->get_slug().'-widget-'.$name.'-field');
			$options['class'] = (isset($options['class']) ? $options['class'].' '.$default_class : $default_class);

			if (is_array($data))
			{
				$key = $name;
				$key = str_replace(array('[', ']'), array('', ''), $key);

				if (isset($data[$name]))
				{
					$value = $data[$name];
				}
			} else
			{
				$value = $data;
			}

			if ($type == 'select')
			{
				$option_list = '';

				if (isset($options['options']))
				{
					foreach ($options['options'] as $k => $v)
					{
						$option_list .= '<option value="'.$k.'"'.($value == $k ? ' selected="selected"' : '').'>'.$v.'</option>';
					}
				} else
				{
					if ( ! empty($value))
					{
						$option_list .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
					} else
					{
						$option_list .= '<option value="" selected="selected"></option>';
					}
				}

 				return '<select'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'" value="'.$value.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').'>'.$option_list.'</select>';
			} else if ($type == 'textarea')
			{
				if ( ! isset($options['rows']) OR empty($options['rows']))
				{
					$options['rows'] = 5;
				}

				return '<textarea'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'"'.((isset($options['rows']) AND ! empty($options['rows'])) ? ' rows="'.$options['rows'].'"' : '').((isset($options['cols']) AND ! empty($options['cols'])) ? ' cols="'.$options['cols'].'"' : '').((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').'>'.htmlspecialchars($value).'</textarea>';
			} else
			{
				return '<input'.$this->get_field_atts($name).' name="'.$this->get_field_name($name).'" type="'.$type.'"'.((isset($options['class']) AND ! empty($options['class'])) ? ' class="'.$options['class'].'"' : '').($type == 'checkbox' ? ($value == 'on' ? ' checked="checked"' : '') : ' value="'.$value.'"').' />';
			}
		}

		public function group_field($type, $name, $index, $data = NULL, $options = array())
		{
			if ($data == NULL OR ! isset($data[$name]) OR ! isset($data[$name][$index]))
			{
				$value = NULL;
			} else
			{
				$value = $data[$name][$index];
			}

			return $this->field($type, $name.'][', $value, $options);
		}

		public function group_item($widget, $index)
		{
			return '';
		}

		public function is_after()
		{
			return $this->after;
		}

		public function get_slug()
		{
			return $this->slug;
		}

		public function get_title($widget = NULL)
		{
			return (isset($widget) AND isset($widget['admin-label']) AND ! empty($widget['admin-label'])) ? $widget['admin-label'] : $this->title;
		}

		public function get_summary($widget)
		{
			return '';
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_location()
		{
			return $this->location;
		}

		public function set_location($location)
		{
			$this->location = $location;
		}

		public function get_row()
		{
			return $this->row;
		}

		public function set_row($row)
		{
			$this->row = $row;
		}

		public function get_column()
		{
			return $this->column;
		}

		public function set_column($column)
		{
			$this->column = $column;
		}

		public function get_label()
		{
			return $this->label;
		}

		public function get_excerpt()
		{
			return '';
		}

		public function set_label($label)
		{
			$this->label = $label;
		}

		public function show()
		{
			$this->visible = TRUE;
		}

		public function hide()
		{
			$this->visible = FALSE;
		}

		// this method was initially added for "heading menu" widget
		// so the widget can iteract and modify currently generetaed content
		public function content_filter($widget, $content)
		{
			return $content;
		}

		public function widget($widget, $input = array(), $errors = array())
		{
			return '';
		}

		public function form($widget)
		{
			return '';
		}

		public function form_after($widget)
		{
			return '';
		}

		public function get_widget_location_preview($widget)
		{
			return '';
		}

		public static function admin_format($value)
		{
			$predefined_fields = array
			(
				'address' => ether::langr('Street'),
				'address2' => ether::langr('Street (Line #2)'),
				'city' => ether::langr('City'),
				'zip' => ether::langr('Zip / Postal code'),
				'state' => ether::langr('State / Province / Region'),
				'country' => ether::langr('Country'),
				'am' => ether::langr('AM'),
				'pm' => ether::langr('PM')
			);

			$predefined_fields = apply_filters('ether_form_predefined_fields', $predefined_fields);

			if (is_array($value))
			{
				$_value = '';

				foreach ($value as $k => $v)
				{
					if (empty($v))
					{
						$v = ether::langr('[No value was provided by the user]');
					} else
					{
						if ($k == 'country')
						{
							$countries = ether_forms_countries();

							$v = $countries[$v];
						}
					}

					if (isset($predefined_fields[$k]))
					{
						$_value .= $predefined_fields[$k]._n.trim($v)._n._n;
					} else
					{
						$_value .= trim($v)._n._n;
					}
				}

				$value = $_value;
			}

			$value = nl2br(htmlspecialchars($value));

			return $value;
		}

		public function set_widget_alignment_class($widget)
		{
			$output = '';

			if (isset($widget['align']) && ! empty($widget['align']))
			{
				$output .= ' builder-align'.$widget['align'];
			}

			return $output;
		}

		public function set_widget_clearfloat_class($widget)
		{
			$output = '';

			if (isset($widget['clearfloat']) && ! empty($widget['clearfloat']))
			{
				$output .= ' builder-clearfloat-indicator';
			}

			return $output;
		}

		public function set_widget_visibility_class($widget)
		{
			$output = '';

			if ($this->is_hidden($widget))
			{
				$output .= ' builder-hidden-widget';
			}

			return $output;
		}

		public function set_widget_width_styles($widget)
		{
			$output = '';
			$width;

			if ( isset($widget['width']) && ! empty($widget['width']))
			{
				$width = ether::unit($widget['width'], 'px');
			} 

			if (isset($width))
			{
				$output .= 'width: '.$width.';';
			}
			
			return $output;
		}

		public function admin_form($widget = NULL)
		{
			return '<div class="builder-widget-wrapper column-1 '.($this->id != '__ID__' ? 'initialized ' : '').($this->is_core() ? 'builder-widget-core ' : '').'builder-widget-type-'.$this->get_slug().( ! $this->visible ? ' hide' : '').' '.$this->set_widget_alignment_class($widget).''.$this->set_widget_clearfloat_class($widget).''.$this->set_widget_visibility_class($widget).'" style="'.$this->set_widget_width_styles($widget).'">
				<div class="builder-widget">
					<div class="builder-widget-bar widget widget-top '.($this->is_core() ? 'builder-core-widget-bar' : '' ).'">
						<div class="builder-widget-bar-info">
							<div class="builder-widget-icon builder-widget-icon-'.$this->get_slug().( ! $this->visible ? ' hide' : '').'"></div>
							<div class="builder-widget-title">'.$this->get_title($widget).'</div>
							<div class="builder-widget-summary">'.$this->get_summary($widget).'</div>
							<div class="builder-widget-excerpt">'.$this->get_excerpt().'</div>
							<div class="builder-widget-label">'.$this->get_label().'</div>
						</div>
						<div class="builder-widget-location-preview">'.$this->get_widget_location_preview($widget).'</div>
						<div class="builder-widget-actions">
							<a href="#edit" class="duplicate">'.ether::langr('Duplicate').'</a>
							<a href="#edit" class="edit">'.ether::langr('Edit').'</a>
							<a href="#remove" class="remove">'.ether::langr('Remove').'</a>
						</div>
					</div>
					<div class="builder-widget-content closed">
						<div class="builder-widget-content-bar widget widget-top">
							<div class="builder-widget-title">'.$this->get_title($widget).'</div>
						</div>
						<div class="builder-widget-inner">
							<div class="builder-widget-content-form">'.$this->form($widget).'</div>
						</div>
						<div class="builder-widget-content-actions">
								<button name="builder-widget-save" class="save">'.ether::langr('Save').'</button>
								<button name="builder-modal-close" class="builder-modal-close">'.ether::langr('Close').'</button>
						</div>
					</div>
					<input type="hidden" name="'.ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.'][__SLUG__]" value="'.$this->get_slug().'" />'.($this->is_core() ? $this->form_after($widget).'<input type="hidden" name="'.ether::config('prefix').'builder_widget['.$this->location.']['.$this->row.']['.$this->column.']['.$this->id.'][__CORE__]" value="true" />' : '').'
				</div>
			</div>'._n;
		}

		public function wp_admin_form($widget, $id_base, $number)
		{
			$form = $this->admin_form($widget);
			$atts = array('name', 'id');

			foreach ($atts as $attr)
			{
				preg_match_all('| '.$attr.'=\"'.ether::config('prefix').'builder_widget\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\](.*)\"|iU', $form, $fields);

				if (isset($fields[5]) AND count($fields[5]) > 0)
				{
					$count = count($fields[3]);

					for ($i = 0; $i < $count; $i++)
					{
						$form = str_replace($fields[0][$i], ' '.$attr.'="widget-'.$id_base.'['.$number.']['.$fields[5][$i].']'.$fields[6][$i].'"', $form);
					}
				}
			}

			return $form;
		}

		protected function form_common($widget)
		{
			$columns = array();
			$rows = array();

			for ($i = 1; $i <= 10; $i++)
			{
				if ($i == 7 || $i == 9)
				{
					$i++;
				}

				$columns[$i] = $i;
				$rows[$i] = $i;
			}

			$output = '';
			$output .= '<h2 class="ether-tab-title">'.ether::langr('Grid Settings').'</h2>
				<div class="ether-tab-content">
					<div class="cols cols-3">
						<div class="col"><label><span class="label-title">'.ether::langr('Columns').'</span> '.$this->field('select', 'columns', $widget, array('options' => $columns)).'</label></div>
						<div class="col"><label><span class="label-title">'.ether::langr('Rows').'</span> '.$this->field('select', 'rows', $widget, array('options' => $rows)).'<small>'.ether::langr('Row count applies only if element is a slider. If you want to limit element count for non slider elements set a proper \'Count\' value if available or limit the amount of included elements').'</small></label></div>
						<div class="col">
						<label>'.$this->field('checkbox', 'disable_spacing', $widget).'<span class="label-title"> '.ether::langr('Disable spacing').'</span> <small>'.ether::langr('Most useful for galleries with images/videos where you don\'t want spacing between elements to occur').'</small></label>
						</div>
					</div>
				</div>';

			return $output;
		}

		protected function form_media_frame($widget)
		{
			$frames = array
			(
				'' => ether::langr('Theme default'),
				'1' => ether::langr('Ether frame 1'),
				'2' => ether::langr('Ether frame 2')
			);
			$height = array
			(
				'auto' => ether::langr('Default'),
				'200' => ether::langr('Short (200px)'),
				'300' => ether::langr('Medium (300px)'),
				'400' => ether::langr('Tall (400px)'),
				'constrain' => ether::langr('Constrain (max 400px)')
			);

			$image_mode = array
			(
				'auto' => ether::langr('Default'),
				'x' => ether::langr('Stretch X'),
				'y' => ether::langr('Stretch Y'),
				'fit' => ether::langr('Fit'),
				'fill' => ether::langr('Fill')
			);

			$ratio = array
			(
				50 => ether::langr('50%%'),
				75 => ether::langr('75%%'),
				100 => ether::langr('100%%'),
				150 => ether::langr('150%%'),
				200 => ether::langr('200%%')
			);

			$output = '';

			$output .= '<div class="cols-2">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Elements Height').'</span> '.$this->field('select', 'height', $widget, array('options' => $height, 'class' => 'ether-cond ether-group-1')).'<small>'.ether::langr('Default: Does nothing. Constrain: Constrains element container size ratio to 1:1. If you want to constrain images size instead, use crop width/height fields below').'</small></label>
						<label class="ether-cond-constrain ether-group-1 "><span class="label-title">'.ether::langr('Constrain Ratio').'</span> '.$this->field('select', 'ratio', $widget, array('options' => $ratio, 'class' => '')).'<small>'.ether::langr('Height to width ratio for constrained galleries.').'</small></label>
					</div>
					<div class="col">
						<label><span class="label-title">'.ether::langr('Frame Style').'</span> '.$this->field('select', 'frame', $widget, array('options' => $frames)).'</label>
					</div>
				</div>
				<div class="cols-3">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image mode').'</span> '.$this->field('select', 'image_mode', $widget, array('options' => $image_mode)).'<small>'.ether::langr('Default: No image manipulations. Fit: Images will be scaled to fit whole available space. Stretch: Stretches images horizontally/vertically. Fit: Fits image within container - will usually leave out blank spaces').'</small></label>
				</div>
				<div class="col">
					<label class="label-alt-1">'.$this->field('checkbox', 'disable_lightbox', $widget).' <span class="label-title">'.ether::langr('Disable lightbox').'</span></label>
				</div>
				<div class="col">
					<label>'.$this->field('checkbox', 'enable_title', $widget).' <span class="label-title">'.ether::langr('Enable titles').'</span></label>
				</div>
			</div>';

			return $output;
		}

		protected function form_image_dimensions($widget)
		{
			$output = '';

			$output .= '<div class="cols-2">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image width').'</span> '.$this->field('text', 'image_width', $widget).'<small>'.ether::langr('"0" or blank field skips scaling through image width attribute.').'</small></label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image height').'</span> '.$this->field('text', 'image_height', $widget).'<small>'.ether::langr('"0" or blank field skips scaling through image width attribute.').'</small></label>
				</div>
			</div>
			<div class="cols-2 cols">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image crop width').'</span> '.$this->field('text', 'image_crop_width', $widget).'<small>'.ether::langr('"0" or blank field skips scaling/croping image. This function will generate thumbnail.').'</small></label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Image crop height').'</span> '.$this->field('text', 'image_crop_height', $widget).'<small>'.ether::langr('"0" or blank field skips scaling/croping image. This function will generate thumbnail.').'</small></label>
				</div>
			</div>';

			return $output;
		}

		protected function form_widget_general($widget, $height = false)
		{
			$aligns = array
			(
				'' => ether::langr('Default'),
				'left' => ether::langr('Left'),
				'right' => ether::langr('Right'),
				'center' => ether::langr('Center')
			);

			$output = '';

			$output .= '<div class="cols cols-'.($height == true ? 3 : 2).'">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Widget Alignment').'</span> '.$this->field('select', 'align', $widget, array('options' => $aligns, 'class' => 'builder-widget-align-field')).'</label>
				</div>
				<div class="col">
					<label><span class="label-title">'.ether::langr('Widget width').'</span> '.$this->field('text', 'width', $widget, array('class' => 'builder-widget-width-field')).'</label>
				</div>'
				.($height == true ? '<div class="col">
					<label><span class="label-title">'.ether::langr('Widget height').'</span> '.$this->field('text', 'height', $widget, array('class' => 'builder-widget-height-field')).'</label>
				</div>
				' : '').'
			</div>';

			return $output;
		}

		protected function form_widget_clearfloat($widget)
		{
			$output = '';

			$output .= '<div class="cols cols-1">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Clear this widget away from left/right aligned widgets.').'</span> '.$this->field('checkbox', 'clearfloat', $widget, array('class' => 'builder-widget-clearfloat-field')).'</label>
				</div>
			</div>';

			return $output;
		}

		protected function form_widget_visibility($widget)
		{
			$output = '';

			$output .= '<div class="cols cols-1">
				<div class="col">
					<label><span class="label-title">'.ether::langr('Hide this widget.').'</span> '.$this->field('checkbox', 'is_hidden', $widget, array('class' => 'builder-widget-visibility-field')).'<small>'.ether::langr('Use this to temporarily hide this widget from the view in the front-end without removing the widget entirely').'</small></label>
				</div>
			</div>';

			return $output;
		}

		protected function is_hidden($widget)
		{
			return ! isset($widget['is_hidden']) ? false : $widget['is_hidden'] == 'on' ? true : false;
		}

		protected function form_errors($widget)
		{
			if (empty($this->error_messages))
			{
				return '';
			}

			$output = '<fieldset class="ether-form">
				<h2 class="ether-tab-title">'.ether::langr('Error Messages').'</h2>
				<div class="ether-tab-content">
					<p class="ether-info">'.ether::langr('Filling the fields below will overwrite default error messages').':</p>
					<div class="cols-2 cols">';

			foreach ($this->error_messages as $type)
			{
				$output .= '<div class="col">
					<label><span class="label-title">'.ether_form::get_error($type, array('%s')).'</span> '.$this->field('text', 'error_'.$type, $widget).'</label>
				</div>';
			}

			$output .= '</div>
			</fieldset>';

			return $output;
		}

		protected function conditional_rule($widget, $i)
		{
			$operators = array
			(
				'is' => ether::langr('Is'),
				'isnot' => ether::langr('Is not')
			);

			return '<div class="col"'.(empty($widget) ? ' style="display: none;"' : '').'>
				<div class="group-item">
					<div class="group-item-title">'.ether::langr('Rule').'</div>
					<div class="group-item-content">
						'.$this->group_field('select', 'cond_rule_field', $i, $widget, array('class' => 'ether-40')).$this->group_field('select', 'cond_rule_check', $i, $widget, array('options' => $operators, 'class' => 'ether-20')).$this->group_field('select', 'cond_rule_value', $i, $widget, array('class' => 'ether-40')).'
					</div>
					<div class="group-item-actions">
						<button name="builder-widget-tab-remove" class="builder-widget-group-item-remove">'.ether::langr('Remove').'</button>
					</div>
				</div>
			</div>';
		}

		protected function form_conditional($widget)
		{
			$actions = array
			(
				'show' => ether::langr('Show'),
				'hide' => ether::langr('Hide')
			);

			$conditions = array
			(
				'all' => ether::langr('All'),
				'any' => ether::langr('Any')
			);

			$rules = '';

			if (isset($widget['cond_rule_field']))
			{
				$column = 0;

				for ($i = 0; $i < count($widget['cond_rule_field']); $i++)
				{
					if ( ! empty($widget['cond_rule_field'][$i]))
					{
						$rules .= $this->conditional_rule($widget, $i);
					}
				}
			}

			return '<h2 class="ether-tab-title">'.ether::langr('Conditional Logic').'</h2>
			<div class="ether-tab-content">
				<div class="cols-2 cols">
					<div class="col">
						<label><span class="label-title">'.ether::langr('Action').'</span> '.$this->field('select', 'cond_action', $widget, array('options' => $actions)).'</label>
					</div>

					<div class="col">
						<label><span class="label-title">'.ether::langr('Condition').'</span> '.$this->field('select', 'cond_condition', $widget, array('options' => $conditions)).'</label>
					</div>
				</div>
				<div class="cols-1 cols">
					<div class="col">
						<div class="sortable-content group-content-wrap">
							<div class="buttonset-1">
								<button name="builder-widget-group-item-add" class="builder-button-classic alignright builder-widget-group-item-add">'.ether::langr('Add rule').'</button>
							</div>
							<div class="group-prototype">'.$this->conditional_rule(array(), -1).'</div>
							<div class="group-content">
								<div class="cols-1 cols">
									'.$rules.'
								</div>
							</div>
							<div class="buttonset-1">
								<button name="builder-widget-group-item-add" class="builder-button-classic alignright builder-widget-group-item-add">'.ether::langr('Add rule').'</button>
							</div>
						</div>
					</div>
				</div>
			</div>';
		}
	}
}

if ( ! class_exists('ether_form_sidebar_widget'))
{
	class ether_form_sidebar_widget extends WP_Widget
	{
		public function __construct()
		{
			parent::__construct(FALSE, ether::langx('Ether form', 'widget name', TRUE), array());
		}

		public function widget($args, $instance)
		{
			if (isset($instance['form_id']))
			{
				ether_form::the_content($instance['form_id']);
			}
		}

		public function update($new_instance, $old_instance)
		{
			return $new_instance;
		}

		public function form($instance)
		{
			$forms = array();

			$posts = ether::get_posts(array('post_type' => 'form', 'numberposts' => -1));

			foreach ($posts as $form)
			{
				$forms[$form['id']] = array('name' => $form['title']);
			}

			$output = '<fieldset class="ether-form">';
			$output .= '<label>'.ether::langr('Form').' '.ether::make_field($this->get_field_name('form_id'), array('type' => 'select', 'options' => $forms), $instance['form_id'], '').'</label>';
			$output .= '</fieldset>';

			echo $output;
		}
	}
}

if (class_exists('ether_builder') AND ! class_exists('ether_builder_form_widget'))
{
	class ether_builder_form_widget extends ether_builder_widget
	{
		public function __construct()
		{
			parent::__construct('form', ether::langr('Ether Form'));
			$this->label = ether::langr('Add form created with Ether Forms plugin');
		}

		public function widget($widget)
		{
			$widget = ether::extend( array
			(
				'popup_title' => ''
			), $widget);

			$form = ether_form::get_the_content($widget['form_id']);

			if (isset($widget['popup']) AND $widget['popup'] == 'on')
			{
				$title = ether::langr('Open form in lightbox');

				if ( ! empty($widget['popup_title']))
				{
					$title = $widget['popup_title'];
				}

				$form = '<a href="#ether-form-popup-'.$widget['form_id'].'" class="ether-form-popup">'.$title.'</a><div class="ether-form-popup-wrapper" id="ether-form-popup-'.$widget['form_id'].'" style="display: none;">'.$form.'</div>';
			}

			return $form;
		}

		public function form($widget)
		{
			$posts = ether::get_posts(array('post_type' => 'form', 'numberposts' => -1));

			foreach ($posts as $form)
			{
				$forms[$form['id']] = $form['title'];
			}

			return '<fieldset class="ether-form">
				<h2 class="ether-tab-title">General</h2>
				<div class="ether-tab-content">
					<label><span class="label-title">'.ether::langr('Ether Form').'</span> '.$this->field('select', 'form_id', $widget, array('options' => $forms)).'</label>
					<label>'.$this->field('checkbox', 'popup', $widget, array('class' => 'ether-cond ether-group-1')).' <span class="label-title">'.ether::langr('Open in popup').'</span></label>
					<div class="cols cols-1 ether-cond-on ether-group-1">
						<div class="col">
							<label><span class="label-title">'.ether::langr('Popup link title').'</span> '.$this->field('text', 'popup_title', $widget).'</label>
						</div>
					</div>
				</div>
			</fieldset>';
		}
	}

	ether_builder::register_widget('ether_builder_form_widget');
}

ether::import('modules.form-widget');

?>
