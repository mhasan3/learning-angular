
        <div class="row control-area">
            <ul class="view-toggle pull-left">
                <li><a href="#" ng-click="postView='list'" data-layout="with-thumb" class="btn glyphicon glyphicon-th-list"></a></li>
                <li><a href="#" ng-click="postView='grid'" data-layout="" class="btn glyphicon glyphicon-th"></a></li>
            </ul>


            <div class="col-md-2 pull-right" >

                    <select class="form-control"  ng-model="postsPerPage">
                        <option value="3">3</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
            </div>
            <div class="col-md-4 pull-right">
                <div class="row">
                    <div class="col-md-8">
                        <select class=" form-control pull-left" ng-init="postOrder.order = 'reverse'" ng-model="postOrder.type"  >
                            <option value="">Sort By</option>
                            <option ng-repeat="(key ,value) in sortData" value="{{value.label}}">{{value.text}}</option>
                        </select>
                    </div>

                    <div class="col-md-4">

                        <a href="#" ng-click="postOrder.order = false " class="btn btn-xs btn-praimary pull-right">
                            <span class="glyphicon glyphicon-arrow-down"></span>
                        </a>

                        <a href="#" ng-click="postOrder.order = 'reverse' " class="btn btn-xs btn-praimary pull-right">
                            <span class="glyphicon glyphicon-arrow-up"></span>
                        </a>

                    </div>

                </div>



            </div>



        </div>


    	<div class="clearfix"></div>
		
		<div dir-paginate="post in posts | itemsPerPage: postsPerPage | filter: QuickSearch | orderBy:postOrder.type:postOrder.order">
         
               <div ng-if="postView == 'list' " class="row post-item">
                    <div class="col-md-2"><img class="thumbnail" ng-src="{{ post.post_thumbnail }}"/></div>
                    <div class="col-md-10">

                        <a style="font-size: 25px;" href="{{ post.post_permalink }}" ng-bind="post.post_title"></a>

                        <p ng-bind-html="post.post_content | limitTo: contentLimit | html"></p>
                    </div>
               </div>

               <div ng-if="postView == 'grid'" class="col col-md-4 post-item-grid">
					<img class="thumbnail" ng-src="{{ post.post_thumbnail }}"/>
					<a href="{{ post.post_permalink }}">
                        <h3 ng-bind="post.post_title"></h3>
                    </a>
               		
               </div>
		</div>

		<div class="loading" ng-show="loading"><i></i><i></i><i></i></div>		
		<alert style="margin-top:200px;" type="danger" ng-show="( posts | filter:QuickSearch).length==0">
            Sorry No Result Found
		</alert>
		
		<div class="clearfix"></div>
		
		<!-- Start pagination  -->
		<dir-pagination-controls boundary-links="true" class="pull-right" on-page-change="pageChangeHandler(newPageNumber)" template-url="<?php  echo UOU_ATMF_URL .'/assets/js/vendor/angular-utils-pagination/dirPagination.tpl.html';  ?>"></dir-pagination-controls>
		<!-- End pagination  -->