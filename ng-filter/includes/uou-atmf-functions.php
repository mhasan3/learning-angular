<?php


if(!function_exists('_log')){

    function _log( $message ) {
        if( WP_DEBUG === true ){
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }
}






function get_mid_by_key( $post_id, $meta_key ) {
    global $wpdb;
    
    $mid = $wpdb->get_var( $wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key) );
   
    if( $mid != '' )
        return (int) $mid;

    return false;
}



function get_search_sidebar(){

            $template_loader = new Uou_Atmf_Load_Template();
            ob_start();
            $template = $template_loader->locate_template( 'left-sidebar.php' );
            include($template);
}




function get_search_result(){

            $template_loader = new Uou_Atmf_Load_Template();
            ob_start();
            $template = $template_loader->locate_template( 'search-result.php' );
            include($template);
}








