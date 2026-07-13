<?php
defined('BASEPATH') or exit('');

/**
 * Description of Production_dashboard
 *
 * Shift-wise production dashboard with charts and summary statistics
 */
class Production_dashboard extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();

    $this->load->model(['production_order', 'quality', 'machine']);
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * 
   */
  public function index()
  {
    //get summary data for the dashboard
    $dashData['totalOrders'] = $this->db->count_all('production_orders');
    $dashData['ordersInProgress'] = $this->production_order->countByStatus('In Progress');
    $dashData['machinesRunning'] = $this->machine->countByStatus('Running');
    $dashData['todaysProduction'] = $this->production_order->getTodaysProduction();
    $dashData['inspectionPassRate'] = $this->quality->getPassRate();

    $data['pageContent'] = $this->load->view('dashboard/production_dashboard', $dashData, TRUE);
    $data['pageTitle'] = "Shift-wise Production Dashboard";

    $this->load->view('main', $data);
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * Get shift-wise production stats for Chart.js
   */
  public function get_shift_stats()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;

    $date = $this->input->get('date', TRUE) ? $this->input->get('date', TRUE) : date('Y-m-d');

    $shiftStats = $this->production_order->getShiftStats($date);

    if ($shiftStats) {
      $json['status'] = 1;
      $json['date'] = $date;
      $json['labels'] = ['Morning', 'Afternoon', 'Night'];
      $json['quantities'] = [
        isset($shiftStats['Morning']) ? (int)$shiftStats['Morning'] : 0,
        isset($shiftStats['Afternoon']) ? (int)$shiftStats['Afternoon'] : 0,
        isset($shiftStats['Night']) ? (int)$shiftStats['Night'] : 0
      ];
    } else {
      $json['status'] = 1;
      $json['date'] = $date;
      $json['labels'] = ['Morning', 'Afternoon', 'Night'];
      $json['quantities'] = [0, 0, 0];
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * Get machine status counts for dashboard chart
   */
  public function get_machine_stats()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;

    $machineStats = $this->machine->getStatusCounts();

    if ($machineStats) {
      $json['status'] = 1;
      $json['labels'] = [];
      $json['counts'] = [];

      foreach ($machineStats as $stat) {
        $json['labels'][] = $stat->status;
        $json['counts'][] = (int)$stat->count;
      }
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * Get quality inspection stats for pie chart
   */
  public function get_quality_stats()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;

    $qualityStats = $this->quality->getInspectionSummary();

    if ($qualityStats) {
      $json['status'] = 1;
      $json['labels'] = [];
      $json['counts'] = [];

      foreach ($qualityStats as $stat) {
        $json['labels'][] = $stat->status;
        $json['counts'][] = (int)$stat->count;
      }
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
