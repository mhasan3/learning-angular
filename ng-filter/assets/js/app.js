    var App = angular.module('popup',['ui.tree','xeditable','ui.bootstrap']);


    App.run(function(editableOptions) {
        editableOptions.theme = 'bs3';
    });


    App.directive("ngRandomClass", function () {
        return {
            restrict: 'EA',
            replace: false,
            scope: {
                ngClasses: "="
            },
            link: function (scope, elem, attr) {

                //Add random background class to selected element
                elem.addClass(scope.ngClasses[Math.floor(Math.random() * (scope.ngClasses.length))]);
            }
        }
    });


App.directive('dropdownMultiselect', function(){
   return {
       restrict: 'E',
       scope:{           
            model: '=',
            options: '=',
            pre_selected: '=preSelected'
       },
       template: "<div class='btn-group' data-ng-class='{open: open}'>"+
                "<button type='button' class='btn btn-danger'>Select</button>"+
                "<button type='button' class='btn btn-danger dropdown-toggle' data-ng-click='open=!open;openDropdown()'><span class='caret'></span></button>"+
                "<ul class='dropdown-menu' aria-labelledby='dropdownMenu'>" + 
                    "<li><a data-ng-click='selectAll()'><i class='icon-ok-sign'></i>  Check All</a></li>" +
                    "<li><a data-ng-click='deselectAll();'><i class='icon-remove-sign'></i>  Uncheck All</a></li>" +                    
                    "<li class='divider'></li>" +
                    "<li data-ng-repeat='option in options'> <a data-ng-click='setSelectedItem()'>{{option.text}}<span data-ng-class='isChecked(option)'></span></a></li>" +                                        
                "</ul>" +
            "</div>" ,
       controller: function($scope){
           
           $scope.openDropdown = function(){        
                    
            };
           
            $scope.selectAll = function () {
              //  $scope.model = _.pluck($scope.options, this.option);
              $scope.model = $scope.options;
                //console.log($scope.model);
            };            
            $scope.deselectAll = function() {
                $scope.model=[];
                //console.log($scope.model);
            };
            $scope.setSelectedItem = function(){
                var option = this.option;


                var checked;

                var check = _.filter($scope.model, function(item){ 
                    //console.log(item);
                    if (item.id == option.id ){ 
                       checked = true;
                    } 
                 });

                if(checked){
                    $scope.model = _.without($scope.model , _.findWhere($scope.model, {id : option.id} ) );
                   // $scope.model.splice()
                }else{

                    $scope.model.push(option); 
                }

                return false;
            };
            $scope.isChecked = function (option) {     

            var checked;

            var check = _.filter($scope.model, function(item){ 
                //console.log(item);
                if (item.id == option.id ){ 
                   checked = true;
                } 
             });

            if(checked){
                 return 'glyphicon glyphicon-ok pull-right';   
            }

                return false;
            };                                 
       }
   } 
});




    
    App.directive('txContenteditable', ['$compile', function($compile) {
        return {
            restrict: "A",
            scope : {
              old : '@'
            },
            require: "ngModel",
            link: function(scope, element, attrs, ngModel) {



                element.bind("click", function(e) {
                    e.preventDefault();
                });

                element.attr("contenteditable", true);

                function read() {
                    ngModel.$setViewValue(element.html());
                }

                ngModel.$render = function() {

                        element.html(ngModel.$viewValue || '' );

                };

                ngModel.$isEmpty('tareq');

                element.bind("keydown", function(e) {
                    if (e.keyCode == 13) {
                        document.execCommand('insertHTML', false, '<br><br>');
                        return false;
                    }
                });

                element.bind("blur keyup change", function(e) {
                    scope.$apply(read);
                });
            }
        };
    }]);














    App.controller('PopupController',function($scope,$filter,$http){



        // static


       // $scope.label = ['label label-success','label label-danger','label label-warning','label label-default','label label-primary','label label-info'];
        $scope.label = ['btn btn-success','btn btn-danger','btn btn-warning','btn btn-primary','btn btn-default','btn btn-info'];



        $scope.DraftMessage = false;

        $scope.postsPerPage = 10;
        $scope.contentLimit = 100;
       

        $scope.list = [];
       
        $scope.add_more = function(){
            var nodeData = $scope.list;

            $scope.list.push({
                "id" : nodeData.length + 1,
                "title" :'New',
                "items" : []
            });
           
        }

        $scope.selectedItem = {};

        $scope.options = {};

        $scope.remove = function(scope) {
            scope.remove();
        };

        $scope.toggle = function(scope) {
            scope.toggle();
        };

        $scope.open = function(scope){

        }







        $scope.sortModel = [];




        $scope.sortSettings = {
            enableSearch: true ,
            scrollable: true ,
            scrollableHeight: '200px',
            externalIdProp: ''
        };



        $scope.getTaxonomy = function(scope){



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

            if(!qs['post']){

                $scope.DraftMessage = true;

            }else{

                $scope.postType = scope;
                $scope.loading = true;

                $http({
                    method: 'POST',
                    url: ajaxurl+'?action=atmf_search_data' ,
                    data: jQuery.param({ 'type': scope }),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).then(function(e){

                    $scope.options = e.data;

                    var option_key;
                    option_key = getOptionData( e.data.metakeys );


                    $scope.sortData = option_key;

                //    console.log($scope.sortData);



                    $scope.loading = false;
                });


            }

        }


        $scope.parseInt = function(number) {
            return parseInt(number, 10);
        }


        $scope.newSubItem = function(scope) {
            var nodeData = scope.$modelValue;



            if( nodeData.option  == 'taxonomy')
            {

                if( angular.isDefined(nodeData.taxonomy) ){

                    $scope.loading = true;

                    $http({
                        method: 'POST',
                        url: ajaxurl+'?action=atmf_get_child_taxonomy' ,
                        data: jQuery.param({ 'type': $scope.postType ,'option': nodeData.option , 'taxonomy': nodeData.taxonomy ,'parent': nodeData.parent_taxonomy  }),
                        headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                    }).then(function(e){
                           


                        $scope.loading = false;

                        if(e.data.alloption)
                             nodeData.alloption = e.data.alloption;

                        if(e.data.first_parent){
                                         

                             nodeData.items.push({
                                id: nodeData.id * 10 + nodeData.items.length,
                                title: nodeData.title + '.' + (nodeData.items.length + 1),
                              
                                parent: nodeData.id,
                                items: [],
                                option : nodeData.option ,
                                taxonomy: nodeData.taxonomy,
                                parent_taxonomies : e.data.first_parent

                            });



                        }else if(e.data.child_parent){

                             nodeData.items.push({
                                id: nodeData.id * 10 + nodeData.items.length,
                                title: nodeData.title + '.' + (nodeData.items.length + 1),
                               
                                parent: nodeData.id,
                                items: [],
                                option : nodeData.option ,
                                taxonomy: nodeData.taxonomy,
                                parent_taxonomies : e.data.child_parent

                            });

                        }else{
                            alert('sorry there is not taxonomy who has children');
                        }
                    

                    }); // end of $http
             

                }
                else{
                    alert('Please select an taxonomy');
                }

            }else if( nodeData.option  == 'metadata' ){
                alert('Sorry you can not create metadata dependancy search fields');
            }else{
                alert('Please select an options');
            }

        };





        $scope.submitMeta = function(){


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


                    var save_data = {
                        list : $scope.list ,
                        option : $scope.options,
                        post_per_page : $scope.postsPerPage,
                        contentLimit  : $scope.contentLimit,
                        search_post_type : $scope.seachPostType,
                        sort_meta : $scope.sortModel,
                        page_id : qs['post']
                    };

                    $http({
                            method: 'POST',
                            url: ajaxurl+'?action=atmf_save_metadata',                            
                            data: jQuery.param({'save_data':save_data}),                            
                            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                    }).then(function(e){

                        
                            if( e.data.data.success ){
                                $scope.SuccessMessage = true;
                                // used jquery 
                                jQuery('textarea#meta-'+ e.data.data.meta_id +'-value').val( e.data.data.save_data );
                               
                            }
                            if( e.data.data.error ){
                                $scope.ErrorMessage = true;
                            }

                       
                    });

            }else{

                $scope.DraftMessage = true;

            }



        }


        // retrive the old data

        function MergeRecursive( list ){

            angular.forEach(list , function(value , key ){
                 if( angular.isObject(value) ){

                        if( angular.isDefined(value.items) ){
                            MergeRecursive(value.items);
                        }else{
                            value.items = [];
                        }
                 }
            });
        }



        function getOptionData( keepOption ){

            var option_data = [{
                'id':1,
                'text': 'Date',
                'label': 'post_date'
            },{
                'id':2,
                'text': 'Comment',
                'label': 'comment_count'
            }];


            angular.forEach(keepOption,function(opt,k){
                var build ={};
                build = {
                    'id' : k+3,
                    'text': opt,
                    'label': opt
                };

                option_data.push(build);

            });

            return option_data;
        }




        if(  angular.isDefined(search_page.metadata) && angular.isObject(search_page.metadata) ){

            $scope.list =  search_page.metadata.list;
            MergeRecursive( $scope.list );

            $scope.seachPostType = search_page.metadata.search_post_type;
            $scope.options = search_page.metadata.option;
            $scope.postsPerPage = search_page.metadata.post_per_page;
            $scope.contentLimit = search_page.metadata.contentLimit;

            var sort_model = search_page.metadata.sort_meta;

            angular.forEach(sort_model,function(value , key){
                value.id = parseInt(value.id);
            });




            if(sort_model){
                $scope.sortModel = sort_model;
            }else{
                $scope.sortModel =[];
            }

            $scope.sortData = getOptionData( search_page.metadata.option.metakeys );

        }


        $scope.closeAlert = function(scope) {
            $scope.DraftMessage = false;
        };





    });


