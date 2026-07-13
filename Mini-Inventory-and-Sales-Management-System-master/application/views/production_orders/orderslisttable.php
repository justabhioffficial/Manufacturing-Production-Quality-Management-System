<?php defined('BASEPATH') OR exit('') ?>

<div class='col-sm-6'>
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>

<div class='col-sm-6 text-right'><b>Total Orders:</b> <?=isset($totalOrders) ? $totalOrders : '0'?></div>

<div class='col-xs-12'>
    <div class="panel panel-primary">
        <!-- Default panel contents -->
        <div class="panel-heading">Production Orders</div>
        <?php if($allOrders): ?>
        <div class="table table-responsive">
            <table class="table table-bordered table-striped table-hover" style="background-color: #f5f5f5">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>ORDER CODE</th>
                        <th>ITEM NAME</th>
                        <th>QTY ORDERED</th>
                        <th>QTY PRODUCED</th>
                        <th>PROGRESS</th>
                        <th>STATUS</th>
                        <th>PRIORITY</th>
                        <th>START DATE</th>
                        <th>END DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allOrders as $get): ?>
                    <tr>
                        <input type="hidden" value="<?=$get->id?>" class="curOrderId">
                        <th class="orderSN"><?=$sn?>.</th>
                        <td><span id="orderCode-<?=$get->id?>"><?=$get->order_code?></span></td>
                        <td><span id="orderItemName-<?=$get->id?>"><?=$get->item_name?></span></td>
                        <td><span id="orderQtyOrdered-<?=$get->id?>"><?=$get->quantity_ordered?></span></td>
                        <td><span id="orderQtyProduced-<?=$get->id?>"><?=$get->quantity_produced?></span></td>
                        <td style="min-width:120px">
                            <?php 
                                $progress = $get->quantity_ordered > 0 ? round(($get->quantity_produced / $get->quantity_ordered) * 100) : 0;
                                $progressClass = 'info';
                                if($progress >= 100) $progressClass = 'success';
                                elseif($progress >= 50) $progressClass = 'primary';
                                elseif($progress >= 25) $progressClass = 'warning';
                            ?>
                            <div class="progress" style="margin-bottom:0">
                                <div class="progress-bar progress-bar-<?=$progressClass?>" role="progressbar" 
                                    aria-valuenow="<?=$progress?>" aria-valuemin="0" aria-valuemax="100" 
                                    style="width:<?=$progress?>%; min-width:2em;">
                                    <?=$progress?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $statusColor = 'default';
                                if($get->status === 'Pending') $statusColor = 'info';
                                elseif($get->status === 'In Progress') $statusColor = 'warning';
                                elseif($get->status === 'Completed') $statusColor = 'success';
                                elseif($get->status === 'Cancelled') $statusColor = 'danger';
                            ?>
                            <span class="label label-<?=$statusColor?>" id="orderStatus-<?=$get->id?>"><?=$get->status?></span>
                        </td>
                        <td>
                            <?php
                                $priorityColor = 'default';
                                if($get->priority === 'Low') $priorityColor = 'default';
                                elseif($get->priority === 'Normal') $priorityColor = 'info';
                                elseif($get->priority === 'High') $priorityColor = 'warning';
                                elseif($get->priority === 'Urgent') $priorityColor = 'danger';
                            ?>
                            <span class="label label-<?=$priorityColor?>" id="orderPriority-<?=$get->id?>"><?=$get->priority?></span>
                        </td>
                        <td><span id="orderStartDate-<?=$get->id?>"><?=$get->start_date ? date('d M Y', strtotime($get->start_date)) : '-'?></span></td>
                        <td><span id="orderEndDate-<?=$get->id?>"><?=$get->end_date ? date('d M Y', strtotime($get->end_date)) : '-'?></span></td>
                        <td class="text-center" style="min-width:120px">
                            <span class="editOrder pointer" id="editO-<?=$get->id?>" data-toggle="tooltip" title="Edit Order"><i class="fa fa-pencil text-primary"></i></span>
                            &nbsp;
                            <span class="logProduction pointer" id="logP-<?=$get->id?>" data-toggle="tooltip" title="Log Production"><i class="fa fa-plus-circle text-success"></i></span>
                            &nbsp;
                            <span class="delOrder pointer" data-toggle="tooltip" title="Delete Order"><i class="fa fa-trash text-danger"></i></span>
                        </td>
                    </tr>
                    <?php $sn++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- table div end-->
        <?php else: ?>
        <ul><li>No production orders</li></ul>
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
