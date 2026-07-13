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
                        <button class="btn btn-primary btn-sm" id='createOrder'>Create Production Order</button>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="ordersListPerPage">Show</label>
                        <select id="ordersListPerPage" class="form-control">
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
                        <label for="ordersListSortBy">Sort by</label>
                        <select id="ordersListSortBy" class="form-control">
                            <option value="order_code-ASC">Order Code (A-Z)</option>
                            <option value="order_code-DESC">Order Code (Z-A)</option>
                            <option value="start_date-DESC">Start Date (Latest first)</option>
                            <option value="start_date-ASC">Start Date (Oldest first)</option>
                            <option value="priority-DESC">Priority (Highest first)</option>
                            <option value="status-ASC">Status</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='orderSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="orderSearch" class="form-control" placeholder="Search Orders">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- row of adding new order form and orders list table-->
    <div class="row">
        <div class="col-sm-12">
            <!--Form to add a new order-->
            <div class="col-sm-4 hidden" id='createNewOrderDiv'>
                <div class="well">
                    <button class="close cancelAddOrder">&times;</button><br>
                    <form name="addNewOrderForm" id="addNewOrderForm" role="form">
                        <div class="text-center errMsg" id='addOrderErrMsg'></div>
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="orderCode">Order Code</label>
                                <input type="text" id="orderCode" name="orderCode" placeholder="Order Code (auto or manual)" maxlength="50"
                                    class="form-control" onchange="checkField(this.value, 'orderCodeErr')" autofocus>
                                <span class="help-block errMsg" id="orderCodeErr"></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="orderItem">Item</label>
                                <select id="orderItem" name="orderItem" class="form-control" onchange="checkField(this.value, 'orderItemErr')">
                                    <option value="">-- Select Item --</option>
                                    <?php if(isset($items) && $items): ?>
                                    <?php foreach($items as $item): ?>
                                    <option value="<?=$item->id?>"><?=$item->name?> (<?=$item->code?>)</option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <span class="help-block errMsg" id="orderItemErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="orderQuantity">Quantity</label>
                                <input type="number" id="orderQuantity" name="orderQuantity" placeholder="Quantity to Produce"
                                    class="form-control" min="1" onchange="checkField(this.value, 'orderQuantityErr')">
                                <span class="help-block errMsg" id="orderQuantityErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group-sm">
                                <label for="orderStartDate">Start Date</label>
                                <input type="date" id="orderStartDate" name="orderStartDate" class="form-control"
                                    onchange="checkField(this.value, 'orderStartDateErr')">
                                <span class="help-block errMsg" id="orderStartDateErr"></span>
                            </div>
                            <div class="col-sm-6 form-group-sm">
                                <label for="orderEndDate">End Date</label>
                                <input type="date" id="orderEndDate" name="orderEndDate" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="orderPriority">Priority</label>
                                <select id="orderPriority" name="orderPriority" class="form-control">
                                    <option value="Normal" selected>Normal</option>
                                    <option value="Low">Low</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="orderNotes">Notes (Optional)</label>
                                <textarea class="form-control" id="orderNotes" name="orderNotes" rows='3'
                                    placeholder="Optional Notes"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewOrder">Create Order</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddOrder" class="btn btn-danger btn-sm cancelAddOrder" form='addNewOrderForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
            
            <!--- Orders list div-->
            <div class="col-sm-12" id="ordersListDiv">
                <!-- Orders list Table-->
                <div class="row">
                    <div class="col-sm-12" id="ordersListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of orders list div-->

        </div>
    </div>
    <!-- End of row-->
</div>

<!--modal to edit order-->
<div id="editOrderModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Production Order</h4>
                <div id="editOrderFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="orderCodeEdit">Order Code</label>
                            <input type="text" id="orderCodeEdit" placeholder="Order Code" class="form-control" readonly>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="orderItemEdit">Item</label>
                            <select id="orderItemEdit" class="form-control checkField">
                                <option value="">-- Select Item --</option>
                                <?php if(isset($items) && $items): ?>
                                <?php foreach($items as $item): ?>
                                <option value="<?=$item->id?>"><?=$item->name?> (<?=$item->code?>)</option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <span class="help-block errMsg" id="orderItemEditErr"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="orderQuantityEdit">Quantity</label>
                            <input type="number" id="orderQuantityEdit" placeholder="Quantity" class="form-control checkField" min="1">
                            <span class="help-block errMsg" id="orderQuantityEditErr"></span>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="orderStartDateEdit">Start Date</label>
                            <input type="date" id="orderStartDateEdit" class="form-control">
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="orderEndDateEdit">End Date</label>
                            <input type="date" id="orderEndDateEdit" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="orderPriorityEdit">Priority</label>
                            <select id="orderPriorityEdit" class="form-control">
                                <option value="Low">Low</option>
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="orderStatusEdit">Status</label>
                            <select id="orderStatusEdit" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="orderNotesEdit">Notes (Optional)</label>
                            <textarea class="form-control" id="orderNotesEdit" placeholder="Optional Notes"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="orderIdEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="editOrderSubmit">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of edit order modal-->

<!--modal to log production-->
<div id="logProductionModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Log Production</h4>
                <div id="logProductionFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="logProductionForm" id="logProductionForm" role="form">
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label>Order Code</label>
                            <input type="text" readonly id="logProdOrderCode" class="form-control">
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label>Item</label>
                            <input type="text" readonly id="logProdItemName" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="logProdMachine">Machine</label>
                            <select id="logProdMachine" class="form-control checkField">
                                <option value="">-- Select Machine --</option>
                            </select>
                            <span class="help-block errMsg" id="logProdMachineErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="logProdShift">Shift</label>
                            <select id="logProdShift" class="form-control checkField">
                                <option value="">---</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Night">Night</option>
                            </select>
                            <span class="help-block errMsg" id="logProdShiftErr"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="logProdQuantity">Quantity Produced</label>
                            <input type="number" id="logProdQuantity" placeholder="Quantity Produced"
                                class="form-control checkField" min="0">
                            <span class="help-block errMsg" id="logProdQuantityErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="logProdRemarks">Remarks</label>
                            <textarea class="form-control" id="logProdRemarks" placeholder="Optional Remarks"></textarea>
                        </div>
                    </div>
                    
                    <input type="hidden" id="logProdOrderId">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="logProductionSubmit">Log Production</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of log production modal-->
<script src="<?=base_url()?>public/js/production_orders.js"></script>
