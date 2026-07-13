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
                        <button class="btn btn-primary btn-sm" id='createMachine'>Add New Machine</button>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="machinesListPerPage">Show</label>
                        <select id="machinesListPerPage" class="form-control">
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
                        <label for="machinesListSortBy">Sort by</label>
                        <select id="machinesListSortBy" class="form-control">
                            <option value="machine_name-ASC">Name (A-Z)</option>
                            <option value="machine_name-DESC">Name (Z-A)</option>
                            <option value="machine_code-ASC">Code (Ascending)</option>
                            <option value="machine_code-DESC">Code (Descending)</option>
                            <option value="status-ASC">Status</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='machineSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="machineSearch" class="form-control" placeholder="Search Machines">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- row of adding new machine form and machines list table-->
    <div class="row">
        <div class="col-sm-12">
            <!--Form to add a new machine-->
            <div class="col-sm-4 hidden" id='createNewMachineDiv'>
                <div class="well">
                    <button class="close cancelAddMachine">&times;</button><br>
                    <form name="addNewMachineForm" id="addNewMachineForm" role="form">
                        <div class="text-center errMsg" id='addMachineErrMsg'></div>
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="machineCode">Machine Code</label>
                                <input type="text" id="machineCode" name="machineCode" placeholder="Machine Code" maxlength="50"
                                    class="form-control" onchange="checkField(this.value, 'machineCodeErr')" autofocus>
                                <span class="help-block errMsg" id="machineCodeErr"></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="machineName">Machine Name</label>
                                <input type="text" id="machineName" name="machineName" placeholder="Machine Name" maxlength="100"
                                    class="form-control" onchange="checkField(this.value, 'machineNameErr')">
                                <span class="help-block errMsg" id="machineNameErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="machineType">Type</label>
                                <input type="text" id="machineType" name="machineType" placeholder="Machine Type" maxlength="100"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="machineLocation">Location</label>
                                <input type="text" id="machineLocation" name="machineLocation" placeholder="Location" maxlength="100"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="machineNotes">Notes (Optional)</label>
                                <textarea class="form-control" id="machineNotes" name="machineNotes" rows='3'
                                    placeholder="Optional Notes"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewMachine">Add Machine</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddMachine" class="btn btn-danger btn-sm cancelAddMachine" form='addNewMachineForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
            
            <!--- Machine list div-->
            <div class="col-sm-12" id="machinesListDiv">
                <!-- Machine list Table-->
                <div class="row">
                    <div class="col-sm-12" id="machinesListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of machine list div-->

        </div>
    </div>
    <!-- End of row of adding new machine form and machines list table-->
</div>

<!--modal to edit machine-->
<div id="editMachineModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Machine</h4>
                <div id="editMachineFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="machineCodeEdit">Machine Code</label>
                            <input type="text" id="machineCodeEdit" placeholder="Machine Code" class="form-control checkField">
                            <span class="help-block errMsg" id="machineCodeEditErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="machineNameEdit">Machine Name</label>
                            <input type="text" id="machineNameEdit" placeholder="Machine Name" autofocus class="form-control checkField">
                            <span class="help-block errMsg" id="machineNameEditErr"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="machineTypeEdit">Type</label>
                            <input type="text" id="machineTypeEdit" placeholder="Machine Type" class="form-control">
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="machineLocationEdit">Location</label>
                            <input type="text" id="machineLocationEdit" placeholder="Location" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="machineNotesEdit">Notes (Optional)</label>
                            <textarea class="form-control" id="machineNotesEdit" placeholder="Optional Notes"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="machineIdEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="editMachineSubmit">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of edit modal-->

<!--modal to update status-->
<div id="updateStatusModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Update Machine Status</h4>
                <div id="updateStatusFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label>Machine</label>
                            <input type="text" readonly id="statusMachineName" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="machineStatusSelect">Status</label>
                            <select id="machineStatusSelect" class="form-control checkField">
                                <option value="">---</option>
                                <option value="Idle">Idle</option>
                                <option value="Running">Running</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Down">Down</option>
                            </select>
                            <span class="help-block errMsg" id="machineStatusSelectErr"></span>
                        </div>
                    </div>
                    <input type="hidden" id="statusMachineId">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="updateStatusSubmit">Update</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of status modal-->
<script src="<?=base_url()?>public/js/machines.js"></script>
