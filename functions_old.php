
<?php

// <site content path>/wp-content/themes/<theme name>
$theme_info = wp_get_theme();
define('STM_THEME_VERSION', (WP_DEBUG) ? time() : $theme_info->get('Version'));
define('STM_MS_SHORTCODES', '1');

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

if (defined('STM_LMS_URL')) {
    require_once($inc_path . '/lms/main.php');
}
function stm_glob_pagenow()
{
    global $pagenow;
    return $pagenow;
}
function stm_glob_wpdb()
{
    global $wpdb;
    return $wpdb;
}

if (class_exists('BuddyPress')) {
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
add_filter('gettext', 'change_woocommerce_return_to_shop_text', 20, 3);

function change_woocommerce_return_to_shop_text($translated_text, $text, $domain)
{
    switch ($translated_text) {
        case 'Return to shop':
            $translated_text = __('Return to Courses', 'woocommerce');
            break;
    }
    return $translated_text;
}

// Change return to shop URL

function wc_empty_cart_redirect_url()
{
    return 'https://www.ttl.academy/courses/';
}
add_filter('woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url');


//Limit Cart number to 1

add_filter('woocommerce_add_to_cart_validation', 'only_one_items_allowed_add_to_cart', 10, 3);

function only_one_items_allowed_add_to_cart($passed, $product_id, $quantity)
{
    $cart_items_count = WC()->cart->get_cart_contents_count();

    if ($cart_items_count >= 1) {
        // Set to false
        $cart_items_count = 1;
        $passed = false;
        // Display a message
        //wc_add_notice( __( "You can’t have more than 1 same course in cart", "woocommerce" ), "error" );
    }
    return $passed;
}

add_filter('woocommerce_update_cart_validation', 'only_one_items_allowed_cart_update', 10, 4);
function only_one_items_allowed_cart_update($passed, $cart_item_key, $values, $updated_quantity)
{
    $cart_items_count = WC()->cart->get_cart_contents_count();
    $original_quantity = $values['quantity'];
    $total_count = $cart_items_count - $original_quantity + $updated_quantity;

    if ($cart_items_count > 1 || $total_count > 1) {
        // Set to false
        if ($updated_quantity > 1) {
            $passed = false;

            // Display a message
            wc_add_notice(__("You can’t add more than one same course in cart", "woocommerce"), "error");
        }
    }
    return $passed;
}

add_action('woocommerce_before_calculate_totals', 'change_cart_item_quantities', 20, 1);
function change_cart_item_quantities($cart)
{

    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    // HERE below define your specific products IDs

    $new_qty = 1;
    // Checking cart items
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        //$product_id = $cart_item['data']->get_id();
        // Check for specific product IDs and change quantity
        // if( in_array( $product_id, $specific_ids ) && $cart_item['quantity'] != $new_qty ){
        $cart->set_quantity($cart_item_key, $new_qty); // Change quantity
    }
    //}
}


function defer_parsing_of_js($url)
{
    if (is_user_logged_in()) return $url; //don't break WP Admin
    if (FALSE === strpos($url, '.js')) return $url;
    if (strpos($url, 'jquery.js')) return $url;
    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);


//Specific Content on pages based on user
// it does not work for media file.
// add_action('template_redirect', function () {

//     if (is_user_logged_in() && current_user_can('read')) {
//         //Show something
//     } else {
//         if (is_user_logged_in()) {
//             //                         // Show something else
//         } else {
//             wp_redirect(site_url('/lms-login'));
//         }
//     }
// });




add_action( 'template_redirect', 'bn_rescrict_backend' );
function bn_rescrict_backend(){
  if( ! current_user_can( 'manage_options') && is_admin() )  {
    wp_redirect( home_url() );
    exit;
  }
}

?>
