<?php 


class WC_Product_Food extends WC_Product {

	public function __construct( $product ) {
		$this->product_type = 'food';
		parent::__construct( $product );
	}
}
