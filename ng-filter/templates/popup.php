<?php

$ajax_nonce = wp_create_nonce( "tmf" );
$args = array(
    // 'public'   => true,
    // '_builtin' => false
);
$output = 'names';
$operator = 'and';

$post_types = get_post_types( $args, $output, $operator );
?>



<div ng-app="popup">
    <div ng-controller="PopupController">

       
                <div style="min-height:200px;">
                   
                        <div class="row">

                               <div class="col-md-5">
                                    <label class="control-label" for="search_post_type"><?php  _e('Select Post Type','atmf');  ?></label>
                                    <select class="form-control"   ng-change="getTaxonomy(seachPostType)"  name="search_post_type" id="search_post_type" ng-model="seachPostType">

                                        <option value=""><?php  _e('Please Select One','atmf');  ?></option>

                                        <?php
                                            foreach ( $post_types as $post_type ) {
                                                echo '<option value="'.$post_type.'">' . $post_type . '</option>';
                                            }
                                        ?>
                                    </select>
                               </div>


                           <div class="col-md-2">
                               <label for="post_per_page"><?php _e('Posts per page ','atmf');  ?></label>
                               <input type="text" class="form-control" ng-model="postsPerPage"  placeholder="<?php  _e('Posts Per Page','atmf');  ?>"/>
                               
                           </div>

                           <div class="col-md-2">
                               <label for="contentLimit"><?php _e('Content Limit','atmf');  ?></label>
                               <input type="text" class="form-control pull-right" ng-model="contentLimit"  placeholder="<?php  _e('Post content limit','atmf'); ?>"/>
                           </div>

                        </div>



                        <div id="security_value" style="display: none;"><?php echo $ajax_nonce;  ?></div>


                        <div class="loading" ng-show="loading"><i></i><i></i><i></i></div>



                        <div class="second_show" ng-show="seachPostType">



                                <div id="search_page_builder">



                                    <label for="">Add sort option</label>

                                    <dropdown-multiselect pre-selected="sortModel" model="sortModel" options="sortData"></dropdown-multiselect>

                                    
    
                                    <!-- <div  ng-dropdown-multiselect=""  options="sortData" selected-model="sortModel" extra-settings="sortSettings"></div> -->

                                    <div class="col col-md-7 sort-holder pull-right">
                                            <a tx-contenteditable ng-model="sortitem.text" class="btn btn-success" ng-repeat="sortitem in sortModel" href="#">
                                                   {{ sortitem.text || ' ' }}  <span> </span>
                                            </a>
                                            &nbsp;
                                    </div>

                                    <div class="clearfix"></div>
                                    <a href="#" class="btn btn-primary"  id="add_more" ng-click="add_more()"><?php  _e('Add independent search option ','atmf');  ?></a>
                                    <br/><br/>
                                </div>

                                <!-- Nested list template -->
                                <script type="text/ng-template" id="items_renderer.html">
                                    <div ui-tree-handle>
                                        
                                        <a data-nodrag tooltip="Click To Edit" href="#"  editable-text="item.title">{{ item.title }}</a>
<!--                                        <a data-nodrag tooltip="Click To Edit" href="#" class="btn" tx-contenteditable  ng-model="item.title">{{ item.title }}</a>-->
                                       
                                       
                                        <a class="pull-right btn btn-danger btn-xs" data-nodrag ng-click="remove(this)"><span class="glyphicon glyphicon-remove"></span></a>
                                         <a class="pull-right btn btn-primary btn-xs" tooltip=" <?php  _e('Add Dependent Search Option','atmf');  ?>" data-nodrag ng-click="newSubItem(this)" style="margin-right: 8px;"><span class="glyphicon glyphicon-plus"></span></a>  
                                        <a class="pull-right btn btn-success btn-xs" tooltip=" <?php _e('Click To Open','atmf'); ?>"  data-nodrag ng-click="toggle(this)"><span class="glyphicon" ng-class="{'glyphicon-chevron-down': collapsed, 'glyphicon-chevron-right': !collapsed}"></span></a>
                                       
                                       
                                    
                                    </div>
                                    <ol  ui-tree-nodes="options" ng-model="item.items" ng-class="{disnone: !collapsed}">



                                <div class="holder" >

                                    <div class="row">
                                        <div class="col-md-5">
                                             
                                             <div ng-hide="item.parent">
                                                <label for=""> <?php  _e('Select filter option ','atmf');  ?></label>
                                                <select class="form-control select_filter_option" ng-model="item.option" name="data_type" >
                                                    <option value=""> <?php  _e('Select Filter Option','atmf');  ?></option>
                                                    <option value="taxonomy"> <?php  _e('Taxonomy','atmf');  ?></option>
                                                    <option value="metadata"> <?php  _e('Metadata','atmf');  ?></option>
                                                </select>
                                                
                                                <div ng-show="item.option =='taxonomy'">
                                                    <label for=""> <?php  _e('Please select','atmf');  ?></label>
                                                    <select name="" class="form-control"  ng-init="item.parent_taxonomy = ( item.parent_taxonomy ).toString()" ng-model="item.taxonomy"  ng-options="value as value for (key, value) in options.taxonomies">
                                                        <option value=""> <?php  _e('Please select','atmf');  ?></option>
                                                    </select>

                                                    <br/>
                                                </div>



                                                <div ng-show="item.option =='metadata'">
                                                    <label for=""> <?php  _e('Please select','atmf');  ?></label>
                                                    <select name="" id="" class="form-control" ng-model="item.metakey" ng-options="value as value for (key, value) in options.metakeys">
                                                        <option value=""><?php  _e('Please select','atmf');  ?></option>
                                                    </select>
                                                </div>
                                        </div>


                                        <div ng-show="item.parent">
                                            <select class="form-control" ng-init="item.parent_taxonomy = ( item.parent_taxonomy ).toString()" ng-model="item.parent_taxonomy" ng-options=" key as value for (key , value) in item.parent_taxonomies">
                                                <option value=""><?php  _e('Please select','atmf');  ?></option>
                                            </select>
                                        </div>


                                        </div>


                                        <div class="col-md-5">

                                             <div>
                                                    <h2>view options <?php  _e('Please select','atmf');  ?></h2>

                                                    

                                                    <div ng-if="item.option =='taxonomy'">
                                                        <label for=""><?php  _e('Choose your view option','atmf');  ?></label>

                                                        <select class="form-control" ng-model="item.viewType">
                                                            <option value="checkbox"> Checkbox </option>
<!--                                                            <option value="select"> Select </option>-->
                                                        </select>
                                                    </div>

                                                    <div ng-if="item.option =='metadata'">
                                                        <label for=""><?php  _e('Choose your view option','atmf');  ?></label>
                                                        <select  class="form-control" ng-model="item.viewType">
                                                            <option value=""><?php  _e('Please select','atmf');  ?></option>
                                                            <option value="checkbox"> Checkbox </option>                                                       
                                                            <option value="radio"> Radio </option>                                                       
                                                            <option value="select"> Select </option>                                                       
                                                            <option value="range"> Range </option>
                                                        </select>
                                                    </div>





                                                    <div class="range" ng-show="item.viewType =='range'">
                                                        <label for=""> <?php  _e('Start Range','atmf');  ?></label>
                                                        <input class="form-control" type="text" placeholder="Start" ng-model="item.rangeStart"/>
                                                        
                                                        <label for=""><?php  _e('End Range','atmf');  ?></label>
                                                        <input class="form-control" type="text" placeholder="End" ng-model="item.rangeEnd"/>
                                                        
                                                        <label for=""><?php  _e('Unit','atmf');  ?></label>
                                                        <input type="text" placeholder="Unit" class="form-control" ng-model="item.rangeUnit" />
                                                    </div>
                                            </div>

                                        </div>


                                     </div>   
                                 </div> 

        
                                        <li ng-repeat="item in item.items" ui-tree-node ng-include="'items_renderer.html'">

                                        </li>
                                    </ol>


                                </script>
                                <div ui-tree="options">
                                    <ol ui-tree-nodes ng-model="list" >
                                        <li ng-repeat="item in list" ui-tree-node ng-include="'items_renderer.html'">
                            
                                        </li>     
                                    </ol>


                                
                                </div>
                        </div>



                        <alert type="danger" ng-show="DraftMessage" close="closeAlert(this)"><?php  _e('Please atleast save the page as Draft , need page id to store the data','atmf');  ?></alert>
                        <alert type="success" ng-show="SuccessMessage" ng-click="SuccessMessage = false" close="closeSuccess(this)"><?php  _e('Successfully added data ','atmf');  ?></alert>
                        <alert type="danger" ng-show="ErrorMessage" ng-click="ErrorMessage = false" close="closeError(this)"><?php  _e('Sorry , There was a problem','atmf');  ?> </alert>

                </div>
                <div class="clearfix"></div>
                      

                <div class="row">
                    <button type="button" id="search_submit" class="btn btn-primary pull-right" ng-click="submitMeta()"><?php  _e('Save changes','atmf');  ?></button>
                </div>     

            
    </div>
</div>





