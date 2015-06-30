<?php



class Uou_Atmf_Ajax_Admin_Request {






    public function __construct(){

        add_action( "wp_ajax_nopriv_atmf_search_data", array ( $this, 'atmf_search_data' ) );
        add_action( "wp_ajax_atmf_search_data",array( $this,'atmf_search_data'));


        add_action( "wp_ajax_nopriv_atmf_get_child_taxonomy", array ( $this, 'atmf_get_child_taxonomy' ) );
        add_action( "wp_ajax_atmf_get_child_taxonomy",array( $this,'atmf_get_child_taxonomy'));



        add_action( "wp_ajax_nopriv_atmf_save_metadata", array ( $this, 'atmf_save_metadata' ) );
        add_action( "wp_ajax_atmf_save_metadata",array( $this,'atmf_save_metadata'));

    }








    public function atmf_search_data(){

        global $wpdb;


        extract($_POST);


        if( !empty($type) )
            $data['taxonomies'] = get_object_taxonomies( $type );






        $sql = $wpdb->prepare( "SELECT DISTINCT $wpdb->postmeta.meta_key FROM $wpdb->posts ".
            " INNER JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id".
            " WHERE $wpdb->posts.post_type = '%s' ".
            " AND ($wpdb->posts.post_status = 'publish') ".
            "  ", $type );




        $query = $wpdb->get_results( $sql );

        $meta_key = array();

        foreach($query as $key => $value){
            $meta_key[] = $value->meta_key;
        }


        $data['metakeys'] = $meta_key;

        echo json_encode( $data , JSON_NUMERIC_CHECK );



        wp_die();
    }











    public function atmf_get_child_taxonomy(){

        extract($_POST);


        $terms_parent_id = array();
        $terms_parent_array = array();
        $parent_taxonomy_output = array();
        $data = array();


        if( empty($_POST['parent']) ){



            $terms = get_terms( $taxonomy , 'orderby=count&hide_empty=0&parent=0' );


            
            foreach($terms as $term){
                $parent_taxonomy_output['alloption'][$term->term_id] = $term->name;

                if( !empty($term->parent) ){

                    $terms_parent_id[] = $term->parent;
                }

                $data[$term->term_id] =  get_term_children( $term->term_id ,$taxonomy);

            }


            $parent_terms = get_terms( $taxonomy , array(
                'include' => $terms_parent_id ,
                'parent'  => 0,
                'hide_empty' => false
            ) );

            foreach ($parent_terms as $key => $value) {

                if( !empty( $data[$value->term_id]) )
                    $parent_taxonomy_output['first_parent'][$value->term_id] = $value->name;
            }


        }else{


            $terms = get_terms( $taxonomy , array(
                'parent' => $parent,
                'hide_empty' => false
            ));



            foreach ($terms as $key => $value) {

                $parent_taxonomy_output['alloption'][$value->term_id] = $value->name;

                $data[$value->term_id] =  get_term_children( $value->term_id ,$taxonomy);
            }

            foreach ($terms as $key => $value) {
                if( !empty( $data[$value->term_id]) )
                    $parent_taxonomy_output['child_parent'][$value->term_id] = $value->name;
            }


        }

        echo json_encode($parent_taxonomy_output , JSON_NUMERIC_CHECK);



        wp_die();
    }





    public function atmf_save_metadata(){

        $save_data = json_encode($_POST['save_data'] ,JSON_NUMERIC_CHECK);



        if( !empty( $_POST['save_data']['page_id'] ) ){

            if( json_decode($save_data) != null)
            {
                $store_it = update_post_meta( $_POST['save_data']['page_id'] , 'search_page', $save_data);
            }

        }



        if( isset($store_it) ){

            $meta_id = get_mid_by_key( $_POST['save_data']['page_id'] , 'search_page' );
            $return = array('success' => true,'meta_id' => $meta_id ,'save_data'=> $save_data );

        }else{

            $return = array('error'=> true);

        }


        $result = array();
        $result['data'] = $return;
        //wp_send_json_success($return);
        echo json_encode( $result , JSON_NUMERIC_CHECK);

        wp_die();
    }











}

new Uou_Atmf_Ajax_Admin_Request();