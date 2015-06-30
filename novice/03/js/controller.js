
	var app = angular.module("myApp.controller",[]);

	app.controller("SiteCtrl", function($scope){

		$scope.publisher = 'site porint';
		$scope.type = "Web development";
		$scope.name = "scope for site controller";
	
	});

	app.controller('bookCtrl', function($scope){
		// $scope.publisher = 'habijabi';
		$scope.books = ['jump to html', 'jump to start css', 'jump start responsive'];
		$scope.name = "Scope for book controller";

		$scope.addToWishList = function(book) {

			$scope.wishListCount ++;
		}

		$scope.wishListCount = 0;

		$scope.$watch('wishListCount', function(newValue,oldValue){
			console.log('Called' +newValue+ 'times');
			if (newValue == 2) {
				alert('Generic alert');
			}
		});
	});