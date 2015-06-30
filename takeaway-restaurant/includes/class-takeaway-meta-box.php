<?php 

class UOU_Add_Meta_Box{

	public function __construct() {

		add_filter( 'product_type_selector' , array( $this, 'add_selector_takeaway' ) );

 
 		}


 	public function add_selector_takeaway($types) {
		$types[ 'food' ] = __( 'Takeaway product', 'takeaway' );
		return $types;
	}


}


new UOU_Add_Meta_Box();
 ?>