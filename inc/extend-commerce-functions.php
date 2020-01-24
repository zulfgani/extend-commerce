<?php
/**
 * Custom functions that act independently of theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Extend_Commerce
 */

if ( ! function_exists( 'extend_commerce_discount_show' ) ) {

	/**
	 * extend_commerce_discount_show - Returns true when display discount is active.
	 *
	 * @return bool
	 */
	function extend_commerce_discount_show() {
		return 'no' !== get_option( 'extend_commerce_show_discount', 'no' );
	}
}

function extend_commerce_on_sale_text( $html ) {
	$sale_string = esc_html( get_option( 'extend_commerce_sale_label' ) );
	
	return str_replace( __( 'Sale!', 'classic-commerce' ), $sale_string, $html );
}
add_filter( 'woocommerce_sale_flash', 'extend_commerce_on_sale_text' );


function extend_commerce_show_sale_percentage() {
   global $product;
   
   if ( ! $product->is_on_sale() || $product->is_type( 'grouped' ) ) return;
   if ( $product->is_type( 'simple' ) ) {
      $max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
   } elseif ( $product->is_type( 'variable' ) ) {
      $max_percentage = 0;
	  $percentage = 0;
      foreach ( $product->get_children() as $child_id ) {
         $variation = wc_get_product( $child_id );
         $price = $variation->get_regular_price();
         $sale = $variation->get_sale_price();
         if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
         if ( $percentage > $max_percentage ) {
            $max_percentage = $percentage;
         }
      }
   }
   if ( $max_percentage > 0 ) echo '<div class="sale-percentage">' . round($max_percentage) . '% <span class="off">off</span></div>'; 
}
if ( extend_commerce_discount_show() ) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'extend_commerce_show_sale_percentage', 25 );
}


function extend_commerce_order_button_text( $button_text ) {
   $order_string = esc_html( get_option( 'extend_commerce_place_order_text' ) );
   
   return $order_string; 
}
add_filter( 'woocommerce_order_button_text', 'extend_commerce_order_button_text' );

function extend_commerce_description_tab_adjustments($tabs) {
    global $product;
	
	$desc_string = esc_html( get_option( 'extend_commerce_description_title' ) );
    if ( ! empty( $desc_string ) ) {
        $tabs['description']['title'] = $desc_string;
    } else {
		$tabs['description']['title'] = 'Description';
	}
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'extend_commerce_description_tab_adjustments', 98);

function extend_commerce_reviews_tab_adjustments($tabs) {
    global $product;
	
	$review_string = esc_html( get_option( 'extend_commerce_review_title' ) );
	
    $get_product_review_count = $product->get_review_count();
    if ( ! empty( $review_string ) && $get_product_review_count == 0 ) {
        $tabs['reviews']['title'] = $review_string;
    } elseif ( ! empty( $review_string ) ) {
        $tabs['reviews']['title'] = $review_string . '('.$get_product_review_count.')';
    } elseif ( empty( $review_string ) ) {
        $tabs['reviews']['title'] = 'Reviews ('.$get_product_review_count.')';
    }
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'extend_commerce_reviews_tab_adjustments', 98);


function extend_commerce_placeholder_img_src( $src, $size = 'woocommerce_thumbnail' ) {
	$src				= CPEC_ASSETS_IMAGES . 'placeholder.jpg';
	$extend_src			= esc_url( get_option( 'extend_commerce_placeholder_image' ) );
	$placeholder_image	= get_option( 'woocommerce_placeholder_image', 0 );
	
	if ( ! empty( $extend_src ) ) {
		$src = $extend_src;
	} elseif ( ! empty( $placeholder_image ) ) {
		if ( is_numeric( $placeholder_image ) ) {
			$image      = wp_get_attachment_image_src( $placeholder_image, $size );

			if ( ! empty( $image[0] ) ) {
				$src = $image[0];
			}
		} else {
			$src = $placeholder_image;
		}
	}
	
	return $src;
	
}
add_filter( 'woocommerce_placeholder_img_src', 'extend_commerce_placeholder_img_src', 10, 1 );


function extend_commerce_placeholder_img( $image_html, $size, $dimensions ){
	$image      = extend_commerce_placeholder_img_src( $size );
	$image_html = '<img src="' . esc_attr( $image ) . '" alt="' . esc_attr__( 'Placeholder Image', 'extend-commerce' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image extend-commerce" height="' . esc_attr( $dimensions['height'] ) . '" />';

	return $image_html;
}
add_filter( 'woocommerce_placeholder_img', 'extend_commerce_placeholder_img', 10, 3 );


if ( ! function_exists( 'extend_commerce_virtual_billing_hide' ) ) {

	/**
	 * extend_commerce_virtual_billing_hide - Returns true when display discount is active.
	 *
	 * @return bool
	 */
	function extend_commerce_virtual_billing_hide() {
		return 'no' != get_option( 'extend_commerce_virtual_billing_hide', 'yes' );
	}
}
function extend_commerce_simplify_checkout_virtual( $fields ) {
    
	$only_virtual = true;

	foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		// Check if there are non-virtual products
		if ( ! $cart_item['data']->is_virtual() ) $only_virtual = false;   
	}

	if( $only_virtual ) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_1']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_country']);
		unset($fields['billing']['billing_state']);
		unset($fields['billing']['billing_phone']);
		//add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
	}

	return $fields;
}
if ( extend_commerce_virtual_billing_hide() ) {
	add_filter( 'woocommerce_checkout_fields' , 'extend_commerce_simplify_checkout_virtual' );
}

/**
 * Radio Button and Select sanitization
 *
 * @param  string		Radio Button value
 * @return integer	Sanitized value
 */
if ( ! function_exists( 'extend_commerce_radio_sanitization' ) ) {
	function extend_commerce_radio_sanitization( $input, $setting ) {
		//get the list of possible radio box or select options
	 $choices = $setting->manager->get_control( $setting->id )->choices;

		if ( array_key_exists( $input, $choices ) ) {
			return $input;
		} else {
			return $setting->default;
		}
	}
}

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );