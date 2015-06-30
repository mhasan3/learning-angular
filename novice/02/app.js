
	var app = angular.module("myApp",[]);

	app.controller("myCtrl", function($scope){
		
		$scope.now = new Date();
		$scope.helloMessage = ['Hello', 'Bonjure', 'Hola', 'Ciao', 'Hallo'];
		$scope.greetings = $scope.helloMessage[0];
		$scope.getRandomHelloMessage = function() {
			// console.log($scope.helloMessage.length);

			$scope.rand = Math.random();

			console.log(parseInt($scope.rand*5));

			$scope.greetings = $scope.helloMessage[parseInt(($scope.rand*$scope.helloMessage.length))];
		}
	});