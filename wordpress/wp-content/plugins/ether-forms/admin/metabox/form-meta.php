<?php

if ( ! class_exists('ether_metabox_form_meta'))
{
	class ether_metabox_form_meta extends ether_metabox
	{
		public static function init()
		{

		}

		public static function header()
		{

		}

		public static function save($post_id)
		{

		}

		public static function body()
		{
			global $post;
			global $post_type;

			$body = '';

			$tmp_post = NULL;

			if (isset($_GET['post']) AND $_GET['post'] != $post->ID)
			{
				$tmp_post = $post;

				$post = get_post($_GET['post']);
			}

			$id = $post->ID;

			$body .= '<fieldset class="ether-form"><div class="cols-1"><div class="col">';

			$body .= ether_form::get_entry_output($id, FALSE, TRUE);

			$body .= '</div></div></fieldset>';

			if ($tmp_post != NULL)
			{
				$post = $tmp_post;
			}

			return $body;
		}
	}
}

?>
