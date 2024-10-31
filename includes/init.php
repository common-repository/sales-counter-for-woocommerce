<?php
/**
 * Main File
 *
 * @package sales-counter-for-woocommerce\includes
 * @version 1.0.1
 */


// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


function sales_counter_for_wc_get_total_sales_per_product($product_id ='') { 
    global $wpdb;
    $post_status = array('wc-completed');
     
    $order_items = $wpdb->get_row( $wpdb->prepare(" SELECT SUM( order_item_meta.meta_value ) as _qty, SUM( order_item_meta_3.meta_value ) as _line_total FROM {$wpdb->prefix}woocommerce_order_items as order_items

    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_3 ON order_items.order_item_id = order_item_meta_3.order_item_id
    LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID

    WHERE posts.post_type = 'shop_order'            
    AND posts.post_status IN ( '".implode( "','", apply_filters( '', $post_status ) )."' )
    AND order_items.order_item_type = 'line_item'
    AND order_item_meta.meta_key = '_qty'
    AND order_item_meta_2.meta_key = '_product_id'
    AND order_item_meta_2.meta_value = %s
    AND order_item_meta_3.meta_key = '_line_total'

    GROUP BY order_item_meta_2.meta_value

    ", $product_id));
    return $order_items;
}

// Column function
function sales_counter_for_wc_custom_columns_list( $columns ) {
    $columns['wc_sales_counter_sales']  = esc_html__( 'Sales', 'sales-counter-for-woocommerce' );
    $columns['wc_sales_counter_earnings']   = esc_html__( 'Earnings', 'sales-counter-for-woocommerce' );
    return $columns;
}
add_filter( 'manage_product_posts_columns', 'sales_counter_for_wc_custom_columns_list' );

// Column callback function
function sales_counter_for_wc_custom_columns_display_list( $column, $post_id ) {
    global $post; $wpdb;
    $post_id = $post->ID;

    $order_items = sales_counter_for_wc_get_total_sales_per_product( $post_id );
    $items_sold_total = (isset($order_items) ? absint($order_items->_line_total) : 0);

    switch ( $column ) {
        case 'wc_sales_counter_sales' :
            $tp_count_sales         = get_post_meta( $post_id,'total_sales', true );
            $tp_count_sales_text    = sprintf ( _n( '%s Sale', '%s Sales', $tp_count_sales, 'sales-counter-for-woocommerce' ), number_format_i18n( $tp_count_sales ) );
            echo esc_html($tp_count_sales_text);
        break;
        case 'wc_sales_counter_earnings' :
            echo wc_price(  $items_sold_total, 'sales-counter-for-woocommerce' );
        break;
    }
}
add_action( 'manage_product_posts_custom_column' , 'sales_counter_for_wc_custom_columns_display_list', 10, 2 );

function wc_scm_product_sold_count() {
    global $product;

    $sales_counter_enable_check = get_option( 'sales_counter_enable_check' ) ? get_option( 'sales_counter_enable_check' ) : '';
    $sales_counter_enable_title = get_option( 'sales_counter_enable_title' ) ? get_option( 'sales_counter_enable_title' ) : '';
    $salesCounterText           = ( $sales_counter_enable_title!='' ) ? $sales_counter_enable_title : 'Sales';
    $sales_counter_wc_select_option = get_option( 'sales_counter_wc_select_option', '1' ); // Default position: After product title

    $units_sold = get_post_meta( get_the_ID(), 'total_sales', true );

    if($sales_counter_enable_check == 1){
        echo '<div class="wc-scm"><div class="wc-scm-inner has-text-align-center">' . sprintf( __( '<span class="wc-scm-count">%s</span> <span class="wc-scm-text">%s</span>', 'woocommerce' ), $units_sold,$salesCounterText ) . '</div></div>';
    }
}

$sales_counter_wc_select_option = get_option( 'sales_counter_wc_select_option', '1' );

switch ($sales_counter_wc_select_option) {
    case '1':
        add_action( 'woocommerce_after_shop_loop_item_title', 'wc_scm_product_sold_count' );
        break;
    case '2':
        add_action( 'woocommerce_before_shop_loop_item_title', 'wc_scm_product_sold_count' );
        break;
    default:
        add_action( 'woocommerce_after_shop_loop_item', 'wc_scm_product_sold_count' );
        break;
}

// add_action( 'woocommerce_after_shop_loop_item', 'wc_scm_product_sold_count' );
// add_action( 'woocommerce_after_shop_loop_item_title', 'wc_scm_product_sold_count' );
// add_action( 'woocommerce_before_shop_loop_item_title', 'wc_scm_product_sold_count' );
// add_action( 'woocommerce_shop_loop_item_title', 'wc_scm_product_sold_count' );

function wc_scm_product_sold_single_page_count() {
    global $product;

    $sales_counter_single_enable_check = get_option( 'sales_counter_single_enable_check' ) ? get_option( 'sales_counter_single_enable_check' ) : '';
    $sales_counter_enable_title = get_option( 'sales_counter_enable_title' ) ? get_option( 'sales_counter_enable_title' ) : '';
    $salesCounterText           = ( $sales_counter_enable_title!='' ) ? $sales_counter_enable_title : 'Sales';
    $sales_counter_wc_select_single_option = get_option( 'sales_counter_wc_select_single_option', '1' ); // Default position: After product title

    $units_sold = get_post_meta( get_the_ID(), 'total_sales', true );

    if($sales_counter_single_enable_check == 1){
        echo '<div class="wc-scm"><div class="wc-scm-inner">' . sprintf( __( '<span class="wc-scm-count">%s</span> <span class="wc-scm-text">%s</span>', 'woocommerce' ), $units_sold,$salesCounterText ) . '</div></div>';
    }
}

$sales_counter_wc_select_single_option = get_option( 'sales_counter_wc_select_single_option', '1' );

switch ($sales_counter_wc_select_single_option) {
    case '1':
        add_action( 'woocommerce_after_add_to_cart_button', 'wc_scm_product_sold_single_page_count' );
        break;
    case '2':
        add_action( 'woocommerce_before_add_to_cart_button', 'wc_scm_product_sold_single_page_count' );
        break;
    default:
        add_action( 'woocommerce_single_product_summary', 'wc_scm_product_sold_single_page_count' );
        break;
}