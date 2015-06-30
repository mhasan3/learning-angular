<?php
/**
 * Plugin Name: Takeaway Custom product [Beta]
 * Plugin URI:  http://uouapps.com/
 * Description: This plugin extends from WooCommerce. This plugin require WooCommerce plugin. You can download the WooCommerce plugin from http://wordpress.org/plugins/woocommerce/
 * Author:      UOUAPPS
 * Author URI:  http://uouapps.com/
 * Version:     1.0.0
 * Text Domain: takeaway
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here

    Class takeaway_restaurant{

    	public function __construct(){

	 		
	 		define( 'UOU_PACKAGE_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
            define( 'UOU_TAKEAWAY_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );


	 		include ( 'includes/class-takeaway-meta-box.php' );


            add_action( 'woocommerce_loaded', array( $this, 'includes' ) );


            add_action('woocommerce_food_add_to_cart', array($this, 'add_to_cart'),40);


            add_action('admin_enqueue_scripts', array($this , 'add_admin_scripts'));

            add_action( "add_meta_boxes", array($this,"add_settings_box" ) );


            add_action( "wp_ajax_nopriv_takeaway_settings", array ( $this, 'takeaway_settings' ) );
            add_action( "wp_ajax_takeaway_settings",array( $this,'takeaway_settings'));


    	
    	}


        public function add_settings_box($post){



            add_meta_box(
                'add_product_option',
                'Add Options',
                array( $this , 'settings_box_hold'),
                'product',
                'normal',
                'core'
            );

            
        }

        public function settings_box_hold($post){

            ob_start();
            include 'templates/settings.php';
            echo ob_get_clean();
        }






        public function add_admin_scripts($hook){


            wp_register_style( 'boot-css', UOU_TAKEAWAY_URL. '/assets/bootstrap-admin.css', array(), false, 'all' );
            wp_enqueue_style( 'boot-css' );



            wp_enqueue_script('jquery');

            if( $hook == 'post.php' || $hook == 'post-new.php'){

                global $post;


                wp_register_script('tr-angular',  UOU_TAKEAWAY_URL. '/assets/angular.js' , array(), false, true);
                wp_enqueue_script( 'tr-angular' );

                wp_register_script( 'aboot', UOU_TAKEAWAY_URL. '/assets/ui-bootstrap-tpls.js', array(), false, true );
                wp_enqueue_script( 'aboot' );


                $meta_data = get_post_meta($post->ID,'takeaway_settings',true);
                $meta_data = json_decode($meta_data);


                wp_register_script( 'custom-takeaway', UOU_TAKEAWAY_URL. '/assets/custom.js', array(), false, true );
                wp_enqueue_script( 'custom-takeaway' );

                wp_localize_script( 'custom-takeaway', 'takeaway_restaurant' , array('metadata'=> $meta_data) );

            }





        }
       

        public function includes(){
            //templates
            include( 'includes/class-uou-product-food.php' );
        }



        public function add_to_cart() {
            wc_get_template( 'single-product/add-to-cart/food.php',$args = array(), $template_path = '', UOU_PACKAGE_TEMPLATE_PATH);
        }


        public function takeaway_settings(){

            $post_id = $_POST['post_id'];

            if( isset($post_id) ){

                $data = json_encode($_POST['items']);

                update_post_meta( $post_id ,'takeaway_settings' , $data);
                

                $meta_id = get_mid_by_key( $post_id, 'takeaway_settings' );
                $result = array('success' => true,'meta_id' => $meta_id ,'data'=> $data );
            
            }else{

                $result = array('error' => true);
            }
            
            echo json_encode( $result);
            wp_die();
        }



    } // end of class


    $GLOBALS['takeaway_restaurant'] = new takeaway_restaurant();

}else{



    function my_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Please Install WooCommerce First before activating this Plugin. You can download WooCommerce from <a href="http://wordpress.org/plugins/woocommerce/">here</a>.', 'uou-bookings' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'my_admin_notice' );


}