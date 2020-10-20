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

require_once($inc_path . '/elementor.php');

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

// //init hook
add_action( 'init', 'file_init' );
function file_init() {
    if ($_REQUEST[ 'dwnld_file' ] != '' ) {
        if ( ! is_user_logged_in() ) { // if not logged-in
            //auth_redirect(); //redirect to login page
            wp_redirect( site_url( '/lms-login' ) );   // or some other page
            exit;
        }
        else {
            check_download_file( $_REQUEST[ 'dwnld_file' ] ); // if logged-in pass file to download
        }
    }
}


//function to download file
function check_download_file( $file ) {
    $upload = wp_upload_dir();
    $file = $upload[ 'basedir' ] . '/houses/' . $file;
    if ( !is_file( $file ) ) {
        status_header( 404 );
        die( 'File not found.' );
    }
    else {
        $mime = wp_check_filetype( $file ); 
        if( false === $mime[ 'type' ] && function_exists( 'mime_content_type' ) )
            $mime[ 'type' ] = mime_content_type( $file );

        if( $mime[ 'type' ] )
        {
            $mimetype = $mime[ 'type' ];        

            header( 'Content-type: ' . $mimetype );
            $last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file ) );
            $etag = '"' . md5( $last_modified ) . '"';
            header( "Last-Modified: $last_modified GMT" );
            header( 'ETag: ' . $etag );
            header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );

            readfile( $file );
            die();
        }
    }
}