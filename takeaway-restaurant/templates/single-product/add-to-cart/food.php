<?php
/**
 * Food type product add to cart
 *
 * @author 		uouapps
 * 
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $post, $woocommerce;

// _log($product);

$parent_product_post = $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>


<?php
		// if ( ! $product->is_sold_individually() )
		// 	woocommerce_quantity_input( array(
		// 		'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
		// 		'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
		// 	) );
	?>

<div ng-app="dyCart">
	<div ng-controller="dyController">
		



<!-- total_price ng-binding -->
	
		<span class="total_price ng-binding" ng-if="price !='0'">Total price : {{price}}</span>


	 	<div ng-repeat="option in options">

	 		<div ng-if="option.type =='checkbox' ">
	 				<input type="checkbox" name="{{option.id}}" ng-model="option.checkbox" ng-change="addOption(option , option.id)"> {{ option.title }} <span><i class="fa fa-plus ng-binding"> <?php echo get_woocommerce_currency_symbol(); ?> {{ option.price}} </i></span>
	 				
	 				<!-- <input type="number"  ng-if="option.quantity == 'yes'" ng-model="option.number">  -->
	 		</div>

     	</div>

     	<input type="hidden" name="product_id" class="food_product_id" value="<?php echo esc_attr( get_the_ID()); ?>">


     	<div ng-repeat="option in options">
     		<div ng-if="option.type =='select' ">

     				<label>{{option.title}}</label>

     				<div ng-if="option.variation == 'yes'">
					<select ng-model="option.selectedOption" ng-change="addSelectOption( option , option.id  , option.selectedOption)" ng-options="attr as attr.text for attr in option.options"></select>			
    				</div>

    				<div ng-if="option.variation == 'no' || !option.variation">
	    				<select ng-model="option.selectedOption" ng-change="addSelectOption( option , '', option.selectedOption)" ng-options="attr as attr.text for attr in option.options">
	    					<option value="">Please select one</option>
	    				</select>				
    				</div>
     		</div>
     			
     	</div>
		
		<?php 
				global $woocommerce;
				$cart_page_id = wc_get_page_id( 'cart' );
				$cart_link = get_permalink( $cart_page_id );
				// echo $cart_link;
	 	?>

	 	<br>

	 <p class="buttons">
		<button type="button" class="button" ng-click="doAddtoCart()">Add to cart</button>
		<a href="<?php echo esc_url($cart_link); ?>" class="button">View Cart</a>
	 </p>
	
	







	</div>
</div>



<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>