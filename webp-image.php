<?php defined('ABSPATH') or die('No script kiddies please!');
/*
Plugin Name: WebP Image
Plugin URI: http://wordpress.org/plugins/webp-image/
Description: A simple plugin that will facilitate the use of WebP images on your website.
Author: RapidDev
Author URI: https://rdev.cc/
License: MIT
License URI: https://opensource.org/licenses/MIT
Version: 1.2.0
Text Domain: rdev_webp
Domain Path: /languages
*/
/**
 * @package WordPress
 * @subpackage WebP
 *
 * @author RapidDev
 * @copyright Copyright (c) 2019-2020, RapidDev
 * @link https://www.rdev.cc/webp
 * @license https://opensource.org/licenses/MIT
 */

/* ====================================================================
 * Constants
 * ==================================================================*/
	define( 'RDEV_WEBP_NAME', 'WebP Image' );
	define( 'RDEV_WEBP_DOMAIN', 'rdev_webp' );
	define( 'RDEV_WEBP_PATH', plugin_dir_path( __FILE__ ) );
	define( 'RDEV_WEBP_BASENAME', plugin_basename( __FILE__ ) );
	define( 'RDEV_WEBP_WP_VERSION', '4.9.0' );
	define( 'RDEV_WEBP_PHP_VERSION', '5.6.28' );

/* ====================================================================
 * Plugin class
 * ==================================================================*/
	if ( is_file( RDEV_WEBP_PATH . 'assets/class.php' ) )
	{
		include RDEV_WEBP_PATH . 'assets/class.php';

		//Init plugin
		( new RDEV_WEBP() );
	}
?>