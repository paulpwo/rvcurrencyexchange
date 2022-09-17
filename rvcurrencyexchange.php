<?php

/**
 * rvcurrencyexchange
 *
 * @package     rvcurrencyexchange
 * @author      Paul Osinga
 * @copyright   2020 rvcurrencyexchange
 * @license     GPL-2.0
 *
 * Plugin Name: rvcurrencyexchange
 * Description: Plugin to display currency exchange rates
 * Version:     0.1.0
 * Author:      Paul Osinga
 * Text Domain: rvcurrencyexchange
 * License:     GPL-2.0
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

// Plugin Defines
define( "RVCURRENCY_FILE", __FILE__ );
define( "RVCURRENCY_DIR", dirname(__FILE__) );
define( "RVCURRENCY_INCLUDE_DIR", dirname(__FILE__) . '/include' );
define( "RVCURRENCY_DIR_BASENAME", plugin_basename( __FILE__ ) );
define( "RVCURRENCY_DIR_PATH", plugin_dir_path( __FILE__ ) );
define( "RVCURRENCY_DIR_URL", plugins_url( null, __FILE__ ) );

// Require the main class file
require_once( dirname(__FILE__) . '/include/class-main.php' );


add_shortcode('rvcurrencyexchange', function($atts){
    $p = shortcode_atts(array(
        'type' => 'usa_purchase', // usa_purchase, usa_sale, eu_purchase, eu_sale, bri_purchase, bri_sale
        'symbol' => '$', // $, €, ¢    
        'duration' => 2000,
    ), $atts);
    $settings = get_option('rvcurrencyexchange_main_options');
    $type = $p['type'];
    $symbol = $p['symbol'];
    $amount = $settings[$type];
    $duration = $p['duration'];
    echo "<span class='rvcurrencyexchange amount count' 
            data-amount='$amount'
            data-duration='$duration' 
            style='display:none;'>0</span> <span class='rvcurrencyexchange symbol'>$symbol</span>";
}); //shortcode
