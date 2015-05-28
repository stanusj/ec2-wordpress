<?php

class user_agent
{
	private static $ip;
	private static $referer;
	private static $agent;
	private static $system;
	private static $engine;
	private static $lang_code;
	private static $lang_name;
	private static $browser;

	public static function ip()
	{
		if (empty(self::$ip))
		{
			$ip_part = explode('.', $_SERVER['REMOTE_ADDR']);

			if ($ip_part[0] == '165' AND $ip_part[1] == '21')
			{
				if (getenv('HTTP_CLIENT_IP'))
				{
					self::$ip = getenv('HTTP_CLIENT_IP');
				} elseif (getenv('HTTP_X_FORWARDED_FOR'))
				{
					self::$ip = getenv('HTTP_X_FORWARDED_FOR');
				} elseif (getenv('REMOTE_ADDR'))
				{
					self::$ip = getenv('REMOTE_ADDR');
				}
			} else
			{
				self::$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		return self::$ip;
	}

	public static function referer()
	{
		if (empty(self::$referer))
		{
			if (isset($_SERVER['HTTP_REFERER']))
			{
				self::$referer = $_SERVER['HTTP_REFERER'];
			} elseif (getenv('HTTP_REFERER'))
			{
				self::$referer = getenv('HTTP_REFERER');
			} else
			{
				self::$referer = 'Unknown';
			}
		}

		return self::$referer;
	}

	public static function agent()
	{
		if (empty(self::$agent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				self::$agent = $_SERVER['HTTP_USER_AGENT'];
			} elseif (getenv('HTTP_USER_AGENT'))
			{
				self::$agent = getenv('HTTP_USER_AGENT');
			}
		}

		return self::$agent;
	}

	public static function system()
	{
		if (empty(self::$system))
		{
			if (preg_match('/windows nt 6/i', self::agent()))
			{
				self::$system = 'Windows Vista';
			} elseif (preg_match('/windows nt 5.1/i', self::agent()))
			{
				self::$system = 'Windows XP';
			} elseif (preg_match('/windows xp/i', self::agent()))
			{
				self::$system = 'Windows XP';
			} elseif (preg_match('/linux/i', self::agent()))
			{
				self::$system = 'Linux';
			} elseif (preg_match('/macintosh/i', self::agent()))
			{
				self::$system = 'Macintosh';
			} elseif (preg_match('/win 9x 4.90/i', self::agent()))
			{
				self::$system = 'Windows ME';
			} elseif (preg_match('/windows me/i', self::agent()))
			{
				self::$system = 'Windows ME';
			} elseif (preg_match('/windows nt 5.0/i', self::agent()))
			{
				self::$system = 'Windows 2000';
			} elseif (preg_match('/windows 2000/i', self::agent()))
			{
				self::$system = 'Windows 2000';
			} elseif (preg_match('/windows nt 4.0/i', self::agent()))
			{
				self::$system = 'Windows NT 4.0';
			} elseif (preg_match('/windows 98/i', self::agent()))
			{
				self::$system = 'Windows 98';
			} elseif (preg_match('/sunos/i', self::agent()))
			{
				self::$system = 'Sun';
			} else
			{
				self::$system = 'Unknown';
			}
		}

		return self::$system;
	}

	public static function browser()
	{
		if (empty(self::$browser))
		{
			$browser_list = array
			(
				array('Internet Explorer 6.0', 'MSIE 6.0; Windows NT 5.1'),
				array('Internet Explorer 7.0', 'MSIE 7.0'),
				array('Internet Explorer 8.0', 'MSIE 8.0'),
				array('Firefox 1.0', 'Firefox/1.0'),
				array('Firefox 1.04', 'Firefox/1.0.4'),
				array('Firefox 1.06', 'Firefox/1.0.6'),
				array('Firefox 1.07', 'Firefox/1.0.7'),
				array('Firefox 1.4b', 'Firefox/1.4'),
				array('Firefox 1.5b', 'Firefox/1.5'),
				array('Firefox 2.0', 'Firefox/2.0'),
				array('Firefox 3.0', 'Firefox/3.0'),
				array('Firefox 3.5', 'Firefox/3.5'),
				array('Firefox', 'Firefox'),
				array('Google Chrome 5', 'Chrome/5.0'),
				array('Google Chrome 4', 'Chrome/4.0'),
				array('Google Chrome 2', 'Chrome/2.0'),
				array('Google Chrome ', 'Chrome'),
				array('Mozilla SeaMonkey', 'SeaMonkey/1.1a'),
				array('Netscape 3.0', 'Mozilla/3.0'),
				array('Netscape 4.61', 'Mozilla/4.61'),
				array('Netscape 4.7', 'Mozilla/4.7C-CCK-MCD'),
				array('Netscape 4.8', 'Mozilla/4.8'),
				array('Netscape 4.9', 'Mozilla/4.9'),
				array('Netscape 5.0', 'Mozilla/5.0'),
				array('Netscape', 'Mozilla'),
				array('Netscape 7.0', 'Netscape/7.0'),
				array('Netscape 7.1', 'Netscape/7.1'),
				array('Netscape 8.0.1', 'Netscape/8.0.1'),
				array('Opera 6.03', 'Opera 6.03'),
				array('Opera 7.23', 'Nokia9500/7.23'),
				array('Opera 8.0', 'Opera/8.0'),
				array('Opera 8.02', 'Opera/8.02'),
				array('Opera 8.50', 'Opera/8.50'),
				array('Opera 9.0', 'Opera/9.0'),
				array('Safari 125', 'Safari/125'),
				array('Safari 312', 'Safari/312'),
				array('Safari 2.0', 'Safari/412'),
				array('Avant Browser', 'Avant Browser'),
				array('Konqueror', 'Konqueror'),
				array('Minimo', 'Minimo'),
				array('Mozilla', 'X11; U; Linux i686; cs-CZ; rv:'),
				array('WebPro', 'WebPro/3.0.1a'),
				array('OmniWeb 5.1.1', 'OmniWeb/v563.51'),
				array('ELinks 0.4', 'ELinks'),
				array('Links 0.99', '0.99pre14'),
				array('Links 2.1', 'Links'),
				array('Lynx 2.84', 'Lynx/2.8.4rel.1'),
				array('OffByOne', 'OffByOne'),
				array('w3m', 'w3m/0.5.1')
			);

			$size = count($browser_list);

			for ($i = 0; $i < $size; $i++)
			{
				if (stripos(self::agent(), $browser_list[$i][1]) !== FALSE)
				{
					self::$browser = $browser_list[$i][0];
					break;
				}
			}

			if (empty(self::$browser))
			{
				self::$browser = 'Unknown';
			}
		}

		return self::$browser;
	}

	public static function engine()
	{
		$url = parse_url(self::referer());

		if (empty($url))
		{
			self::$engine = 'Unknown';
		}

		if (empty(self::$engine))
		{
			if (isset($url['host']))
			{
				if (stripos($url['host'], 'google') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Google Search';
				} elseif (stripos($url['host'], 'yahoo.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Yahoo Search';
				} elseif (stripos($url['host'], 'ask.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Ask Search';
				} elseif (stripos($url['host'], 'alltheweb.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'All The Web Search';
				} elseif (stripos($url['host'], 'aol.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'AOL Search';
				} elseif (stripos($url['host'], 'hotbot.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Hot Bot';
				} elseif (stripos($url['host'], 'teoma.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Teoma';
				} elseif (stripos($url['host'], 'altavista.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Alta Vista Search';
				} elseif (stripos($url['host'], 'gigablast.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Giga Blast';
				} elseif (stripos($url['host'], 'looksmart.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Look Smart';
				} elseif (stripos($url['host'], 'lycos.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Lycos Search';
				} elseif (stripos($url['host'], 'msn.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'MSN Search';
				} elseif (stripos($url['host'], 'netscape.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Netscape Search';
				} elseif (stripos($url['host'], 'dmoz.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'DMOZ';
				} elseif (stripos($url['host'], 'excite.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Excite';
				} elseif (stripos($url['host'], 'alexa.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Alexa';
				} elseif (stripos($url['host'], 'a9.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'A9';
				} elseif (stripos($url['host'], 'cuil.com') !== FALSE AND stripos($url['query'], 'q=') !== FALSE)
				{
					self::$engine = 'Cuil Search';
				} else
				{
					self::$engine = 'Unknown';
				}
			} else
			{
				self::$engine = 'Unknown';
			}
		}

		return self::$engine;
	}

	public static function lang()
	{
		if (empty(self::$lang_code))
		{
			$code = array('af', 'ar', 'bg', 'ca', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'gl', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'ko', 'ka', 'lt', 'lv', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr', 'sv', 'th', 'tr', 'uk', 'zh');
			$name = array('Afrikaans', 'Arabic', 'Bulgarian', 'Catalan', 'Czech', 'Danish', 'German', 'Greek', 'English', 'Spanish', 'Estonian', 'Finnish', 'French', 'Galician', 'Hebrew', 'Hindi', 'Croatian', 'Hungarian', 'Indonesian', 'Italian', 'Japanese', 'Korean', 'Georgian', 'Lithuanian', 'Latvian', 'Malay', 'Dutch', 'Norwegian', 'Polish', 'Portuguese', 'Romanian', 'Russian', 'Slovak', 'Slovenian', 'Albanian', 'Serbian', 'Swedish', 'Thai', 'Turkish', 'Ukrainian', 'Chinese');

			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			} elseif (getenv('HTTP_ACCEPT_LANGUAGE'))
			{
				$lang = getenv('HTTP_ACCEPT_LANGUAGE');
			} else
			{
				$lang = 'Unknown';
			}

			$lang = strtolower($lang);
			$agent = strtolower(self::agent());

			foreach ($code as $sub => $result)
			{
				if (strpos($lang, $result) === 0 OR strpos($lang, $result) !== FALSE OR preg_match('/[\[\( ]'.$result.'[;,_\-\)]/', $agent))
				{
					self::$lang_code = $result;
					self::$lang_name = $name[$sub];
					break;
				}
			}
		}

		return self::$lang_code;
	}
}

?>
