<? php

add_action( 'init', 'file_init' );
function file_init() {
    if ($_REQUEST[ 'dwnld_file' ] != '' ) {
        if ( ! is_user_logged_in() ) { 
            
            wp_redirect( site_url( '/lms-login' ) );   
            exit;
        }
        else {
            check_download_file( $_REQUEST[ 'dwnld_file' ] ); 
        }
    }
}


function check_download_file( $file ) {
    $upload = wp_upload_dir();
    $file = site_url( '/houses' ) . '/' . $file;
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
            header('X-Accel-Redirect: '. $file);
           
            die();
        }
    }
}
