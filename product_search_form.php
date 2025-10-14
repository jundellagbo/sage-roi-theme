<?php
$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
		<div>
			<label class="screen-reader-text" for="s">' . __( 'Search for:', 'woocommerce' ) . '</label>
			<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'My Super Search form', 'woocommerce' ) . '" />
			<input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', 'woocommerce' ) .'" />
			<input type="hidden" name="post_type" value="product" />
		</div>
	</form>';
	
echo $form;

global $avia_config; 

$search_params = apply_filters('avf_frontend_search_form_param', array(

    'placeholder'  	=> __('Search','avia_framework'),
    'search_id'	   	=> 's',
    'form_action'	=> home_url( '/' ),
    'ajax_disable'	=> false
));

$disable_ajax = $search_params['ajax_disable'] == false ? "" : "av_disable_ajax_search";

$icon  = av_icon_char('search');
$class = av_icon_class('search');
?>