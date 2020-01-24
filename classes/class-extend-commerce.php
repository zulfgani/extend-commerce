<?php

defined( 'ABSPATH' ) || exit;

class Extend_Commerce {

	/**
	 * Path to the plugin directory
	 *
	 * @param string
	 */
	static $plugin_dir;

	/**
	 * URL to the plugin
	 *
	 * @param string
	 */
	static $plugin_url;

	// Add actions and filters to this method.
	public function __construct() {
		
		$this->add_actions();		
		
	}
	
	protected function add_actions() {
		
		add_action( 'init', array( $this, 'load_textdomain' ) );
		
		//add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ], 130 );
	}
	
	/**
	 * Load the plugin textdomain for localistion
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'extend-commerce' );
	}
	
	public function enqueue_frontend_assets() {
		$min = SCRIPT_DEBUG ? '' : 'min.';
		wp_enqueue_style( 
			'extend-commerce-frontend', 
			CPEC_ASSETS_STYLES . 'frontend.css', 
			[], 
			CPEC_VERSION
		);
	}
	
}

new Extend_Commerce;