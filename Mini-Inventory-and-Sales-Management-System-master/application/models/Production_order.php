<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Production_order
 *
 * Production Order model for Manufacturing Production & Quality Management
 */
class Production_order extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Get all production orders with pagination
     * @param type $orderBy
     * @param type $orderFormat
     * @param type $start
     * @param type $limit
     * @return boolean
     */
    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        
        $run_q = $this->db->get('production_orders');
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Add a new production order
     * @param type $order_code
     * @param type $item_code
     * @param type $item_name
     * @param type $qty
     * @param type $start_date
     * @param type $end_date
     * @param type $priority
     * @param type $notes
     * @param type $created_by
     * @return boolean
     */
    public function add($order_code, $item_code, $item_name, $qty, $start_date, $end_date, $priority, $notes, $created_by){
        $data = ['order_code'=>$order_code, 'item_code'=>$item_code, 'item_name'=>$item_name, 
                 'quantity_ordered'=>$qty, 'start_date'=>$start_date, 'end_date'=>$end_date, 
                 'priority'=>$priority, 'notes'=>$notes, 'created_by'=>$created_by];
                
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('dateAdded', "datetime('now')", FALSE) 
                : 
        $this->db->set('dateAdded', "NOW()", FALSE);
        
        $this->db->insert('production_orders', $data);
        
        if($this->db->insert_id()){
            return $this->db->insert_id();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Edit a production order
     * @param type $id
     * @param type $item_code
     * @param type $item_name
     * @param type $qty
     * @param type $start_date
     * @param type $end_date
     * @param type $priority
     * @param type $notes
     */
    public function edit($id, $item_code, $item_name, $qty, $start_date, $end_date, $priority, $notes){
        $data = ['item_code'=>$item_code, 'item_name'=>$item_name, 'quantity_ordered'=>$qty, 
                 'start_date'=>$start_date, 'end_date'=>$end_date, 'priority'=>$priority, 'notes'=>$notes];
        
        $this->db->where('id', $id);
        $this->db->update('production_orders', $data);
        
        return TRUE;
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Update production order status
     * @param type $id
     * @param type $status
     * @return boolean
     */
    public function updateStatus($id, $status){
        $data = ['status'=>$status];
        
        $this->db->where('id', $id);
        $this->db->update('production_orders', $data);
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Log production and increment quantity_produced on the order
     * @param type $order_id
     * @param type $machine_id
     * @param type $shift
     * @param type $qty
     * @param type $operator_id
     * @param type $production_date
     * @param type $remarks
     * @return boolean
     */
    public function logProduction($order_id, $machine_id, $shift, $qty, $operator_id, $production_date, $remarks){
        $data = ['order_id'=>$order_id, 'machine_id'=>$machine_id, 'shift'=>$shift, 
                 'quantity_produced'=>$qty, 'operator_id'=>$operator_id, 
                 'production_date'=>$production_date, 'remarks'=>$remarks];
        
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('dateAdded', "datetime('now')", FALSE) 
                : 
        $this->db->set('dateAdded', "NOW()", FALSE);
        
        $this->db->insert('production_logs', $data);
        
        if($this->db->insert_id()){
            //increment quantity_produced on the production order
            $q = "UPDATE production_orders SET quantity_produced = quantity_produced + ? WHERE id = ?";
            
            $this->db->query($q, [$qty, $order_id]);
            
            return $this->db->insert_id();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Get total number of production orders
     * @return boolean
     */
    public function totalOrders(){
        $q = "SELECT COUNT(*) as 'totalOrders' FROM production_orders";
        
        $run_q = $this->db->query($q);
        
        if($run_q->num_rows() > 0){
            foreach($run_q->result() as $get){
                return $get->totalOrders;
            }
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Delete a production order
     * @param type $id
     * @return boolean
     */
    public function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('production_orders');
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Get production logs for a specific order
     * @param type $order_id
     * @return boolean
     */
    public function getProductionLogs($order_id){
        $q = "SELECT * FROM production_logs WHERE order_id = ?";
        
        $run_q = $this->db->query($q, [$order_id]);
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Get shift-wise aggregated stats for a date
     * @param type $date
     * @return boolean
     */
    public function getShiftStats($date){
        $q = "SELECT shift, SUM(quantity_produced) as 'totalProduced', COUNT(*) as 'logCount' 
              FROM production_logs 
              WHERE production_date = ? 
              GROUP BY shift";
        
        $run_q = $this->db->query($q, [$date]);
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Get day-by-day production totals between dates
     * @param type $from
     * @param type $to
     * @return boolean
     */
    public function getDailyStats($from, $to){
        $q = "SELECT production_date, SUM(quantity_produced) as 'totalProduced', COUNT(*) as 'logCount' 
              FROM production_logs 
              WHERE production_date >= ? AND production_date <= ? 
              GROUP BY production_date 
              ORDER BY production_date ASC";
        
        $run_q = $this->db->query($q, [$from, $to]);
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
}
