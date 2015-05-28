<?php

ether::import('admin.ether-backup');

if ( ! class_exists('ether_panel_ether'))
{
	class ether_panel_ether extends ether_panel
	{
		public static function init()
		{
			if (class_exists('ether_panel_ether_backup'))
			{
				ether_panel_ether_backup::init();
			}
		}

		public static function header()
		{
			if (class_exists('ether_panel_ether_backup'))
			{
				ether_panel_ether_backup::header();
			}
		}

		public static function reset()
		{

		}

		public static function body()
		{
			$branches = ether::branches();
			$branches_info = '';

			foreach ($branches as $branch_name => $branch_data)
			{
				$branches_info .= '<li>'.$branch_data['name'].' '.$branch_data['version'].'</li> ';
			}

			return '<fieldset class="ether-form">
				<h2 class="title">'.ether::langr('Welcome to Ether world').'</h2>
				<hr class="ether-divider">
				<h3 class="title">'.ether::langr('Framework &amp; Plugin info').'</h3>
				<ul style="list-style: disc; padding-left: 20px;">
					<li>'.ether::langr('Ether Framework').' '.ETHER_VERSION.' ('.str_replace(WP_CONTENT_DIR, '', ETHER_FILE).')</li> '.$branches_info.'
				</ul>
				<hr class="ether-divider">
				<h3 class="title">'.ether::langr('Need help?').'</h3>
				<p>'.ether::langr('In case of a need of any further asistance that goes beyond the scope provided by plugin documentation please try the following:').'</p>
				<ul style="list-style: disc; padding-left: 20px;">
					<li>'.ether::langr('Email: <a href="mailto:contact@onether.com">contact@onether.com</a> - Note: When reporting plugin/theme incompatibility please include the following details in your message along with problem description for smoother turnaround:').'
					<ul style="list-style: circle; padding-left: 20px; padding-top: 5px;">
						<li>'.ether::langr('your item purchase code you got after purchasing an item').'</li>
						<li>'.ether::langr('zipped Plugin/Theme files so we can conduct local tests').'</li>
						<li>'.ether::langr('FTP and WP admin login/pass to your installation').'</li>
					</ul>
					</li>
				</ul>
			</fieldset>';
		}

		public static function use_controls()
		{
			return FALSE;
		}
	}
}

?>
