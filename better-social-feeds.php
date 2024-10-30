<?php
/**
* Plugin Name: Social Feeds for Threads
* Description: Display feeds from your Threads profile on your WordPress website.
* Version: 1.0.1
* Author: Better Social Feeds
* Author URI: https://feedsforthreads.com/
* License: GPL+2
* Text Domain: better-social-feeds
* Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}

add_action( 'init', 'fftmj_init_plugin', 1 );
if (!function_exists('fftmj_init_plugin')) {
	function fftmj_init_plugin(){
		define ( 'FFTMJ_PLUGIN_DIR', plugin_dir_path(__FILE__ ) );
		global $fftmj_plugin_url, $fftmj_threads_api_url;
		$fftmj_plugin_url = plugin_dir_url( __FILE__ );
		$fftmj_threads_api_url = 'https://graph.threads.net/v1.0/';
		
		add_action( 'wp_enqueue_scripts', 'fftmj_styles_scripts' );
		add_action( 'admin_enqueue_scripts', 'fftmj_admin_script' );

		// Other files
		include(plugin_dir_path(__FILE__ ) . 'admin/get-access-token.php');
		include(plugin_dir_path(__FILE__ ) . 'admin/settings.php');
		
		include(plugin_dir_path(__FILE__ ) . 'shortcode.php');
		
		load_plugin_textdomain( 'better-social-feeds', false, 'better-social-feeds' );
	}
}

// Back-end assets
if (!function_exists('fftmj_admin_script')) {
	function fftmj_admin_script(){
		wp_enqueue_style(
			'fftmj-admin-style',
			plugin_dir_url( __FILE__ ) . 'admin/settings.css'
		);
		wp_enqueue_script(
			'fftmj-admin-script',
			plugins_url('admin/settings.js',__FILE__ ),
			array('jquery')
		);
	}
}

if (!function_exists('fftmj_styles_scripts')) {
	function fftmj_styles_scripts(){
		
		wp_register_style(
			'fftmj-style',
			plugin_dir_url( __FILE__ ) . 'css/style.css'
		);
		
	}
}

if(is_admin()){
	// Plugin Configuration Page
	add_action( 'plugins_loaded', 'fftmj_set_admin_menu' );
	if (!function_exists('fftmj_set_admin_menu')) {
		function fftmj_set_admin_menu(){
			add_action('admin_menu', 'fftmj_admin_config', 999);
		}
	}
	
	if (!function_exists('fftmj_admin_config')) {
		function fftmj_admin_config() {
			add_menu_page('Better Social Feeds', 'Better Feeds', 'manage_options', 'fftmj-free', 'fftmj_config_callback', 'dashicons-text');
			add_submenu_page('fftmj-free', 'Settings', 'Settings', 'manage_options', 'fftmj-free', 'fftmj_config_callback', 1);
		}
	}
}
