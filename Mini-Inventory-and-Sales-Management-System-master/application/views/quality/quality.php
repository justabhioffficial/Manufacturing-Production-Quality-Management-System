<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">   
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createInspection'>Add Inspection</button>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="inspectionsListPerPage">Show</label>
                        <select id="inspectionsListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>per page</label>
                    </div>

                    <div class="col-sm-4 form-group-sm form-inline">
                        <label for="inspectionsListSortBy">Sort by</label>
                        <select id="inspectionsListSortBy" class="form-control">
                            <option value="inspection_date-DESC">Inspection Date (Latest first)</option>
                            <option value="inspection_date-ASC">Inspection Date (Oldest first)</option>
                            <option value="qty_inspected-DESC">Qty Inspected (Highest first)</option>
                            <option value="status-ASC">Status</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='inspectionSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="inspectionSearch" class="form-control" placeholder="Search Inspections">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- row of adding new inspection form and inspections list table-->
    <div class="row">
        <div class="col-sm-12">
            <!--Form to add a new inspection-->
            <div class="col-sm-4 hidden" id='createNewInspectionDiv'>
                <div class="well">
                    <button class="close cancelAddInspection">&times;</button><br>
                    <form name="addNewInspectionForm" id="addNewInspectionForm" role="form">
                        <div class="text-center errMsg" id='addInspectionErrMsg'></div>
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="inspOrderId">Production Order</label>
                                <select id="inspOrderId" name="inspOrderId" class="form-control" onchange="checkField(this.value, 'inspOrderIdErr')">
                                    <option value="">-- Select Production Order --</option>
                                    <?php if(isset($orders) && $orders): ?>
                                    <?php foreach($orders as $order): ?>
                                    <option value="<?=$order->id?>"><?=$order->order_code?> - <?=$order->item_name?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <span class="help-block errMsg" id="inspOrderIdErr"></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="inspDate">Inspection Date</label>
                                <input type="date" id="inspDate" name="inspDate" class="form-control"
                                    onchange="checkField(this.value, 'inspDateErr')" value="<?=date('Y-m-d')?>">
                                <span class="help-block errMsg" id="inspDateErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="inspQtyInspected">Qty Inspected</label>
                                <input type="number" id="inspQtyInspected" name="inspQtyInspected" placeholder="Qty Inspected"
                                    class="form-control" min="0" onchange="checkField(this.value, 'inspQtyInspectedErr')">
                                <span class="help-block errMsg" id="inspQtyInspectedErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group-sm">
                                <label for="inspQtyPassed">Qty Passed</label>
                                <input type="number" id="inspQtyPassed" name="inspQtyPassed" placeholder="Qty Passed"
                                    class="form-control" min="0" onchange="checkField(this.value, 'inspQtyPassedErr')">
                                <span class="help-block errMsg" id="inspQtyPassedErr"></span>
                            </div>
                            <div class="col-sm-6 form-group-sm">
                                <label for="inspQtyFailed">Qty Failed (Auto)</label>
                                <input type="number" id="inspQtyFailed" name="inspQtyFailed" placeholder="Auto-calculated"
                                    class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="inspStatus">Status</label>
                                <select id="inspStatus" name="inspStatus" class="form-control">
                                    <option value="Passed">Passed</option>
                                    <option value="Failed">Failed</option>
                                    <option value="Partial">Partial</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="inspRemarks">Remarks (Optional)</label>
                                <textarea class="form-control" id="inspRemarks" name="inspRemarks" rows='3'
                                    placeholder="Optional Remarks"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewInspection">Add Inspection</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddInspection" class="btn btn-danger btn-sm cancelAddInspection" form='addNewInspectionForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
            
            <!--- Inspections list div-->
            <div class="col-sm-12" id="inspectionsListDiv">
                <!-- Inspections list Table-->
                <div class="row">
                    <div class="col-sm-12" id="inspectionsListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of inspections list div-->

        </div>
    </div>
    <!-- End of row-->
</div>
<script src="<?=base_url()?>public/js/quality.js"></script>
