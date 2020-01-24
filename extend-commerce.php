<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://cpengineered.com
 * @since             1.0.0
 * @package           Extend_Commerce
 *
 * @classicpress-plugin
 * Plugin Name:       Extend Commerce
 * Plugin URI:        http://cpengineered.com
 * Description:       Universal and theme agnostic helper plugin that extens the Classic Commerce plugin without vendor lockin to a specific theme.
 * Version:           0.0.1
 * Author:            CPEngineered
 * Author URI:        http://cpengineered.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       extend-commerce
 */

/* Do not access this file directly */
if ( ! defined( 'WPINC' ) ) { die; }

/* Let's do nothing if Classic Commerce is not active - Like fail gracefully ;) */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'CPEC_VERSION', '1.0.0' );

/* Set constant path to the plugin directory. */
define( 'CPEC_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

/* Set the constant path to the plugin directory URI. */
define( 'CPEC_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

define( 'CPEC_INC_PATH', CPEC_PATH . 'inc/' );
define( 'CPEC_CLASSES_PATH', CPEC_PATH . 'classes/' );
define( 'CPEC_ASSETS_URL', CPEC_URI . 'assets/' );
define( 'CPEC_ASSETS_STYLES', CPEC_ASSETS_URL . 'styles/' );
define( 'CPEC_ASSETS_SCRIPTS', CPEC_ASSETS_URL . 'scripts/' );
define( 'CPEC_ASSETS_IMAGES', CPEC_ASSETS_URL . 'images/' );

/* Load the main plugin Class */
require_once( CPEC_CLASSES_PATH . 'class-extend-commerce.php' );

/* Load the plugin's Functions */
require_once( CPEC_INC_PATH . 'extend-commerce-functions.php' );

/* Add Customizer Options - NOTE: These are options and not theme_mods! */
require_once( CPEC_CLASSES_PATH . 'class-extend-commerce-customizer.php' );

/* The fina touch - Load the plugin apdater Class. Thank you @author John Alarcon */
require_once( CPEC_CLASSES_PATH . 'class-plugin-updater.php' );