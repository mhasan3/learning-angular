	var app = angular.module('SettingsApp',['ui.bootstrap']);

	app.controller('SetCont', function($scope , $http){

		$scope.items = [];




		$scope.addOption = function(item) {
			item.options.push({
                'id' : item.id,
				'text' : '',
				'price' : '',
                'title' : item.title
			});
		}

		$scope.doAddBlock = function() {
			$scope.items.push({
                'id' : (new Date).getTime(),
				'title' : 'untitled',
				'details':false,
				'options':[]
			});
		}


		$scope.removeOption = function(hashKey , options) {
            
            angular.forEach( options , function(obj, index ){
	              if (obj.$$hashKey === hashKey) {
	               		 options.splice(index, 1);
	                return;
	              };
	          });  

        };

        $scope.removeItem = function(hashKey , items) {
            
            angular.forEach( items , function(obj, index ){
	              if (obj.$$hashKey === hashKey) {
	               		 items.splice(index, 1);
	                return;
	              };
	          });  

        };


        $scope.saveAsMeta = function(){


        	$scope.DraftMessage = false;

            var qs = (function(a) {
                if (a == "") return {};
                var b = {};
                for (var i = 0; i < a.length; ++i)
                {
                    var p=a[i].split('=');
                    if (p.length != 2) continue;
                    b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
                }
                return b;
            })(window.location.search.substr(1).split('&'));

            if( qs['post'] ){

            	var post_id = qs['post'];

   				$http({
                    method: 'POST',
                    url: ajaxurl+'?action=takeaway_settings' ,
                    data: jQuery.param({ 'items': $scope.items ,'post_id': post_id }),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function(r){


                    if( r.success ){
                        $scope.SuccessMessage = true;
                        
                        angular.element('textarea#meta-'+ r.meta_id +'-value').val( r.data );                       
                    }
                    if( r.error ){
                        $scope.ErrorMessage = true;
                    }

                });


            }else{
            	$scope.DraftMessage = true;
            }

        }



        function MergeRecursive( list ){

            angular.forEach(list , function(value , key ){
                 if( angular.isObject(value) ){

                        if( angular.isDefined(value.options) ){
                            MergeRecursive(value.options);
                        }else{
                            value.options = [];
                        }
                 }
            });
        }



        if(  angular.isDefined(takeaway_restaurant.metadata) && angular.isObject(takeaway_restaurant.metadata) ){
                $scope.items = takeaway_restaurant.metadata;
                MergeRecursive( $scope.items );
        }












		
	});