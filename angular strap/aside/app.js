var app = angular.module('mgcrea.ngStrapDocs', ['ngAnimate', 'ngSanitize', 'mgcrea.ngStrap']);

app.controller('MainCtrl', function($scope) {
});

'use strict';


angular.module('mgcrea.ngStrapDocs')

.controller('AlertDemoCtrl', function($scope, $templateCache, $timeout, $alert) {

  $scope.alert = {title: 'Holy guacamole!', content: 'Best check yo self, you\'re not looking too good.', type: 'info'};

  // Service usage
  var myAlert = $alert({title: 'Holy guacamole!', content: 'Best check yo self, you\'re not looking too good.', placement: 'top', type: 'info', keyboard: true, show: false});
  $scope.showAlert = function() {
    myAlert.show(); // or myAlert.$promise.then(myAlert.show) if you use an external html template
  };

});


angular.module('mgcrea.ngStrapDocs')

.config(function($modalProvider) {
  angular.extend($modalProvider.defaults, {
    html: true
  });
})

.controller('ModalDemoCtrl', function($scope, $modal) {
  $scope.modal = {title: 'Title', content: 'Hello Modal<br />This is a multiline message!'};


});

angular.module('mgcrea.ngStrapDocs')

.config(function($asideProvider) {
  angular.extend($asideProvider.defaults, {
    container: 'body',
    html: true
  });
})

.controller('asideCtrl', function($scope) {
  $scope.aside = {title: 'Title', content: 'Hello Aside<br />This is a multiline message!'};
});


angular.module('mgcrea.ngStrapDocs')

.config(function($tooltipProvider) {
  angular.extend($tooltipProvider.defaults, {
    html: true
  });
})

.controller('TooltipDemoCtrl', function($scope, $q, $sce, $tooltip) {

  $scope.tooltip = {title: 'Hello Tooltip<br />This is a multiline message!', checked: false};

});


angular.module('mgcrea.ngStrapDocs')

.config(function($popoverProvider) {
  angular.extend($popoverProvider.defaults, {
    html: true
  });
})

.controller('PopoverDemoCtrl', function($scope) {

  $scope.popover = {title: 'Title', 
  content: 'Hello Popover<br />This is a multiline message!<br/> <button class="btn btn-danger">cool button</button>'};
 
});


angular.module('mgcrea.ngStrapDocs')

.controller('TypeaheadDemoCtrl', function($scope, $templateCache, $http) {

  $scope.selectedState = '';
  $scope.states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Dakota', 'North Carolina', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];

  $scope.selectedIcon = '';
  $scope.icons = [
    {value: 'Gear', label: '<i class="fa fa-gear"></i> Gear'},
    {value: 'Globe', label: '<i class="fa fa-globe"></i> Globe'},
    {value: 'Heart', label: '<i class="fa fa-heart"></i> Heart'},
    {value: 'Camera', label: '<i class="fa fa-camera"></i> Camera'}
  ];

  $scope.selectedAddress = '';
  $scope.getAddress = function(viewValue) {
    var params = {address: viewValue, sensor: false};
    return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {params: params})
    .then(function(res) {
      return res.data.results;
    });
  };
});

