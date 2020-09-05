<?php
$theme_info = wp_get_theme();
define('STM_THEME_VERSION', ( WP_DEBUG ) ? time() : $theme_info->get( 'Version' ) );
define('STM_MS_SHORTCODES', '1' );

$inc_path = get_template_directory() . '/inc';

$widgets_path = get_template_directory() . '/inc/widgets';
// Theme setups

// Custom code and theme main setups
require_once($inc_path . '/setup.php');

// Enqueue scripts and styles for theme
require_once($inc_path . '/scripts_styles.php');

/*Theme configs*/
require_once($inc_path . '/theme-config.php');

// Visual composer custom modules
if (defined('WPB_VC_VERSION')) {
        require_once($inc_path . '/visual_composer.php');
}

// Custom code for any outputs modifying
//require_once($inc_path . '/payment.php');
require_once($inc_path . '/custom.php');

// Custom code for woocommerce modifying
if (class_exists('WooCommerce')) {
        require_once($inc_path . '/woocommerce_setups.php');
}

if(defined('STM_LMS_URL')) {
        require_once($inc_path . '/lms/main.php');
}
function stm_glob_pagenow(){
    global $pagenow;
    return $pagenow;
}
function stm_glob_wpdb(){
    global $wpdb;
    return $wpdb;
}

if(class_exists('BuddyPress')) {
    require_once($inc_path . '/buddypress.php');
}

//Announcement banner
if (is_admin()) {
        require_once($inc_path . '/admin/generate_styles.php');
        require_once($inc_path . '/admin/admin_helpers.php');
        require_once($inc_path . '/admin/review/review-notice.php');
        require_once($inc_path . '/admin/product_registration/admin.php');
        require_once($inc_path . '/tgm/tgm-plugin-registration.php');
}

// Change return to shop text
add_filter( 'gettext', 'change_woocommerce_return_to_shop_text', 20, 3 );

function change_woocommerce_return_to_shop_text( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
        case 'Return to shop' :
                $translated_text = __( 'Return to Courses', 'woocommerce' );
                break;
        }
        return $translated_text;
}

// Change return to shop URL

function wc_empty_cart_redirect_url() {
    return 'https://www.ttl.academy/courses/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );


//Limit Cart number to 1

add_filter( 'woocommerce_add_to_cart_validation', 'only_one_items_allowed_add_to_cart', 10, 3 );

function only_one_items_allowed_add_to_cart( $passed, $product_id, $quantity ) {
    $cart_items_count = WC()->cart->get_cart_contents_count();
    $total_count = $cart_items_count + $quantity;

    if( $cart_items_count >= 1 || $total_count >= 1 ){
        // Set to false
        $passed = false;
        // Display a message
        wc_add_notice( __( "You can’t have more than 1 same course in cart", "woocommerce" ), "error" );
    }
    return $passed;
}

add_filter( 'woocommerce_update_cart_validation', 'only_one_items_allowed_cart_update', 10, 4 );
function only_one_items_allowed_cart_update( $passed, $cart_item_key, $values, $updated_quantity ) {
    $cart_items_count = WC()->cart->get_cart_contents_count();
    $original_quantity = $values['quantity'];
    $total_count = $cart_items_count - $original_quantity + $updated_quantity;

    if( $cart_items_count > 1 || $total_count > 1 ){
            // Set to false
            if( $updated_quantity > 1 ){
                $passed = false;

            // Display a message
                wc_add_notice( __( "You can’t update more than 1 items in cart", "woocommerce" ), "error" );
            }
    }
    return $passed;
}
