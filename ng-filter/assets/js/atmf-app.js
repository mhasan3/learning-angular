
 var Atmf = angular.module('atmf',['ngSanitize','ui.bootstrap','angular-loading-bar' ]);


    Atmf.filter('html',function($sce){
        return function(input){
            return $sce.trustAsHtml(input);
        }
    });


// Atmf.filter('offset', function() {
//   return function(input, start) {
//     start = parseInt(start, 10);
//     return input.slice(start);
//   };
// });


Atmf.filter('startFrom', function() {
    return function(input, start) {
        if(input) {
            start = +start; //parse to int
            return input.slice(start);
        }
        return [];
    }
});


    Atmf.filter('flatt',function(){
        return function(input){
            var price = 0;
            angular.forEach(input, function(obj , index){
                price = parseFloat(price) + parseFloat(obj);
            });

            return price;


        }
    });





  Atmf.directive("slider", function() {
    return {
        restrict: 'A',
        scope: {
            start: "=",
            end : "=",
            min : "=",
            max:  "=",
            key : "=",
            onchnage : "&",
            onend : "&",

        },

        link: function(scope, elem, attrs ) {

            jQuery(elem).slider({
                range: true ,
                min: parseInt(scope.min),
                max: parseInt(scope.max),
                values: [ scope.start , scope.end ],
        
                slide: function(event, ui) { 
                    scope.$apply(function() {
                        scope.start = ui.values[0];
                        scope.end = ui.values[1];
                        scope.onchnage( scope.key , ui.values[0] , ui.values[1] );
                    });
                },
                stop: function( event, ui ) {
                    scope.$apply(function(){
                        scope.onend();    
                    });
                }
            });
        }
    }
});



    Atmf.config(['cfpLoadingBarProvider', function( cfpLoadingBarProvider ) {
         cfpLoadingBarProvider.includeSpinner = false;
    }]);




  Atmf.controller('AtmfFrontEnd',function( $scope , $filter , $http , $timeout ,filterFilter  ){


   // var search_page =  JSON.parse(angular.element('#dso').text());



   
    $scope.currentPage = 0;







    if(  angular.isDefined(search_page) ){

        $scope.list =  search_page.metadata.list;

        $scope.seachPostType = search_page.metadata.search_post_type;

        $scope.postsPerPage = search_page.metadata.post_per_page;

        $scope.contentLimit = search_page.metadata.contentLimit;

        $scope.sortData = search_page.metadata.sort_meta;

        console.log(search_page);

    }





    $scope.formData = {};
    $scope.formMeta = {};

    if( angular.isObject(search_page.post) ){

        $scope.posts = search_page.post;
        $scope.totalItems = $scope.posts.length;

    }






    $scope.addTometa = function(key, start , end){
      
        $scope.formMeta[key] = {
            start : start , 
            end  : end
        }
    }








    function throughItem(item , id ){


        //if( item.parent_taxonomy ){

            angular.forEach( item.alloption , function(option , key){

                if ( $scope.blankarray.indexOf( parseInt(key) ) == -1) {
                    $scope.blankarray.push( parseInt(key) );
                }

            });


        //}


        if( angular.isArray(item.items)){
            throughItem(item.items[0] , parseInt(id) );
        }






           
    }


    $scope.selected_taxonomy = [];

    
    $scope.grabResult = function(scope,model, ngitem){



        if( model == true && scope.key != undefined ) {

            if ( $scope.selected_taxonomy.indexOf( parseInt(scope.key) ) == -1) {
                $scope.selected_taxonomy.push( parseInt(scope.key) );
            }

            //  $scope.selected_taxonomy_refs.push(ngitem);
        }
            //else if( angular.isNumber(parseInt(model) ) ){
            //    //console.log(model);
            //
            //    if ( $scope.selected_taxonomy.indexOf( parseInt(model) ) == -1) {
            //        $scope.selected_taxonomy.push( parseInt(model) );
            //    }
            //
            //}

        else {

            $scope.blankarray = [];


            var taxonomy_name = [];

            //if(angular.isNumber(parseInt(model)) ) {
            //    scope.key = parseInt(model);
            //}

            // get all the options from the unselect checkbox and taxonomy name
            if( angular.isArray(ngitem.items) ){

                taxonomy_name.push(ngitem.taxonomy);
                throughItem( ngitem.items[0] , parseInt(scope.key) );

            }


           $scope.selected_taxonomy.splice( $scope.selected_taxonomy.indexOf(parseInt(scope.key)  ), 1 );






           angular.forEach( $scope.blankarray , function(blank_key) {

               // splicing all options from root unselect

               if(jQuery.inArray(blank_key ,$scope.selected_taxonomy) != -1){
                   $scope.selected_taxonomy.splice( $scope.selected_taxonomy.indexOf(parseInt(blank_key)  ), 1 );
               }

               // true / false change in the formdata
               angular.forEach($scope.formData , function(cat,taxonomy){

                    if( jQuery.inArray(taxonomy ,taxonomy_name) != -1 ){

                        angular.forEach(cat,function(category,category_key){

                            if( category_key == blank_key ){

                                cat[category_key] = false;
                            }
                        });
                    }
                });

           });



        } // else

     //   console.log('selected taxonomy : ',$scope.selected_taxonomy);



        $scope.doFilter();

    }







    $scope.grabMeta = function(){
       $scope.doFilter();
    }


   $scope.isObject = function(scope){

        if(angular.isObject(scope))
            return true;
        else
            return false;
   }


    $scope.filterData = {};


    $scope.doFilter = function(){

            $scope.filterData.taxonomy = $scope.selected_taxonomy;
            $scope.filterData.metadata = $scope.formMeta;
            $scope.filterData.alltaxonomies = $scope.formData;
            $scope.filterData.post_type = search_page.metadata.search_post_type;
            $scope.filterData.sort_meta = search_page.metadata.sort_meta;

            $scope.loading = true;

            $http({
                method: 'POST',
                url: atmf.ajaxurl+'?action=atmf_do_filter' ,
                data: jQuery.param({ 'filter': $scope.filterData }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(e){
                $scope.posts = [];
                $scope.posts = e.data;

                $scope.filtered = e.data;

                $scope.totalItems = $scope.posts.length;
                $scope.loading = false;
            });
    }

    $scope.doReset = function(){
            location.reload();
    }

    $scope.postView = 'list';



// for taxonomy



        $scope.chnagePrice = function( $event , childId , childPrice , scope , final_price){

            if( !angular.isDefined(final_price)){
                    final_price = 0;
            }

            var checkbox = $event.target;
            if( checkbox.checked == true){  

                    final_price = parseFloat(childPrice) + parseFloat(final_price);
                    
            }else{

                final_price =  parseFloat(final_price) - parseFloat(childPrice);
            }
            

            return parseFloat(final_price);
            
        }


        $scope.selectedChild = function($event , childPrice , childId , selected_child , scope ){
            var checkbox = $event.target;

            if( !angular.isDefined(selected_child)){
                    selected_child = [];
            }

            if( checkbox.checked == true ){

                selected_child.push({
                    'child_product_id':childId,
                    'child_product_price': childPrice
                });

            }else{

                angular.forEach(selected_child , function(child , index){
                        if(child.child_product_id == childId ){
                            selected_child.splice(index,1);
                        }
                });          
                
            }

            return selected_child;
        }   









        $scope.addToCart = function( post_id, post_price , post_selected_child){

           $scope.showLoader = true;

            $http({
                method: 'POST',
                url: atmf.ajaxurl+'?action=do_add_to_cart' ,
                data: jQuery.param({ 'items': post_selected_child }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(e){
                

             
                
                if( e.success == 1 ){

                 //   $alert({ content: e.message, placement: 'top', type: 'material', show: true });

                    jQuery('span.amount').html(e.cart);
                    jQuery('html, body').animate({ scrollTop: 0 }, 500);

                    $scope.showLoader = false;
                } 



               
                
            });
                
        }


        $scope.addToCartSimple = function(post_id){

         
            $scope.showLoader = true;
          


            $http({
                method: 'POST',
                url: atmf.ajaxurl+'?action=do_add_to_cart_simple',
                data: jQuery.param({ 'id': post_id }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(e){                 

             
                if( e.success == 1 ){

                   // $alert({ content: e.message, placement: 'top', type: 'material', show: true });

                    jQuery('span.amount').html(e.cart);

                    jQuery('html, body').animate({ scrollTop: 0 }, 500);

                    $scope.showLoader = false;
                } 


               
            });

        }




    $scope.currentPage = 1; //current page
    $scope.maxSize = 5; //pagination max size
    //$scope.entryLimit = $scope.postsPerPage; //max rows for data table

    /* init pagination with $scope.list */
    $scope.noOfPages = Math.ceil($scope.totalItems/$scope.postsPerPage);
    
    $scope.$watch('QuickSearch', function(term) {
        // Create $scope.filtered and then calculat $scope.noOfPages, no racing!

        

           
        $scope.filtered = filterFilter($scope.posts, term);
        $scope.noOfPages = Math.ceil($scope.filtered.length/$scope.postsPerPage);

        
    });


    $scope.$watch('posts',function(){
         $scope.noOfPages = Math.ceil($scope.posts.length/$scope.postsPerPage);
    });















            $scope.addOption = function( option , id , formOption , new_price  ){
                
                        var found = jQuery.inArray(option, formOption );
                        if (found >= 0) {
                            // Element was found, remove it.

                            

                            //new_price = parseFloat(new_price) - parseFloat(option.price);

//                            new_price.splice(found ,1);
//                            
//                            

                          
                            formOption.splice(found, 1);
                            new_price[0] = parseFloat(new_price[0]) - parseFloat(option.price);                            

                            
                        } else {
                            // Element was not found, add it.

                           
                           

//                            new_price.push(option.price);

                            if( new_price[0] ==''){
                                new_price[0] = 0.0;
                            }
                            
                            new_price[0] = parseFloat(new_price[0]) + parseFloat(option.price);                            

                            formOption.push(option);
                        }
                            
            }





            $scope.addSelectOption = function(option , id , selectedOption , formOption , price ){

                        if( formOption.length > 0){

                            angular.forEach( formOption , function(obj , index){
                                    
                                        if( obj.id == option.id){

                                           // price.splice(index ,1);
                                            price[0] = parseFloat(price[0]) - parseFloat(obj.price);                            
                                            formOption.splice(index, 1);                                    
                                            return;
                                        }
                            });

                            if( option.variation == 'yes' ){
                                selectedOption.variation = 'yes';
                            }

                            if( selectedOption != null){

                                if( price[0] == null){
                                    price[0] = '0';
                                }
                                
                                price[0] = parseFloat(price[0]) + parseFloat(selectedOption.price);                            




                               // price.push(selectedOption.price);
                                formOption.push(selectedOption);
                            }


                        }else{


                            if( price[0] == null ){
                                price[0] = '0';
                            }
                            
                            price[0] = parseFloat(price[0]) + parseFloat(selectedOption.price);                            


                              // price.push(selectedOption.price);
                               formOption.push(selectedOption);
                        }
            }





            function check_for_variation(options){

                angular.forEach(options , function(obj , index ){
                    if(obj.variation == 'yes'){
                        
                        return true;
                    }
                });
                return false;
            }





          $scope.doAddtoCartGrid = function( formOption , price_array , id ){

                var quantity = 1;

                var price = 0;

                angular.forEach( price_array , function(obj , index){
                    price = parseFloat(price) + parseFloat(obj);
                });

            

                $scope.showLoader = true;




                $http({
                    method: 'POST',
                    url: atmf.ajaxurl+'?action=do_cart_new',
                    data: jQuery.param({ 'quantity' : quantity , 'price':price , 'option':formOption , 'product_id':id  }),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(e){

                   


                    if( e.success == 1 ){


                         $scope.showLoader = false;
                

                        jQuery('span.amount').html(e.cart);

                            $http({

                                method: 'POST',
                                url: atmf.ajaxurl+'?action=show_mini_cart',                            
                                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }

                            }).success(function(r){

                                jQuery('#mini-cart').html(r); 
                                jQuery('html, body').animate({ scrollTop: 0 }, 500);

                            });


                    } 



                    

                    
                });


          }






  });

 