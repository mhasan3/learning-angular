<?php
/**
 * Template Name: ATMF Search Page
 *
 * A template used to demonstrate how to include the template
 * using this plugin.
 *
 * @package ATMF
 * @since 	1.0.0
 * @version	1.0.0
 */

 	get_header();
?>

<!--START : DO NOT DELETE THIS BLOCK -->
<div id="dso" style="display: none;">
    <?php do_action('atmf_hidden_data_show'); ?>
</div>
<!--END: DO NOT DELETE THIS BLOCK -->


<div ng-app="atmf" ng-cloak>
	<div ng-controller="AtmfFrontEnd">
		<div class="container">
			<div class="row">
			
				<!-- start of main  -->
				<div class="col-md-4 sidebar" rel="sidebar">
					<?php  get_search_sidebar(); ?>
				</div>
				<!-- end of sidebar  -->

				<!-- Start of main  -->	
				<div class="col-md-8 main" rel="main">
					<?php  get_search_result(); ?>
				</div>	
				<!-- end of main  -->			

			</div> <!--  end row  -->
		</div><!--   end container  -->
	</div>
</div>

<?php  

 get_footer();




