<?php defined('BASEPATH') OR exit('') ?>

<div class='col-sm-6'>
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>

<div class='col-sm-6 text-right'><b>Total Machines:</b> <?=isset($totalMachines) ? $totalMachines : '0'?></div>

<div class='col-xs-12'>
    <div class="panel panel-primary">
        <!-- Default panel contents -->
        <div class="panel-heading">Machines</div>
        <?php if($allMachines): ?>
        <div class="table table-responsive">
            <table class="table table-bordered table-striped table-hover" style="background-color: #f5f5f5">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>MACHINE CODE</th>
                        <th>MACHINE NAME</th>
                        <th>TYPE</th>
                        <th>LOCATION</th>
                        <th>STATUS</th>
                        <th>LAST UPDATED</th>
                        <th>EDIT</th>
                        <th>STATUS</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allMachines as $get): ?>
                    <tr>
                        <input type="hidden" value="<?=$get->id?>" class="curMachineId">
                        <th class="machineSN"><?=$sn?>.</th>
                        <td><span id="machineCode-<?=$get->id?>"><?=$get->machine_code?></span></td>
                        <td><span id="machineName-<?=$get->id?>"><?=$get->machine_name?></span></td>
                        <td><span id="machineType-<?=$get->id?>"><?=$get->type?></span></td>
                        <td><span id="machineLocation-<?=$get->id?>"><?=$get->location?></span></td>
                        <td>
                            <?php
                                $statusColor = 'default';
                                if($get->status === 'Running') $statusColor = 'success';
                                elseif($get->status === 'Idle') $statusColor = 'warning';
                                elseif($get->status === 'Down') $statusColor = 'danger';
                                elseif($get->status === 'Maintenance') $statusColor = 'warning';
                            ?>
                            <span class="label label-<?=$statusColor?>" id="machineStatus-<?=$get->id?>"><?=$get->status?></span>
                        </td>
                        <td><?=date('d M Y H:i', strtotime($get->updated_at))?></td>
                        <td class="text-center text-primary">
                            <span class="editMachine" id="editM-<?=$get->id?>"><i class="fa fa-pencil pointer"></i></span>
                        </td>
                        <td class="text-center">
                            <span class="updateMachineStatus pointer" id="statusM-<?=$get->id?>"><i class="fa fa-refresh text-info"></i></span>
                        </td>
                        <td class="text-center"><i class="fa fa-trash text-danger delMachine pointer"></i></td>
                    </tr>
                    <?php $sn++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- table div end-->
        <?php else: ?>
        <ul><li>No machines</li></ul>
        <?php endif; ?>
    </div>
    <!--- panel end-->
</div>

<!---Pagination div-->
<div class="col-sm-12 text-center">
    <ul class="pagination">
        <?= isset($links) ? $links : "" ?>
    </ul>
</div>
