<?php

require_once get_stylesheet_directory() . '/vendor/autoload.php';

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION
// 


// Display the sku below cart item name
add_filter( 'woocommerce_cart_item_name', 'display_sku_after_item_name', 5, 3 );
function display_sku_after_item_name( $item_name, $cart_item, $cart_item_key ) {
    $product = $cart_item['data']; // The WC_Product Object

    if( is_cart() && $product->get_sku() ) {
        $item_name .= '<br><span class="cust-item-sku">'. $product->get_sku() .  '</span>';		
        
    }
    return $item_name;
}

// Display the options-include
// add_filter( 'woocommerce_cart_item_price', 'display_sku_after_item_price', 5, 3 );
// function display_sku_after_item_price( $item_name, $cart_item, $cart_item_key ) {
//     $product = $cart_item['data']; // The WC_Product Object

//     if( is_cart() && $product->get_sku() ) {
//         // $item_name .= '<br><span class="cust-item-sku">'. $product->get_sku() .  '</span>';
//         $item_name .= '<br><span class="cust-item-ar">'. $product->get_attribute("options") .  '</span>';
// 		// $item_name .= '<br><span class="cust-item-prod">'. $product .  '</span>';
//         $item_name .= '<br><span class="cust-item-ave-hand">'. $product->get_stock_quantity() .  '</span>';
        
//     }
//     return $item_name;
// }


// Add min value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_min', 'min_decimal');
function min_decimal($val) {
    return 0.1;
}

// Add step value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_step', 'nsk_allow_decimal');
function nsk_allow_decimal($val) {
    return 0.1;
}

// Removes the WooCommerce filter, that is validating the quantity to be an int
remove_filter('woocommerce_stock_amount', 'intval');

// Add a filter, that validates the quantity to be a float
add_filter('woocommerce_stock_amount', 'floatval');


// Add quantity in checkout page
// add_filter( 'woocommerce_checkout_cart_item_quantity', 'qty_input_field_on_checkout', 20, 3 );
// function qty_input_field_on_checkout( $quantity_html, $cart_item, $cart_item_key ) {
//     $_product = $cart_item['data'];

//     if ( $_product->is_sold_individually() ) {
//         $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
//     } else {
//         $product_quantity = woocommerce_quantity_input(
//             array(
//                 'input_name'   => "cart[{$cart_item_key}][qty]",
//                 'input_value'  => $cart_item['quantity'],
//                 'max_value'    => $_product->get_max_purchase_quantity(),
//                 'min_value'    => '0',
//                 'product_name' => $_product->get_name(),
//             ),
//             $_product,
//             false
//         );
//     }

//     return '<br><span class="product-quantity"><strong>' . __( 'Qty') . ': </strong></span>' . $product_quantity;
// }

// add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
// function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
//     if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
//         $html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
//         $html .= woocommerce_quantity_input( array(), $product, false );
//         $html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
//         $html .= '</form>';
//     }
//     return $html;
// }


include('custom-shordcodes.php');

//Add category in all products
function category_single_product(){
    $product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );

    if ( $product_cats && ! is_wp_error ( $product_cats ) ){

        $single_cat = array_shift( $product_cats ); ?>

        <h4 itemprop="name" class="product_category_title"><span><?php echo $single_cat->name; ?></span></h4>

<?php }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'category_single_product', 25 );

/**
 * @snippet       Allow Order Edit @ Custom Status
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 7
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
add_filter( 'wc_order_is_editable', 'bbloomer_custom_order_status_editable', 9999, 2 );
function bbloomer_custom_order_status_editable( $allow_edit, $order ) {
    if ( $order->get_status() === 'processing' ) {
        $allow_edit = true;
    }
    return $allow_edit;
}



add_filter( 'woocommerce_quantity_input_args', 'bloomer_woocommerce_quantity_changes', 9999, 2 );
   
function bloomer_woocommerce_quantity_changes( $args, $product ) {
   $args['min_value'] = 1;
   $args['step'] = 1;
   $args['input_value'] = 1;
   return $args;
   
}