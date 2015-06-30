'use strict';

angular.module('myApp',['myApp.controller']);

angular.module('myApp').run(function($rootScope){

	$rootScope.title = 'Famous books';
	$rootScope.name = 'Root scope';

});