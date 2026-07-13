<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Quality
 *
 * Quality Inspection model for Manufacturing Production & Quality Management
 */
class Quality extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Get all quality inspections with pagination
     * @param type $orderBy
     * @param type $orderFormat
     * @param type $start
     * @param type $limit
     * @return boolean
     */
    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        
        $run_q = $this->db->get('quality_inspections');
        
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
     * Add a new quality inspection
     * @param type $order_id
     * @param type $inspection_date
     * @param type $qty_inspected
     * @param type $qty_passed
     * @param type $qty_failed
     * @param type $status
     * @param type $inspector_id
     * @param type $remarks
     * @return boolean
     */
    public function add($order_id, $inspection_date, $qty_inspected, $qty_passed, $qty_failed, $status, $inspector_id, $remarks){
        $data = ['order_id'=>$order_id, 'inspection_date'=>$inspection_date, 
                 'quantity_inspected'=>$qty_inspected, 'quantity_passed'=>$qty_passed, 
                 'quantity_failed'=>$qty_failed, 'status'=>$status, 
                 'inspector_id'=>$inspector_id, 'remarks'=>$remarks];
                
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('dateAdded', "datetime('now')", FALSE) 
                : 
        $this->db->set('dateAdded', "NOW()", FALSE);
        
        $this->db->insert('quality_inspections', $data);
        
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
     * Get total number of inspections
     * @return boolean
     */
    public function totalInspections(){
        $q = "SELECT COUNT(*) as 'totalInspections' FROM quality_inspections";
        
        $run_q = $this->db->query($q);
        
        if($run_q->num_rows() > 0){
            foreach($run_q->result() as $get){
                return $get->totalInspections;
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
     * Get inspection stats (total inspected, passed, failed)
     * @return boolean
     */
    public function getInspectionStats(){
        $q = "SELECT SUM(quantity_inspected) as 'totalInspected', 
                     SUM(quantity_passed) as 'totalPassed', 
                     SUM(quantity_failed) as 'totalFailed' 
              FROM quality_inspections";
        
        $run_q = $this->db->query($q);
        
        if($run_q->num_rows() > 0){
            return $run_q->row();
        }
        
        else{
            return FALSE;
        }
    }
}
