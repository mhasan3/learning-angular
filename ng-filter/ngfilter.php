<?php
/**
 * Angular based filtering plugin for WordPress
 *
 *
 *
 * @package   ATMF
 * @author    Tareq Jobayere <tarexyz@gmail.com>
 * @link      https://tarex.github.io
 * @copyright 2014 http://uouapps.com
 *
 * @wordpress-plugin
 * Plugin Name:       Angular based filtering plugin
 * Plugin URI:        http://uouapps.com/
 * Description:       Angular based metadata and taxonomy filtering plugin for WordPress
 * Version:           1.3.0
 * Author:            http://uouapps.com
 * Author URI:        http://uouapps.com
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


add_action( 'plugins_loaded', array( 'NG_Filter', 'get_instance' ) );


if( ! class_exists('NG_Filter') ){

    class NG_Filter{


        const VERSION = '1.3.0';

        protected $plugin_slug;


        private static $instance;


        protected $templates;




        public static function get_instance() {

            if( null == self::$instance ) {
                self::$instance = new NG_Filter();
            } // end if

            return self::$instance;

        } // end getInstance





        private function __construct() {

            $this->templates = array();
            $this->plugin_locale = 'atmf';

            define( 'UOU_ATMF_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
            define( 'UOU_ATMF_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

            require_once( UOU_ATMF_DIR . '/includes/class-uou-atmf-load-template.php' );
            require_once( UOU_ATMF_DIR . '/includes/uou-atmf-functions.php' );
            require_once( UOU_ATMF_DIR . '/includes/class-uou-atmf-ajax-admin-request.php' );
            require_once( UOU_ATMF_DIR . '/includes/class-uou-atmf-ajax-frontend-request.php' );
            


            add_action( 'init', array( $this, 'atmf_load_plugin_textdomain' ) );

            add_action( 'wp_enqueue_scripts', array( $this , 'atmf_load_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'atmf_admin_load_scripts' ) );


//            add_action('atmf_hidden_data_show', array($this , 'hidden_data_show') );

            add_action( "add_meta_boxes", array($this,"atmf_add_meta_boxes_page_main" ) );


            add_filter('page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ) );
            add_filter('wp_insert_post_data', array( $this, 'register_project_templates' ) );
            add_filter('template_include', array( $this, 'view_project_template') );

            $this->templates = array(
                'atmf-search.php' => __( 'ATMF Search Page', 'atmf' ),
            );

            $templates = wp_get_theme()->get_page_templates();
            $templates = array_merge( $templates, $this->templates );


        }





        public function atmf_load_scripts(){

            global $post;


            if(is_page_template('atmf-search.php') ) {

                wp_register_style('bootstrap-f-css', UOU_ATMF_URL . '/assets/css/bootstrap.min.css', array(), false, 'all');
                wp_enqueue_style('bootstrap-f-css');


                wp_register_style('loading-bar', UOU_ATMF_URL . '/assets/js/vendor/angular-loading-bar/loading-bar.css', array(), false, 'all');
                wp_enqueue_style('loading-bar');


                wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

                wp_register_style('atmf-f-css', UOU_ATMF_URL . '/assets/css/atmf-frontend.css', array(), false, 'all');
                wp_enqueue_style('atmf-f-css');

                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-ui-slider');

               wp_register_script('atmf-f-bootstrap',  'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js', array(), false, true);
               wp_enqueue_script('atmf-f-bootstrap');


                wp_register_script('atmf-angular', UOU_ATMF_URL . '/assets/js/vendor/angular/angular.js', array(), false, true);
                wp_enqueue_script('atmf-angular');

                wp_register_script('atmf-angular-bar', UOU_ATMF_URL . '/assets/js/vendor/angular-loading-bar/loading-bar.js', array(), false, true);
                wp_enqueue_script('atmf-angular-bar');


                wp_register_script('atmf-angular-sanitize', UOU_ATMF_URL . '/assets/js/vendor/angular-sanitize/angular-sanitize.js', array(), false, true);
                wp_enqueue_script('atmf-angular-sanitize');

                wp_register_script('angular-bootstrap', UOU_ATMF_URL . '/assets/js/vendor/angular-bootstrap/ui-bootstrap-tpls.js', array(), false, true);
                wp_enqueue_script('angular-bootstrap');


                wp_register_script('angular-pagination', UOU_ATMF_URL . '/assets/js/vendor/angular-utils-pagination/dirPagination.js', array(), false, true);
                wp_enqueue_script('angular-pagination');


                wp_register_script('atmf-app', UOU_ATMF_URL . '/assets/js/atmf-app.js', array(), false, true);
                wp_enqueue_script('atmf-app');


                $search_data = $this->hidden_data_show();


                wp_localize_script('atmf-app', 'atmf', array('ajaxurl' => admin_url('admin-ajax.php') ));

                wp_localize_script( 'atmf-app', 'search_page' , $search_data );

            }

        }


        /**
         * Posting search data into a hidden div
         * Transient api added
         * @return print search data
         * @verison	1.2.0
         * @since	1.2.0
         */


        public function hidden_data_show(){

            global $post;

            $transient_name = 'search_page';
            $cacheTime = 10; // 10 min



                $something = get_post_meta( $post->ID , 'search_page', true);

                $meta_data = json_decode($something);

                if( isset($meta_data->list) ){

                    $post_type = $meta_data->search_post_type;
                    $meta_data->list =  $this->process_search_sidebar( $meta_data->list , $post_type );
                }

                $get_all_post = $this->get_all_post( $meta_data , $post_type );


                $search_page = array();

                $search_page = array(
                    'post_id'  => $post->ID,
                    'metadata' => $meta_data,
                    'post'     => $get_all_post
                );

               // $transient_data =  json_encode( $search_page , JSON_NUMERIC_CHECK);

                return $search_page;
        }




        public function atmf_admin_load_scripts($hook){

            
            wp_register_style( 'bootstrap-css', UOU_ATMF_URL. '/assets/css/bootstrap-admin.css', array(), false, 'all' );
            wp_enqueue_style( 'bootstrap-css' );


            wp_enqueue_script('jquery');
            wp_enqueue_script('underscore');
            
            wp_register_script( 'atmf-jquery', UOU_ATMF_URL. '/assets/js/app-jquery.js', array(), false, true );
            wp_enqueue_script( 'atmf-jquery' );

            
            wp_register_script( 'atmf-bootstrap', UOU_ATMF_URL. '/assets/js/vendor/bootstrap/bootstrap.js', array(), false, true );
            wp_enqueue_script( 'atmf-bootstrap' );


            if( $hook == 'post.php' || $hook == 'post-new.php'){

                global $post;

                wp_register_style( 'ng-tree-css', UOU_ATMF_URL. '/assets/js/vendor/angular-ui-tree/angular-ui-tree.min.css', array(), false, 'all' );
                wp_enqueue_style( 'ng-tree-css' );

                wp_register_style( 'ng-xe-css', UOU_ATMF_URL. '/assets/js/vendor/angular-xeditable/xeditable.css', array(), false, 'all' );
                wp_enqueue_style( 'ng-xe-css' );


                wp_register_style( 'ng-app-css', UOU_ATMF_URL. '/assets/css/app.css', array(), false, 'all' );
                wp_enqueue_style( 'ng-app-css' );

                wp_register_script( 'atmf-angular', UOU_ATMF_URL. '/assets/js/vendor/angular/angular.js', array(), false, true );
                wp_enqueue_script( 'atmf-angular' );

                wp_register_script( 'atmf-angular-router', UOU_ATMF_URL. '/assets/js/vendor/angular-ui-router/angular-ui-router.js', array(), false, true );
                wp_enqueue_script( 'atmf-angular-router' );

                wp_register_script( 'angular-bootstrap', UOU_ATMF_URL. '/assets/js/vendor/angular-bootstrap/ui-bootstrap-tpls.js', array(), false, true );
                wp_enqueue_script( 'angular-bootstrap' );

                wp_register_script( 'atmf-ng-ui-tree-js', UOU_ATMF_URL. '/assets/js/vendor/angular-ui-tree/angular-ui-tree.js', array(), false, true );
                wp_enqueue_script( 'atmf-ng-ui-tree-js' );

                wp_register_script( 'atmf-x-js', UOU_ATMF_URL. '/assets/js/vendor/angular-xeditable/xeditable.js', array(), false, true );
                wp_enqueue_script( 'atmf-x-js' );

                wp_register_script( 'atmf-appp', UOU_ATMF_URL. '/assets/js/app.js', array(), false, true );
                wp_enqueue_script( 'atmf-appp' );


                $meta_data = get_post_meta($post->ID,'search_page',true);

                $meta_data = json_decode($meta_data);

                wp_localize_script( 'atmf-appp', 'search_page' , array('metadata'=> $meta_data) );

            }

        }


        /**
         * Get all the posts through the metadata object
         *
         * @param  object $metadata
         * @return object
         * @verison	1.0.0
         * @since	1.0.0
         */


        public function get_all_post( $metadata ,$post_type ){

            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,

            );
            $query = get_posts( $args );


            $result =array();

            foreach($query as $key=>$post){
                $data = array();

 /**
edited here
**/

                $meta = get_post_custom($post->ID);
                
                if(has_term( 'grouped', 'product_type', $post )) :
                    
                    $args = array(
                        'post_parent' => $post->ID,
                        'post_type'   => 'product', 
                        'posts_per_page' => -1,
                        'post_status' => 'publish'
                    );
                    $children_array = get_children( $args, OBJECT );

                    // _log($children_array);

                    foreach ($children_array as $children) {

                        $data['children'][] = array(

                            'post_id' => $children->ID,
                            'post_title' => $children->post_title,
                            'post_permalink' => get_the_permalink($children->ID) ,
                            'price' =>  get_post_meta( $children->ID, '_price', true ) 
                        );

                    }
                

                  endif;




                  if(has_term( 'food', 'product_type', $post )) {


                        $food = get_post_meta($post->ID , 'takeaway_settings', true);                                

                        if( is_string($food) ){
                            $data['food']= json_decode($food);
                        }

                        $main_price = get_post_meta( $post->ID , '_price_for_grouped_product_grp_product_price' , true);

                        if(!empty($main_price) ){
                            $data['food_price'] = $main_price;
                        }

                  }




                // _log($data);

                if(isset($meta['_price'][0])){
                    $base_price = $meta['_price'][0];
                    $data['price'] = $base_price; 
                }



/**
ends editing
**/
                $data['post_id'] = $post->ID;
                $data['post_title'] = $post->post_title;
                $data['post_content'] = $post->post_content;
                $data['post_permalink'] = get_the_permalink($post->ID);
                $data['post_date'] = $post->post_date;
                $data['comment_count'] = intval( $post->comment_count );
                $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large');
                if($large_image_url) {
                    $data['post_thumbnail'] =  $large_image_url[0];
                } else {
                    $data['post_thumbnail'] =  'http://placehold.it/400x400';
                }



                //@ added in version 1.2.0
                // for sorting facility

                if( isset( $metadata->sort_meta ) && !empty($metadata->sort_meta) ){
                    foreach($metadata->sort_meta as $sort_key => $sort_value ){

                        if( !isset( $data[$sort_value->label] ) )
                            $data[$sort_value->label] = get_post_meta( $post->ID , $sort_value->label , true );

                    }
                }

                // end of sorting data



                $result[] = $data;

               
            }



            return $result;
        }



        /**
         * Filter sidebar auxilliary items added through this
         *
         * @param  object $metadata , string $post_type
         * @return object
         * @verison	1.0.0
         * @since	1.0.0
         */

        public function process_search_sidebar( $metadata , $post_type ){

            global $wpdb;





            foreach ( $metadata as $item) {


                $data = array();

                if( isset($item->option)  && $item->option == 'taxonomy' ){

                    // taxonomy

                    if( isset($item->parent_taxonomy) && !empty($item->parent_taxonomy) ){

                        if( !isset($item->alloption) ){

                            $child_terms = get_term_children( $item->parent_taxonomy , $item->taxonomy );

                            $setdata = array();

                            if( !empty( $child_terms ) ){

                                foreach( $child_terms as $child) {
                                    $term = get_term_by( 'id', $child, $item->taxonomy );
                                    if( $term->parent == $item->parent_taxonomy)
                                        $setdata[ $term->term_id ] = $term->name;
                                }

                            }

                            $item->alloption = $setdata;

                        }


                    }else{

                       // if( empty($item->alloption) || !isset($item->alloption) ){

                            $terms = get_terms( $item->taxonomy , 'orderby=count&hide_empty=0&parent=0' );
                            foreach ($terms as  $term) {
                                $data[$term->term_id] = $term->name;
                            }

                            $item->alloption = $data;



                      //  }

                    }

                    // recursive calling
                    if(isset($item->items)){
                        $this->process_search_sidebar($item->items , $post_type);
                    }

                }else{

                    // metadata

                    if( isset($item->viewType) && $item->viewType == 'range' ){

                        $item->start = $item->rangeStart;
                        $item->end = $item->rangeEnd;
                    }


                    if( isset($item->metakey) ){

                        $get_meta_value = $this->get_meta_values( $item->metakey , $post_type );
                        $item->alloption = $get_meta_value;       

                    }


                }

            }

            return $metadata;
        }



        /**
         * Get distinct the meta values
         *
         * @param  string $key , string $type , string $status
         * @return object
         * @verison	1.0.0
         * @since	1.0.0
         */



        public function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

            global $wpdb;

            if( empty( $key ) )
                return;

            $r = $wpdb->get_col( $wpdb->prepare( "
                        SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                        WHERE pm.meta_key = '%s'
                        AND pm.meta_value != ''
                        AND p.post_status = '%s'
                        AND p.post_type = '%s'
                    ", $key, $status, $type ) );

            return $r;
        }



        /**
         * Load Modal html into the wp admin footer
         *
         * @return void
         * @verison	1.0.0
         * @since	1.0.0
         */


        public function atmf_html_backend(){

            $template_loader = new Uou_Atmf_Load_Template();
            ob_start();
            $template = $template_loader->locate_template( 'popup.php' );

            if(is_user_logged_in() ){
                include( $template );
            }
            echo ob_get_clean();

        }


        /**
         * Add meta box in the right side of a new page , in wp-admin
         *
         * @return void
         * @verison 1.0.0
         * @since   1.0.0
         */


        public function atmf_add_meta_boxes_page_main( $post )
        {
            add_meta_box(
                'search_box_main',
                'Create your Search Option',
                array( $this , 'atmf_main_search_box'),
                'page',
                'normal',
                'core'
            );
        }

        public function atmf_main_search_box( $post )
        {

          //  echo '<a href="#" id="create_search_options">Create/Update Search Options</a>';

            $template_loader = new Uou_Atmf_Load_Template();
            ob_start();
            $template = $template_loader->locate_template( 'popup.php' );

            if(is_user_logged_in() ){
                include( $template );
            }
            echo ob_get_clean();


        }

        /**
         * Load text domain for translation
         *
         * @return void
         * @verison	1.0.0
         * @since	1.0.0
         */

        public function atmf_load_plugin_textdomain(){

            $domain = $this->plugin_slug;
            $locale = apply_filters( 'plugin_locale', get_locale(), 'atmf' );

            load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
            load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
        }

        /**
         * Adds our template to the pages cache in order to trick WordPress
         * into thinking the template file exists where it doens't really exist.
         *
         * @param   array    $atts    The attributes for the page attributes dropdown
         * @return  array    $atts    The attributes for the page attributes dropdown
         * @verison	1.0.0
         * @since	1.0.0
         */

        public function register_project_templates( $atts ) {

            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

            $templates = wp_cache_get( $cache_key, 'themes' );
            if ( empty( $templates ) ) {
                $templates = array();
            } // end if

            wp_cache_delete( $cache_key , 'themes');
            $templates = array_merge( $templates, $this->templates );
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;

        } // end register_project_templates



        /**
         * Checks if the template is assigned to the page
         *
         * @version	1.0.0
         * @since	1.0.0
         */

        public function view_project_template( $template ) {

            global $post;

            if ( !isset( $post ) ) return $template;

            if ( ! isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
                return $template;
            } // end if

            $template_loader = new Uou_Atmf_Load_Template();

            if(  is_page_template( 'atmf-search.php' ) ){
                $file = $template_loader->locate_template( 'atmf-search.php' );
            }


      //      $file = plugin_dir_path( __FILE__ ) . 'templates/' . get_post_meta( $post->ID, '_wp_page_template', true );

            if( file_exists( $file ) ) {
                return $file;
            } // end if

            return $template;

        } // end view_project_template



        /**
         * Retrieves and returns the slug of this plugin. This function should be called on an instance
         * of the plugin outside of this class.
         *
         * @return  string    The plugin's slug used in the locale.
         * @version	1.0.0
         * @since	1.0.0
         */



        public function get_locale() {
            return $this->plugin_slug;
        } // end get_locale


    } // end of class
}