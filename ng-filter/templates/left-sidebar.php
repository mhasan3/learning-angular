

					<input type="text" placeholder="Quick Search" class="form-control" ng-model="QuickSearch">

					<script type="text/ng-template" id="items_renderer.html">


						<!-- for taxonomy  -->
						<div ng-if="item.option =='taxonomy' && !item.parent_taxonomy">

								<h3>{{ item.title }}</h3>

								<div ng-if="item.taxonomy">

									<div ng-show="item.viewType == 'checkbox'">
										<ul>
											<li ng-repeat="(key, value) in item.alloption">
											  <input type="checkbox" ng-change="grabResult( this ,formData[item.taxonomy][key], item)"  name="{{value}}" ng-model="formData[item.taxonomy][key]"  > {{value}}
											</li>
										</ul>
									</div>

                                    <div ng-show="item.viewType == 'select'">
                                        <select class="form-control" ng-change="grabResult( this ,formData[item.taxonomy] , item)" ng-model="formData[item.taxonomy]">
                                            <option value="">Please select</option>
                                            <option value="{{key}}" ng-repeat="(key ,value) in item.alloption">{{value}}</option>
                                        </select>
                                    </div>

								</div>

						</div>





						<!-- for metadata   -->

						<div ng-if="item.option == 'metadata' ">
							<h3>{{ item.title }}</h3>	
							<div ng-if="item.viewType =='range' ">
     								<div class="range-slider clearfix">
										<div slider min="item.rangeStart"  max="item.rangeEnd" start="item.start" end="item.end" class="cdbl-slider" onend="grabMeta()" onchnage="addTometa(item.metakey ,item.start , item.end)" key="item.metakey" ></div>
										<br/>
										<span> {{item.start}} </span>
     									<span style="float:right;"> {{item.end}} </span>
									</div>
							</div>
							<div ng-show="item.viewType == 'checkbox' ">
								<ul ng-if="item.metakey">
									<li ng-repeat="(key, value) in item.alloption">
									  <input type="checkbox" ng-change="grabMeta()" name="{{value}}" ng-model="formMeta[item.metakey][value]"> {{value}}
									</li>
								</ul>
							</div>
							<div ng-show="item.viewType == 'radio' ">
								<ul ng-if="item.metakey">
									<li ng-repeat="(key, value) in item.alloption">
									  <input type="radio"   name="{{item.metakey}}" ng-model="formMeta[item.metakey]" ng-value="{{value}}"  ng-change="grabMeta()"> {{value}}
									</li>
								</ul>					
							</div>
							<div ng-show="item.viewType == 'select' ">								
                                    <select class="form-control" ng-change="grabMeta()" ng-model="formMeta[item.metakey]" ng-options="o as o for o in item.alloption"></select>
     						</div>

						</div>





						<!--  second stage  , it will show after its parent show  	-->		
						<div ng-show="selected_taxonomy.indexOf(item.parent_taxonomy)!=-1">
								<h3>{{ item.title }}</h3>	


                            <div ng-if="item.taxonomy">

                                <div ng-show="item.viewType == 'checkbox'">
                                    <ul>
                                        <li ng-repeat="(key, value) in item.alloption">
                                            <input type="checkbox" ng-change="grabResult( this ,formData[item.taxonomy][key], item)"  name="{{value}}" ng-model="formData[item.taxonomy][key]"  > {{value}}
                                        </li>
                                    </ul>
                                </div>

<!--                                <div ng-show="item.viewType == 'select'">-->
<!--                                    <select class="form-control" ng-change="grabResult( this ,formData[item.taxonomy] , item)" ng-model="formData[item.taxonomy]">-->
<!--                                        <option value="">Please select</option>-->
<!--                                        <option value="{{key}}" ng-repeat="(key ,value) in item.alloption">{{value}}</option>-->
<!--                                    </select>-->
<!--                                </div>-->

                            </div>

						</div>	
						<div ng-repeat="item in item.items" ng-include="'items_renderer.html'"></div>




					</script>


					<div ng-repeat="item in list" ng-include="'items_renderer.html'"></div>	 

					<a ng-click="doFilter()" class="btn btn-primary filter-btn" href="#"> <?php  _e('Filter','atmf');  ?></a>
					<a ng-click="doReset()" class="btn btn-primary filter-btn" href="#"> <?php  _e('Reset','atmf');  ?></a>