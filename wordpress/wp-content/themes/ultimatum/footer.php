<?php
/*
 WARNING: This file is part of the core Ultimatum framework. DO NOT edit
 this file under any circumstances.
 */

/**
 *
 * This file is a core Ultimatum file and should not be edited.
 *
 * @package  Ultimatum
 * @author   Wonder Foundry http://www.wonderfoundry.com
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     http://ultimatumtheme.com
 * @version 2.50
 */
do_action('ultimatum_print_footer');
wp_footer();
do_action('ultimatum_before_body_close');
if(get_ultimatum_option('scripts', 'footer_scripts')){ 
	echo stripslashes(get_ultimatum_option('scripts', 'footer_scripts'));
}
echo "\n";
?>
<?php 
/*
global $ultimatumlayout;
echo'<pre>';
print_r($ultimatumlayout);
echo'</pre>';
*/
?>
</body>
</html>
