<?php

/**
 * Mirrors plugin visibility logic from sage_roi_custom_pre_get_posts_query.
 */
function sage_roi_theme_visibility_meta_query_for_current_user() {
    $user_id = get_current_user_id();
    if ( ! $user_id || ! function_exists( 'sage_roi_option_key' ) ) {
        return array();
    }

    return array(
        'relation' => 'AND',
        array(
            'relation' => 'OR',
            array(
                'key'     => sage_roi_option_key( 'hide_from_customers' ),
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => sage_roi_option_key( 'hide_from_customers' ),
                'value'   => ';i:' . (int) $user_id . '[;}]',
                'compare' => 'NOT REGEXP',
            ),
        ),
        array(
            'relation' => 'OR',
            array(
                'key'     => sage_roi_option_key( 'hide_from_users' ),
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => sage_roi_option_key( 'hide_from_users' ),
                'value'   => ';i:' . (int) $user_id . '[;}]',
                'compare' => 'NOT REGEXP',
            ),
        ),
        array(
            'relation' => 'OR',
            array(
                'key'     => sage_roi_option_key( 'show_only_to_users' ),
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => sage_roi_option_key( 'show_only_to_users' ),
                'value'   => '',
                'compare' => '=',
            ),
            array(
                'key'     => sage_roi_option_key( 'show_only_to_users' ),
                'value'   => 'a:0:{}',
                'compare' => '=',
            ),
            array(
                'key'     => sage_roi_option_key( 'show_only_to_users' ),
                'value'   => ';i:' . (int) $user_id . '[;}]',
                'compare' => 'REGEXP',
            ),
        ),
    );
}

function sage_roi_theme_get_visible_product_count_by_category( $category_id, $fallback_count = 0 ) {
    if ( ! class_exists( 'WC_Product_Query' ) ) {
        return (int) $fallback_count;
    }

    $args = array(
        'status'   => 'publish',
        'limit'    => -1,
        'return'   => 'ids',
        'paginate' => false,
        'tax_query' => array(
            array(
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => array( (int) $category_id ),
                'operator'         => 'IN',
                'include_children' => true,
            ),
        ),
    );

    $meta_query = sage_roi_theme_visibility_meta_query_for_current_user();
    if ( ! empty( $meta_query ) ) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WC_Product_Query( $args );
    $ids   = $query->get_products();
    if ( ! is_array( $ids ) ) {
        return (int) $fallback_count;
    }

    $visible_count = 0;
    foreach ( $ids as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( $product && $product->is_visible() ) {
            $visible_count++;
        }
    }

    return $visible_count;
}

function sage_roi_theme_category_link_html( $cat, $product_image = '' ) {
    $term_link     = get_term_link( $cat->slug, 'product_cat' );
    $visible_count = sage_roi_theme_get_visible_product_count_by_category( $cat->term_id, $cat->category_count );

    if ( is_wp_error( $term_link ) ) {
        return '';
    }

    if ( ! empty( $product_image ) ) {
        return '<div class="category-filter-item"><a href="' . esc_url( $term_link ) . '#product-lists" aria-label="' . esc_attr( $cat->name ) . '"><img src="' . esc_url( $product_image ) . '" alt="' . esc_attr( $cat->name ) . '" width="130" height="70" /><span class="cat-product-count">' . (int) $visible_count . '</span></a></div>';
    }

    return '<div class="category-filter-item"><a href="' . esc_url( $term_link ) . '#product-lists"><span class="category-filter-name">' . esc_html( $cat->name ) . '</span><span class="cat-product-count">' . (int) $visible_count . '</span></a></div>';
}

// Add text before price
// function bd_rrp_price_html( $price, $product ) {
//     if(is_shop()){
//         $price = 'Rent from: ' . $price;
//     }
//     return $price;
    
// }
// add_filter( 'woocommerce_get_price_html', 'bd_rrp_price_html', 100, 2 );

function cst_display_invoice_number(){
// 	global $woocommerce, $post;
// 	$cst_post_id = $post->ID;
// 	$order = new WC_Order($cst_post_id);
// 	$order_id = trim(str_replace('#', '', $order->get_order_number()));


	global $woocommerce;
	$product=reset($woocommerce->cart->get_cart());
// 	$string = WC_Order::get_order_number();
// 	$related=ThemexWoo::getRelatedPost($product['product_id'], array('course_product', 'plan_product'), true);
	
// 	return "test".var_dump($product);
}
add_shortcode('cst-display-invoice-number', 'cst_display_invoice_number'); 


function display_sales_events_horizontal(){
    $pages_elements = '<div class="category-filter-section"><div class="category-filter-horizontal" name="category-filter" id="category-filter" onchange="categoryFilter(this)">';

    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title        = '';  
    $empty        = 0;

    $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
            $product_thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true ); 

            $product_image = wp_get_attachment_url( $product_thumbnail_id ); 
            if($cat->name != 'Uncategorized'){
                $pages_elements .= sage_roi_theme_category_link_html( $cat, $product_image );
            }
        }
    }
	
	return $pages_elements.'</div><script>function categoryFilter(x){window.location.href = x.value;} (function(){if(document.body.classList.contains("home")||document.body.classList.contains("front-page")){return;}var normalizePath=function(path){return path.replace(/\/+$/,"")||"/";};var currentPath=normalizePath(window.location.pathname);if(currentPath==="/"){return;}var doc_current_page=document.querySelectorAll("div#category-filter");for(var i=0;i<doc_current_page.length;i++){var all_a_under=doc_current_page[i].querySelectorAll("a");for(var x=0;x<all_a_under.length;x++){all_a_under[x].classList.remove("current-shop-page-active");var linkPath=normalizePath(new URL(all_a_under[x].href,window.location.origin).pathname);if(linkPath===currentPath){all_a_under[x].classList.add("current-shop-page-active");}}}})();</script></div>';
}

add_shortcode('filter-product-horizontal', 'display_sales_events_horizontal'); 


function display_sales_events(){
    $pages_elements = '<div class="category-filter-section"><div name="category-filter" id="category-filter" onchange="categoryFilter(this)"><h4>Select Category</h4>';

    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title        = '';  
    $empty        = 0;

    $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;    
            if($cat->name != 'Uncategorized'){   
                $pages_elements .= sage_roi_theme_category_link_html( $cat );
            }
        }
    }
	
	return $pages_elements.'</div><script>function categoryFilter(x){window.location.href = x.value;} (function(){if(document.body.classList.contains("home")||document.body.classList.contains("front-page")){return;}var normalizePath=function(path){return path.replace(/\/+$/,"")||"/";};var currentPath=normalizePath(window.location.pathname);if(currentPath==="/"){return;}var doc_current_page=document.querySelectorAll("div#category-filter");for(var i=0;i<doc_current_page.length;i++){var all_a_under=doc_current_page[i].querySelectorAll("a");for(var x=0;x<all_a_under.length;x++){all_a_under[x].classList.remove("current-shop-page-active");var linkPath=normalizePath(new URL(all_a_under[x].href,window.location.origin).pathname);if(linkPath===currentPath){all_a_under[x].classList.add("current-shop-page-active");}}}})();</script></div>';
}

add_shortcode('filter-product', 'display_sales_events'); 

// wp_enqueue_style( 'my-style', get_stylesheet_directory_uri() . '/sales-template/style.css', false, '1.0', 'all' ); 

function woocommerce_button_proceed_to_checkout() { ?>
    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
    <?php esc_html_e( 'Submit Order', 'woocommerce' ); ?>
    </a>
    <?php
}
?>
