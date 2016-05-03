<?php
/**
 * Plugin Name: TS Email Obfuscate
 * Plugin URI: http://tecsmith.com.au
 * Description: Creates an [email] shortcode
 * Author: Vino Rodrigues
 * Version: 1.0.1
 * Author URI: http://vinorodrigues.com
**/


// if (!defined('EMAIL_OBFS_PLUGIN_URL'))
// 	define( 'EMAIL_OBFS_PLUGIN_URL', str_replace( ' ', '%20', plugins_url( '', __FILE__ ) ) );


/**
 *  Obfuscate Email
 */
if (!function_exists('ts_obfuscate_email')) :
function ts_obfuscate_email($to, $name = false, $class = false, $rel = false) {
	if (!is_email($to)) return '';
	if (strcmp($to, $name) == 0) $name = false;
	if ($rel === false) $rel = 'nofollow';

	if (true) {  // ----------- Optional second layer obfuscation, html chars ----------
		$never = array('.', '@', '+');  // don't encode as not supported in IE
		$new = '';
		for ($i = 0; $i < strlen($to); $i++) {
			if (!in_array($to[$i], $never)) {
				switch (rand(1, 3)) {
					case 2: $new .= '&#'.ord($to[$i]).';'; break;
					case 3: $new .= '&#x'.dechex(ord($to[$i])).';'; break;
					default: $new .= $to[$i];
				}
			} else {
				$new .= $to[$i];
			}
		}
		$to = $new;
	}

	$tag = '<a href="mailto:' . $to . '" rel="' . $rel . '"';
	if ($class !== false) $tag .= ' class="' . $class . '"';
	$tag .= '>';
	$tag .= ($name !== false) ? $name : $to;
	$tag .= '</a>';

	$tag = str_rot13($tag);  // First layer obfuscation, ROT13 encoding
	$tag = str_replace('"', '\"', $tag);

	$output = '<script type="text/javascript">';
	$output.= '  document.write("' . $tag . '".replace(/[a-zA-Z]/g,';
	$output.= '  function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));';
	$output.= '</script><noscript>&#x1f6ab;</noscript>';

	return $output;
}
endif;


/**
 * Hide email using a shortcode.
 * @param array  $atts    Shortcode attributes.
 * @param string $content The shortcode content. Should be an email address.
 *
 * @return string The obfuscated email address.
 */
function ts_email_obfuscate_shortcode( $atts , $content = null ) {

	extract( shortcode_atts( array(
		'to'    => $content,
		'name'  => $content,
		'class' => false,
        	), $atts, 'email') );

	if (!is_email($to)) return $content;

	return ts_obfuscate_email($to, $name, $class);
}

add_shortcode( 'email', 'ts_email_obfuscate_shortcode' );

// Enable shortcodes in widget text
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
