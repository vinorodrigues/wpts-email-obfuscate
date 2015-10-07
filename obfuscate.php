<?php
/**
 * Plugin Name: TS eMail Obfuscate
 * Plugin URI: http://tecsmith.com.au
 * Description: Creates an [email] shortcode
 * Author: Vino Rodrigues
 * Version: 1.0.0
 * Author URI: http://vinorodrigues.com
**/

// Small fix to work arround windows and virtual paths while in dev env.
if ( defined('WP_DEBUG') && WP_DEBUG )
	define( 'FAVICON_PLUGIN_URL', plugins_url() . '/ts-email-obfuscate' );
if (!defined('FAVICON_PLUGIN_URL'))
	define( 'FAVICON_PLUGIN_URL', plugins_url( '', __FILE__ ) );

/**
 * Hide email using a shortcode.
 * @param array  $atts    Shortcode attributes.
 * @param string $content The shortcode content. Should be an email address.
 *
 * @return string The obfuscated email address. 
 */
function ts_email_obfuscate_shortcode( $atts , $content = null ) {

	extract( shortcode_atts( array(
		'email' => $content,
		'name' => $content,
        ), $atts, 'email') );

	if (!is_email($email)) return $content;
    if (strcmp($email, $name) == 0) $name = FALSE;
	
	$pos = strpos( $email, '@' );
	$pre = substr( $email, 0, $pos);
    $dom = substr( $email, $pos+1);
    
    $ret = '<script language="JavaScript">' . PHP_EOL;
    $ret .= '  var ema = \'' . $pre . '\' + String.fromCharCode(64);' . PHP_EOL;
    $ret .= '  ema = ema + \'' . $dom . '\';' . PHP_EOL;
    $ret .= '  document.write(\'<a href="mailto:\'); document.write(ema); document.write(\'">';
    if (!$name) {
        $ret .= '\' + ema + \'';
    } else {
        $ret .= $name;
    }
    $ret .= '</a>\');' . PHP_EOL;
    $ret .= '</script>';
	return $ret;
}
    
add_shortcode( 'email', 'ts_email_obfuscate_shortcode' );

// Enable shortcodes in widget text
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );

/*
<script language="JavaScript">// < ![CDATA[
var ema = 'jack' + String.fromCharCode(64);
              
              
// ]]></script>
*/