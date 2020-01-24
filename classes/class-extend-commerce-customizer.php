<?php
/**
 * Adds options to the customizer for Extend Commerce.
 *
 * @version 1.0.0
 * @package Extend Commerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extend_Commerce_Customizer class.
 */
class Extend_Commerce_Customizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->add_actions();
	}
	
	protected function add_actions() {
		
		add_action( 'customize_register', array( $this, 'add_sections' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 130 );
		
	}

	/**
	 * Add settings to the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_sections( $wp_customize ) {
		$this->add_extend_commerce_section( $wp_customize );
	}

	/**
	 * Extend Commerce section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_extend_commerce_section( $wp_customize ) {
		$wp_customize->add_section(
			'extend_commerce_options',
			array(
				'title'    => __( 'Extend Commerce: Store', 'extend-commerce' ),
				'priority' => 100,
				'panel'    => 'woocommerce',
			)
		);
		
		$wp_customize->add_section(
			'extend_commerce_single_options',
			array(
				'title'    => __( 'Extend Commerce: Single', 'extend-commerce' ),
				'priority' => 110,
				'panel'    => 'woocommerce',
			)
		);

		$wp_customize->add_setting(
			'extend_commerce_sale_label',
			array(
				'default'           => __( 'Sale!', 'extend-commerce' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_sale_label',
			array(
				'label'       => __( 'On Sale Label', 'extend-commerce' ),
				'description' => __( 'Change the text of the On Sale label i.e. Offer!', 'extend-commerce' ),
				'section'     => 'extend_commerce_options',
				'settings'    => 'extend_commerce_sale_label',
				'type'        => 'text',
				'priority'        => 10,
			)
		);
		
		$wp_customize->add_setting(
			'extend_commerce_onsale_position' , array(
				'default'			=> 'default',
				'type'				=> 'option',
				'capability'		=> 'manage_woocommerce',
				'sanitize_callback'	=> 'extend_commerce_radio_sanitization',
			) 
		);

		$wp_customize->add_control(
			'extend_commerce_onsale_position', array(
				'label'		=> __( 'Onsale Label Position', 'extend-commerce' ),
				'section'	=> 'extend_commerce_options',
				'settings'	=> 'extend_commerce_onsale_position',				
				'priority'	=> 20,
				'type'		=> 'radio',
				'choices'	=> array(
					'top-right'	=> __( 'Top Right', 'extend-commerce' ),
					'top-left'	=> __( 'Top Left', 'extend-commerce' ),
					'default'	=> __( 'Default', 'extend-commerce' ),
				),
			)
		);
		
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_sale_label', array(
					'selector'            => 'span.onsale',
					'container_inclusive' => true,
				)
			);
		}

		$wp_customize->add_setting(
			'extend_commerce_show_discount',
			array(
				'default'				=> 'no',
				'type'					=> 'option',
				'capability'			=> 'manage_woocommerce',
				'sanitize_callback'		=> 'wc_bool_to_string',
				'sanitize_js_callback'	=> 'wc_string_to_bool',
				'transport'         	=> 'postMessage',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_show_discount',
			array(
				'label'			=> __( 'Display % Discount Label', 'extend-commerce' ),
				'description'	=> __( 'Select to display the discount in % label for the On Sale products!', 'extend-commerce' ),
				'section'		=> 'extend_commerce_options',
				'settings'		=> 'extend_commerce_show_discount',
				'type'			=> 'checkbox',
				'priority'		=> 30,
			)
		);

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_show_discount', array(
					'selector'            => '.sale-percentage',
					'container_inclusive' => true,
					'render_callback'     => 'extend_commerce_show_discount',
				)
			);
		}
		
		$wp_customize->add_setting(
			'extend_commerce_discount_position' , array(
				'default'			=> 'default',
				'type'				=> 'option',
				'capability'		=> 'manage_woocommerce',
				'sanitize_callback'	=> 'extend_commerce_radio_sanitization',
			) 
		);

		$wp_customize->add_control(
			'extend_commerce_discount_position', array(
				'label'		=> __( 'Discount Label Position', 'extend-commerce' ),
				'section'	=> 'extend_commerce_options',
				'settings'	=> 'extend_commerce_discount_position',				
				'priority'	=> 40,
				'type'		=> 'radio',
				'choices'	=> array(
					'top-right'	=> __( 'Top Right', 'extend-commerce' ),
					'top-left'	=> __( 'Top Left', 'extend-commerce' ),
					'default'	=> __( 'Default', 'extend-commerce' ),
				),
			)
		);
		
		/**
		 * Discount Text Color
		 */
		$wp_customize->add_setting(
			'extend_commerce_discount_color', array(
				'default'           => apply_filters( 'extend_commerce_discount_text_default_color', '#ffffff' ),
				'type'				=> 'option',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 'extend_commerce_discount_color', array(
					'label'           => __( 'Discount text color', 'extend-commerce' ),
					'section'         => 'extend_commerce_options',
					'settings'        => 'extend_commerce_discount_color',
					'priority'        => 40,
				)
			)
		);
		
		/**
		 * Discount Background Color
		 */
		$wp_customize->add_setting(
			'extend_commerce_discount_bg_color', array(
				'default'           => apply_filters( 'extend_commerce_discount_default_background_color', '#d9534f' ),
				'type'				=> 'option',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 'extend_commerce_discount_bg_color', array(
					'label'           => __( 'Discount background color', 'extend-commerce' ),
					'section'         => 'extend_commerce_options',
					'settings'        => 'extend_commerce_discount_bg_color',
					'priority'        => 50,
				)
			)
		);
		
		/*
		 * Additional custom controls
		 */
		
		$wp_customize->add_setting(
			'extend_commerce_virtual_billing_hide',
			array(
				'default'				=> 'no',
				'type'					=> 'option',
				'capability'			=> 'manage_woocommerce',
				'sanitize_callback'		=> 'wc_bool_to_string',
				'sanitize_js_callback'	=> 'wc_string_to_bool',
				'transport' 			=> 'refresh',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_virtual_billing_hide',
			array(
				'label'			=> __( 'Hide address fields', 'extend-commerce' ),
				'description'	=> __( '<br />Hide address billing fields on virtual products! <code>NOTE:<strong>Currently this needs a customizer refresh in order to see the changes!</strong></code>', 'extend-commerce' ),
				'section'		=> 'woocommerce_checkout',
				'settings'		=> 'extend_commerce_virtual_billing_hide',
				'type'			=> 'checkbox',
				'priority'		=> 95,
			)
		);

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_virtual_billing_hide', array(
					'selector'            => '#customer_details',
					'container_inclusive' => true,
					'render_callback'     => 'extend_commerce_virtual_billing_hide',
				)
			);
		}
		
		$wp_customize->add_setting(
			'extend_commerce_place_order_text',
			array(
				'default'           => __( 'Place order', 'extend-commerce' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
				'transport'			=> 'postMessage',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_place_order_text',
			array(
				'label'			=> __( 'Place Order button text', 'extend-commerce' ),
				'description'	=> __( 'Change the text of the Place order button i.e. Submit! <p><code><strong>NOTE:</strong>This option is added by the Extend Commerce plugin & will not be available if it is deactivated!</code></p>', 'extend-commerce' ),
				'section'		=> 'woocommerce_checkout',
				'settings'		=> 'extend_commerce_place_order_text',
				'type'			=> 'text',
				'priority'		=> 100,
			)
		);
		
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_place_order_text', array(
					'selector'            => '#place_order',
					'container_inclusive' => true,
				)
			);
		}

		$wp_customize->add_setting( 'extend_commerce_placeholder_image', array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'manage_woocommerce',
			'sanitize_callback' => 'esc_url_raw',
		));

		// Add Controls
		$wp_customize->add_control( 
			new WP_Customize_Image_Control( 
				$wp_customize, 'extend_commerce_placeholder_image', array(
					'label'		=> __( 'Default Placeholder Image', 'extend-commerce' ),
					'description'	=> __( 'Set a default placeholder image for your store! <p><code><strong>NOTE:</strong>This option is added by the Extend Commerce plugin & will not be available if it is deactivated!</code></p>', 'extend-commerce' ),
					'width' 	=> 800,
					'height' 	=> 800,
					'section'	=> 'woocommerce_product_catalog',
					'settings'	=> 'extend_commerce_placeholder_image',
					'priority'	=> 100,
				)
			)
		);
		
		$wp_customize->add_setting(
			'extend_commerce_description_title',
			array(
				'default'           => __( 'Description', 'extend-commerce' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_description_title',
			array(
				'label'       => __( 'Description Tab Title', 'extend-commerce' ),
				'description' => __( 'Change description tab\'s title', 'extend-commerce' ),
				'section'     => 'extend_commerce_single_options',
				'settings'    => 'extend_commerce_description_title',
				'type'        => 'text',
				'priority'        => 10,
			)
		);
		
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_description_title', array(
					'selector'            => '#tab-title-description',
					'container_inclusive' => true,
				)
			);
		}
		
		$wp_customize->add_setting(
			'extend_commerce_review_title',
			array(
				'default'           => __( 'Reviews', 'extend-commerce' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'extend_commerce_review_title',
			array(
				'label'       => __( 'Reviews Tab Title', 'extend-commerce' ),
				'description' => __( 'Change the Reviews tab\'s title', 'extend-commerce' ),
				'section'     => 'extend_commerce_single_options',
				'settings'    => 'extend_commerce_review_title',
				'type'        => 'text',
				'priority'        => 20,
			)
		);
		
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'extend_commerce_review_title', array(
					'selector'            => '#tab-title-reviews',
					'container_inclusive' => true,
				)
			);
		}
	}
	
	/**
	 * Returns an array of the desired default Extend Commerce Options
	 *
	 * @return array
	 */
	public function get_extend_commerce_default_option_values() {
		return apply_filters(
			'extend_commerce_default_option_values', $args = array(
				'extend_commerce_discount_text_default_color'		=> '#ffffff',
				'extend_commerce_discount_default_background_color'	=> '#d9534f',
			)
		);
	}

	/**
	 * Adds a value to each Extend Commerce setting if one isn't already present.
	 *
	 * @uses get_extend_commerce_default_option_values()
	 */
	public function default_plugin_option_values() {
		foreach ( $this->get_extend_commerce_default_option_values() as $opt => $val ) {
			add_filter( 'plugin_opt_' . $opt, array( $this, 'get_plugin_opt_value' ), 10 );
		}
	}

	/**
	 * Get plugin opt value.
	 *
	 * @param string $value Theme modification value.
	 * @return string
	 */
	public function get_plugin_opt_value( $value ) {
		$key = substr( current_filter(), 10 );

		$set_plugin_opt = get_options();

		if ( isset( $set_plugin_opt[ $key ] ) ) {
			return $value;
		}

		$values = $this->get_extend_commerce_default_option_values();

		return isset( $values[ $key ] ) ? $values[ $key ] : $value;
	}

	/**
	 * Set Customizer setting defaults.
	 * These defaults need to be applied separately as child themes can filter storefront_setting_default_values
	 *
	 * @param  array $wp_customize the Customizer object.
	 * @uses   get_storefront_default_setting_values()
	 */
	public function edit_default_customizer_settings( $wp_customize ) {
		foreach ( $this->get_extend_commerce_default_option_values() as $opt => $val ) {
			$wp_customize->get_setting( $opt )->default = $val;
		}
	}
	
	/**
	 * Get all of the Extend Commerce options.
	 *
	 * @return array $extend_commerce_options The Extend Commerce Options.
	 */
	public function get_extend_commerce_options() {
		$extend_commerce_options = array(
			'discount_text_color'		=> get_option( 'extend_commerce_discount_color' ),
			'discount_background_color'	=> get_option( 'extend_commerce_discount_bg_color' ),
		);

		return apply_filters( 'extend_commerce_options', $extend_commerce_options );
	}
	
	public function set_onsale_label_position() {
		$onsale_label_position = get_option( 'extend_commerce_onsale_position', 'default' );
		
		if ( $onsale_label_position === 'top-right' ) {
			
			$onsale_css = '.woocommerce ul.products li.product .onsale {top:0;right:0;left:auto;display:inline-block;position:absolute;}';
			
		} elseif ( $onsale_label_position === 'top-left' ) {
			
			$onsale_css = '.woocommerce ul.products li.product .onsale {top:0;right:auto;left:0;display:inline-block;position:absolute;}';
			
		} elseif ( $onsale_label_position === 'default' ) {
			
			$onsale_css = '.woocommerce ul.products li.product .onsale {display:inline-block;position:relative;}';
			
		}
		return $onsale_css;
	}
	
	public function set_discount_label_position() {
		
		$discount_label_position = get_option( 'extend_commerce_discount_position', 'default' );
		if ( $discount_label_position === 'top-right' ) {
			
			$position_css = 'position:absolute;top:0;right: 0;';
			
		} elseif ( $discount_label_position === 'top-left' ) {
			
			$position_css = 'position:absolute;top:0;right:auto;left:0;';
			
		} elseif ( $discount_label_position === 'default' ) {
			
			$position_css = 'position:relative;';
			
		}
		return $position_css;
		
	}
	
	/**
	 * Get Customizer css.
	 *
	 * @see get_extend_commerce_options()
	 * @return array $styles the css
	 */
	public function get_css() {
		$extend_commerce_options = $this->get_extend_commerce_options();
		
		$onsale_position_css = $this->set_onsale_label_position();
		
		$discount_position_css = $this->set_discount_label_position();

		$styles = '
			.sale-percentage {color:' . $extend_commerce_options['discount_text_color'] . ';background-color:' . $extend_commerce_options['discount_background_color'] . ';' . $discount_position_css . '}
			' . $onsale_position_css . '
		';

		return apply_filters( 'extend_commerce_customizer_css', $styles );
	}
	
	/**
	 * Add CSS in <head> for styles handled by the customizer
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_customizer_css() {
		wp_add_inline_style( 'extend-commerce-frontend', $this->get_css() );
	}

}

new Extend_Commerce_Customizer();
