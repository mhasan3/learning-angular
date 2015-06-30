<!-- <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.8/angular.min.js"></script> -->

<style type="text/css">
	.single-item {
		padding: 20px;
		border: 1px solid #ccc;
		margin-bottom: 20px;
		margin-top: 20px;
	}

	.config {
		padding: 30px;
		margin-top: 20px;
		background: #F9F9F9;
	}

</style>




<div ng-app="SettingsApp">
	<div class="" ng-controller="SetCont">





		<button type="button" class="btn btn-primary" ng-click="doAddBlock()">Add</button>


			<div class="single-item" ng-repeat="item in items">

					 <span ng-click="item.details = !item.details" class="glyphicon glyphicon-th-list pin"></span> 
					   <a class="pull-right btn btn-xs cross"  ng-click="removeItem(item.$$hashKey , items )"><span class="glyphicon glyphicon-remove"></span></a>

					 <h2>{{item.title}}</h2>	


					<!-- hidden  -->
					<div class="config" ng-class="{'hidden': ! item.details , 'animated zoomIn': item.details }">

						<div class="col-xs-5">

							<label>Title</label>
							<input type="text" ng-model="item.title" placeholder="Title" class="form-control"> <br>

							<label>Type</label> 
							<select ng-model="item.type" class="form-control">
								<option value="select"> Select </option>
								<option value="checkbox"> Checkbox </option>
							</select>

							<br>


						</div>
							<div class="clearfix"></div>

							<br>


							<div ng-if="item.type == 'select' ">
								
								<button type="button" class="btn btn-success" ng-click="addOption(item)">Add Option</button> <br> <br>
								<div ng-repeat="option in item.options">
								<br>
									  <a class="pull-right btn btn-xs cross"  ng-click="removeOption(option.$$hashKey , item.options )"><span class="glyphicon glyphicon-remove"></span></a>
									  <input type="text" ng-model="option.text" placeholder="text">
									  <input type="text" ng-model="option.price" placeholder="price">
									  <br>
								</div>
									
								<br><br>							


						  		<label>Save as product variation </label>

								  <select ng-model="item.variation">									  	
						          	<option value="yes"> Yes </option>
						          	<option value="no"> No </option>
						          </select>

				
								
							</div>


							<div ng-if="item.type == 'checkbox' ">
								  <input type="number" placeholder="Price" class="form-control" ng-model="item.price">			
							</div>


					</div>
					<!-- hidden  -->

									


			</div> 
			<!-- item loop  -->

			<br>

			[ Note : Please select variation just for one element , it won't work if you select for two ]


                         <alert type="danger" ng-show="DraftMessage" close="closeAlert(this)"><?php  _e('Please atleast save the page as Draft , need page id to store the data','takeaway');  ?></alert>
                         <alert type="success" ng-show="SuccessMessage" ng-click="SuccessMessage = false" close="closeSuccess(this)"><?php  _e('Successfully added data ','takeaway');  ?></alert>
                         <alert type="danger" ng-show="ErrorMessage" ng-click="ErrorMessage = false" close="closeError(this)"><?php  _e('Sorry , There was a problem','takeaway');  ?> </alert>


			<br>

			<button type="button" class="btn btn-primary" ng-click="saveAsMeta()">Save</button>
		
	</div>
</div>
