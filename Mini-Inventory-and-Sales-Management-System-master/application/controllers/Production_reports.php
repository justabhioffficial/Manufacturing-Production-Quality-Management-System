<?php
defined('BASEPATH') or exit('');

/**
 * Description of Production_reports
 *
 * Generates daily production reports with date range filtering
 */
class Production_reports extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();
    $this->genlib->superOnly();

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
    $data['pageContent'] = $this->load->view('reports/production_report', '', TRUE);
    $data['pageTitle'] = "Daily Production Report";

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
   * Generate report for a given date range
   */
  public function generate()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;

    $fromDate = $this->input->get('from_date', TRUE) ? $this->input->get('from_date', TRUE) : date('Y-m-d');
    $toDate = $this->input->get('to_date', TRUE) ? $this->input->get('to_date', TRUE) : date('Y-m-d');

    //get daily production stats from the production_order model
    $reportData['dailyStats'] = $this->production_order->getDailyStats($fromDate, $toDate);

    //get inspection stats from the quality model
    $reportData['inspectionStats'] = $this->quality->getInspectionStats($fromDate, $toDate);

    //get machine utilization data
    $reportData['machineStats'] = $this->machine->getUtilization($fromDate, $toDate);

    $reportData['fromDate'] = $fromDate;
    $reportData['toDate'] = $toDate;

    //render report HTML
    $json['status'] = 1;
    $json['reportHtml'] = $this->load->view('reports/production_report_table', $reportData, TRUE);

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
