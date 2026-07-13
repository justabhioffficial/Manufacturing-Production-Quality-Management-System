<?php
defined('BASEPATH') or exit('');

/**
 * Description of Quality_inspections
 *
 * Manages quality inspection records for production orders
 */
class Quality_inspections extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();
    $this->genlib->superOnly();

    $this->load->model(['quality', 'production_order']);
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
    $inspectionData['productionOrders'] = $this->production_order->getAll('created_at', 'DESC', 0, 1000); //get production orders for dropdown

    $data['pageContent'] = $this->load->view('quality/quality', $inspectionData, TRUE);
    $data['pageTitle'] = "Quality Inspection";

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
   * "lilt" = "load list table"
   */
  public function lilt()
  {
    $this->genlib->ajaxOnly();

    $this->load->helper('text');

    //set the sort order
    $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "inspection_date";
    $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "DESC";

    //count the total number of inspections in db
    $totalInspections = $this->db->count_all('quality_inspections');

    $this->load->library('pagination');

    $pageNumber = $this->uri->segment(3, 0); //set page number to zero if the page number is not set in the third segment of uri

    $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10; //show $limit per page
    $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit; //start from 0 if pageNumber is 0, else start from the next iteration

    //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
    $config = $this->genlib->setPaginationConfig($totalInspections, "quality_inspections/lilt", $limit, ['onclick' => 'return lilt(this.href);']);

    $this->pagination->initialize($config); //initialize the library class

    //get all inspections from db
    $data['allInspections'] = $this->quality->getAll($orderBy, $orderFormat, $start, $limit);
    $data['range'] = $totalInspections > 0 ? "Showing " . ($start + 1) . "-" . ($start + count($data['allInspections'])) . " of " . $totalInspections : "";
    $data['links'] = $this->pagination->create_links(); //page links
    $data['sn'] = $start + 1;

    $json['inspectionsListTable'] = $this->load->view('quality/inspectionslisttable', $data, TRUE); //get view with populated inspections table

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function add()
  {
    $this->genlib->ajaxOnly();

    $this->load->library('form_validation');

    $this->form_validation->set_error_delimiters('', '');

    $this->form_validation->set_rules('orderId', 'Production Order', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('inspectionDate', 'Inspection Date', ['trim']);
    $this->form_validation->set_rules('quantityInspected', 'Quantity Inspected', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('quantityPassed', 'Quantity Passed', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('quantityFailed', 'Quantity Failed', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules(
      'inspectionStatus',
      'Inspection Status',
      ['required', 'trim', 'in_list[Pass,Fail,Partial]'],
      ['required' => "required"]
    );
    $this->form_validation->set_rules('remarks', 'Remarks', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $this->db->trans_start(); //start transaction

      /**
       * insert info into db
       */
      $insertedId = $this->quality->add(
        set_value('orderId'),
        set_value('inspectionDate') ? set_value('inspectionDate') : date('Y-m-d'),
        set_value('quantityInspected'),
        set_value('quantityPassed'),
        set_value('quantityFailed'),
        set_value('inspectionStatus'),
        set_value('remarks')
      );

      $qtyInspected = set_value('quantityInspected');
      $qtyPassed = set_value('quantityPassed');
      $qtyFailed = set_value('quantityFailed');
      $status = set_value('inspectionStatus');

      //insert into eventlog
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Quality inspection recorded: {$qtyInspected} inspected, {$qtyPassed} passed, {$qtyFailed} failed. Status: {$status}";

      $insertedId ? $this->genmod->addevent("Quality Inspection", $insertedId, $desc, "quality_inspections", $this->session->admin_id) : "";

      $this->db->trans_complete();

      $json = $this->db->trans_status() !== FALSE ?
        ['status' => 1, 'msg' => "Quality inspection record added successfully"]
        :
        ['status' => 0, 'msg' => "Oops! Unexpected server error! Please contact administrator for help. Sorry for the embarrassment"];
    } else {
      //return all error messages
      $json = $this->form_validation->error_array(); //get an array of all errors

      $json['msg'] = "One or more required fields are empty or not correctly filled";
      $json['status'] = 0;
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
