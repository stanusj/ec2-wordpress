<?php

if ( ! class_exists('ether_validator'))
{
	class ether_validator
	{
		public $data;
		public $rules;
		public $filters;
		public $errors;

		public function __construct()
		{
			$this->data = array();
			$this->rules = array();
			$this->filters = array();
		}

		public static function email($email)
		{
			return (preg_match('/[-a-zA-Z0-9_.+]+@[a-zA-Z0-9-]{2,}\.[a-zA-Z]{2,}/', $email) > 0) ? TRUE : FALSE;
		}

		public static function url($url)
		{
			return ( ! preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
		}

		public static function callback($value, $key, $callback)
		{
			if (call_user_func_array($callback, array($key, $value)))
			{
				return TRUE;
			}

			return FALSE;
		}

		public static function numeric($num)
		{
			return strval($num) == '' OR is_numeric($num);
		}

		public static function between($num, $low, $high)
		{
			return ($this->numeric($num) AND $num >= $low AND $num <= $high);
		}

		public static function alpha($content)
		{
			return ctype_alpha($content);
		}

		public static function alpha_numeric($content)
		{
			return ctype_alnum($content);
		}

		public static function lower_case($content)
		{
			return (strtolower($content) == $content);
		}

		public static function upper_case($content)
		{
			return (strtoupper($content) == $content);
		}

		public static function zip_code($zip_code)
		{
			return preg_match('/^([0-9]{5})(-[0-9]{4})?$/i', $zip_code);
		}

		public static function phone($phone)
		{
			return preg_match('/^[\(]?[0-9]{3}[\)]?[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/', $phone);
		}

		public static function date($date, $format = 'DD-MM-YYYY')
		{
			$format = explode('-', $format);
			$nodes = count($format);
			$exp;

			for ($i = 0; $i < $nodes; $i++)
			{
				$exp .= '([0-9]{'.strlen($format[$i]).'})';

				if ($i < $nodes-1)
				{
					$exp .= '-';
				}
			}

			if (preg_match('/^'.$exp.'$/', $date, $parts))
			{
				return TRUE;
			}

			return FALSE;
		}

		public function min_length($content, $length)
		{
			return (strlen($content) >= $length);
		}

		public function max_length($content, $length)
		{
			return (strlen($content) <= $length);
		}

		public function length($content, $length)
		{
			return(strlen($content) == $length);
		}

		public function match($content, $content_match)
		{
			return ($content === $this->data[$content_match]);
		}

		public function regexp($content, $regexp)
		{
			return preg_match($regexp, $content);
		}

		public function required($content)
		{
			return ( ! empty($content));
		}

		public function min($content, $min)
		{
			return ($this->numeric($content) AND $content >= $min);
		}

		public function max($content, $max)
		{
			return ($this->numeric($content) AND $content <= $max);
		}

		public function one_of($content, array $matches)
		{
			foreach ($matches as $match)
			{
				if ($content == $match)
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		public static function format_to_number($matches)
		{
			return '([0-9]{'.strlen($matches[0]).'})';
		}

		public static function format_to_alpha($matches)
		{
			return '([a-zA-Z]{'.strlen($matches[0]).'})';
		}

		public static function format_to_regexp($format)
		{
			$format = preg_quote($format);

			$format = str_replace('\(', '(', $format);
			$format = str_replace('\)', ')?', $format);

			$format = preg_replace_callback('/[xX]{1,30}/', array('ether_validator', 'format_to_number'), $format);
			$format = preg_replace_callback('/[aA]{1,30}/', array('ether_validator', 'format_to_alpha'), $format);

			// make all spaces optional
			$format = str_replace(' ', '( )*', $format);

			return '/^'.$format.'$/';
		}

		public static function format($content, $format)
		{
			$parts = explode('|', $format);
			$count = count($parts);

			for ($i = 0; $i < $count; $i++)
			{
				$part = trim($parts[$i]);

				if (preg_match(self::format_to_regexp($part), $content))
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		public function validate(&$data)
		{
			$this->data = $data;
			$this->errors = array();

			foreach($this->filters as $filter => $filter_data)
			{
				foreach($filter_data as $filter_key => $filter_params)
				{
					if (isset($data[$filter]))
					{
						if (is_array($filter_params['params']))
						{
							array_unshift($filter_params['params'], $data[$filter]);
						} else
						{
							if ($filter_params['params'] == NULL)
							{
								$filter_params['params'] = array($data[$filter]);
							} else
							{
								$filter_params['params'] = array($data[$filter], $filter_params['params']);
							}
						}

						$data[$filter] = call_user_func_array($filter_params['filter'], $filter_params['params']);
					}
				}
			}

			foreach($this->rules as $rule => $rule_data)
			{
				foreach($rule_data as $rule_key => $rule_params)
				{
					if (is_string($rule_key))
					{
						if (isset($data[$rule]))
						{
							if (isset($this->filters['*']))
							{
								foreach($this->filters['*'] as $filter)
								{
									array_unshift($filter['params'], $data[$rule]);

									$data[$rule] = call_user_func_array($filter['filter'], $filter['params']);
								}
							}

							if ( ! is_array($rule_params))
							{
								if ($rule_params == NULL)
								{
									$rule_params = array($data[$rule]);
								} else
								{
									$rule_params = array($data[$rule], $rule_params);
								}
							} else
							{
								array_unshift($rule_params, $data[$rule]);
							}

							if ( ! call_user_func_array(array($this, $rule_key), $rule_params))
							{
								array_shift($rule_params);

								$this->errors[$rule][] = array($rule_key, $rule_params);
							}
						} else
						{
							if ( ! is_array($rule_params))
							{
								if ($rule_params == NULL)
								{
									$rule_params = array();
								} else
								{
									$rule_params = array($rule_params);
								}
							}

							$this->errors[$rule][] = array($rule_key, $rule_params);
						}
					}
				}
			}

			if (count($this->errors) > 0)
			{
				$this->errors['error'] = TRUE;
			}

			return $this->errors;

			$this->data = array();
			$this->rules = array();
			$this->filters = array();
			$this->errors = array();
		}

		public function rule($field, array $rule)
		{
			if (is_array($field))
			{
				foreach($field as $key)
				{
					if (isset($this->rules[$key]))
					{
						$this->rules[$key] = array_merge($rule, $this->rules[$key]);
					} else
					{
						$this->rules[$key] = $rule;
					}
				}
			} else
			{
				if (isset($this->rules[$field]))
				{
					$this->rules[$field] = array_merge($rule, $this->rules[$field]);
				} else
				{
					$this->rules[$field] = $rule;
				}
			}

			return $this;
		}

		public function filter($field, $filter, $params = array())
		{
			if (is_array($field))
			{
				foreach($field as $key)
				{
					$this->filters[$key][] = array
					(
						'filter' => $filter,
						'params' => $params
					);
				}
			} else
			{
				$this->filters[$field][] = array
				(
					'filter' => $filter,
					'params' => $params
				);
			}

			return $this;
		}
	}
}

?>
