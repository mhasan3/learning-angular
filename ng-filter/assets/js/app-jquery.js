    (function($){
        $(document).ready(function(){



	        $('#search_box_main').hide();

	        $('select#page_template').on('change',function(e){
	           
	           e.preventDefault();
	           
	           if( $(this).val() == 'atmf-search.php' ){
		            $('#search_box_main').show();
	            }else{
	                $('#search_box_main').hide();
	            }


	        });

	        if( $('select#page_template').val() == 'atmf-search.php' ){
	             $('#search_box_main').show();
	        }

        });
    })(jQuery);
