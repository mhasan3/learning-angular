<?php


class Uou_Atmf_Ajax_Frontend_Request {




    public function __construct(){

        add_action( "wp_ajax_nopriv_atmf_do_filter", array ( $this, 'atmf_do_filter' ) );
        add_action( "wp_ajax_atmf_do_filter",array( $this,'atmf_do_filter'));
    }



    public function atmf_do_filter(){

        $filter = $_POST['filter'];
        $post_type = $filter['post_type'];


        if(isset( $filter['sort_meta'] ) ){
            $sort_meta = $filter['sort_meta'];
        }

        // taxonomy query building

        $tax_query = array();
        $build_array = array();




        if( isset( $filter['alltaxonomies'] ) ){

            foreach ( $filter['alltaxonomies'] as $key => $terms_id) {

                $taxonomy_terms = array();

                if( is_array($terms_id) ){

                    foreach ($terms_id as $term_key => $term_value) {

                        if($term_value == 'true'){
                            $taxonomy_terms[] = $term_key;
                        }
                    }

                    if( !empty($taxonomy_terms) ){

                        $build_array['taxonomy'] = $key;
                        $build_array['field'] = 'id';
                        $build_array['terms'] = $taxonomy_terms;
                        $tax_query[] = $build_array;
                    }

                }else{


                        $build_array['taxonomy'] = $key;
                        $build_array['field'] = 'id';
                        $build_array['terms'] = $terms_id;
                        $tax_query[] = $build_array;
                }
            }
        }




        // Meta query building

        $meta_query = array();
        $build = array();



        if( isset($filter['metadata'] ) ){

            foreach ( $filter['metadata'] as $meta_key => $metas_id) {



                $meta_keys = array();


                    // for range 
                    if( is_array($metas_id) && isset( $metas_id['start'] ) ){

                        $build['value'] = array( $metas_id['start'] , $metas_id['end']);
                        $build['key'] = $meta_key;
                        $build['type'] = 'numeric';
                        $build['compare'] = 'BETWEEN';

                        $meta_query[] = $build;



                    }

                    // check with true value 
                    if( is_array($metas_id) ){

                        foreach( $metas_id as $m_key => $m_value ) {

                            if( $m_value == 'true' ){

                                $meta_keys[] = $m_key;

                            }
                        }


                        if( !empty($meta_keys) ){

                            $build['key'] = $meta_key;
                            $build['compare'] = 'IN';
                            $build['value'] = $meta_keys;
                            $meta_query[] = $build;

                        }

                    }


                    if(!is_array($metas_id)){


                            $build['key'] = $meta_key;
                            $build['compare'] = 'IN';
                            $build['value'] = $metas_id;
                            $meta_query[] = $build;

                    }





            } // end of foreach metadata

        }


        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'tax_query'      => $tax_query ,
            'meta_query'     => $meta_query
        );




        $posts = get_posts($args);


        $result =array();

        foreach($posts as $key=>$post){
            $data = array();

            $data['post_id'] = $post->ID;
            $data['post_title'] = $post->post_title;
            $data['post_content'] = $post->post_content;
            $data['post_permalink'] = get_the_permalink($post->ID);
            $data['post_date'] = $post->post_date;
            $data['comment_count'] = $post->comment_count;
            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large');
            if($large_image_url) {
                $data['post_thumbnail'] =  $large_image_url[0];
            } else {
                $data['post_thumbnail'] =  'http://placehold.it/400x400';
            }


            //@ added in version 1.2.0
            // for sorting facility

            if( isset( $sort_meta ) && !empty($sort_meta) ){
                foreach( $sort_meta as $sort_key => $sort_value ){

                    $label = $sort_value['label'];
                    if( !isset( $data[$label] ) ){
                        $data[$label] = get_post_meta( $post->ID , $label , true );
                    }



                }
            }

             //end of sorting data






/**
  edit start 
*/





                $meta = get_post_custom($post->ID);
                
                if(has_term( 'grouped', 'product_type', $post )):
                    
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
  edit end 
*/          



              $result[] = $data;

            
        }


        echo json_encode( $result , JSON_NUMERIC_CHECK );

        wp_die();
    }

}


new Uou_Atmf_Ajax_Frontend_Request();


