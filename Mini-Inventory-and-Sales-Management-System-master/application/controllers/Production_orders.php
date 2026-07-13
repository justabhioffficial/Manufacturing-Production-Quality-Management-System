<?php
defined('BASEPATH') or exit('');

/**
 * Description of Production_orders
 *
 * Manages production orders, status updates, and production logging
 */
class Production_orders extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();
    $this->genlib->superOnly();

    $this->load->model(['production_order', 'item', 'machine']);
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
    $orderData['items'] = $this->item->getActiveItems('name', 'ASC'); //get items for dropdown

    $data['pageContent'] = $this->load->view('production_orders/production_orders', $orderData, TRUE);
    $data['pageTitle'] = "Production Orders";

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
    $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "created_at";
    $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "DESC";

    //count the total number of production orders in db
    $totalOrders = $this->db->count_all('production_orders');

    $this->load->library('pagination');

    $pageNumber = $this->uri->segment(3, 0); //set page number to zero if the page number is not set in the third segment of uri

    $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10; //show $limit per page
    $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit; //start from 0 if pageNumber is 0, else start from the next iteration

    //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
    $config = $this->genlib->setPaginationConfig($totalOrders, "production_orders/lilt", $limit, ['onclick' => 'return lilt(this.href);']);

    $this->pagination->initialize($config); //initialize the library class

    //get all production orders from db
    $data['allOrders'] = $this->production_order->getAll($orderBy, $orderFormat, $start, $limit);
    $data['range'] = $totalOrders > 0 ? "Showing " . ($start + 1) . "-" . ($start + count($data['allOrders'])) . " of " . $totalOrders : "";
    $data['links'] = $this->pagination->create_links(); //page links
    $data['sn'] = $start + 1;

    $json['ordersListTable'] = $this->load->view('production_orders/orderslisttable', $data, TRUE); //get view with populated orders table

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

    $this->form_validation->set_rules(
      'orderCode',
      'Order Code',
      ['required', 'trim', 'max_length[20]', 'is_unique[production_orders.order_code]'],
      ['required' => "required", 'is_unique' => "There is already a production order with this code"]
    );
    $this->form_validation->set_rules('itemCode', 'Item', ['required', 'trim'], ['required' => "required"]);
    $this->form_validation->set_rules('quantityOrdered', 'Quantity Ordered', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('startDate', 'Start Date', ['trim']);
    $this->form_validation->set_rules('endDate', 'End Date', ['trim']);
    $this->form_validation->set_rules('priority', 'Priority', ['trim', 'in_list[Low,Medium,High,Urgent]']);
    $this->form_validation->set_rules('orderNotes', 'Notes', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $this->db->trans_start(); //start transaction

      /**
       * insert info into db
       */
      $insertedId = $this->production_order->add(
        set_value('orderCode'),
        set_value('itemCode'),
        set_value('quantityOrdered'),
        set_value('startDate'),
        set_value('endDate'),
        set_value('priority'),
        set_value('orderNotes')
      );

      $orderCode = set_value('orderCode');
      $qty = set_value('quantityOrdered');

      //insert into eventlog
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Creation of new production order '{$orderCode}' for {$qty} units";

      $insertedId ? $this->genmod->addevent("Creation of new production order", $insertedId, $desc, "production_orders", $this->session->admin_id) : "";

      $this->db->trans_complete();

      $json = $this->db->trans_status() !== FALSE ?
        ['status' => 1, 'msg' => "Production order successfully added"]
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

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function edit()
  {
    $this->genlib->ajaxOnly();

    $this->load->library('form_validation');

    $this->form_validation->set_error_delimiters('', '');

    $this->form_validation->set_rules('orderId', 'Order ID', ['required', 'trim', 'numeric']);
    $this->form_validation->set_rules('orderCode', 'Order Code', ['required', 'trim'], ['required' => 'required']);
    $this->form_validation->set_rules('itemCode', 'Item', ['required', 'trim'], ['required' => 'required']);
    $this->form_validation->set_rules('quantityOrdered', 'Quantity Ordered', ['required', 'trim', 'numeric'], ['required' => 'required']);
    $this->form_validation->set_rules('startDate', 'Start Date', ['trim']);
    $this->form_validation->set_rules('endDate', 'End Date', ['trim']);
    $this->form_validation->set_rules('priority', 'Priority', ['trim', 'in_list[Low,Medium,High,Urgent]']);
    $this->form_validation->set_rules('orderNotes', 'Notes', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $orderId = set_value('orderId');
      $orderCode = set_value('orderCode');
      $itemCode = set_value('itemCode');
      $quantityOrdered = set_value('quantityOrdered');
      $startDate = set_value('startDate');
      $endDate = set_value('endDate');
      $priority = set_value('priority');
      $orderNotes = set_value('orderNotes');

      //update production order in db
      $updated = $this->production_order->edit($orderId, $orderCode, $itemCode, $quantityOrdered, $startDate, $endDate, $priority, $orderNotes);

      $json['status'] = $updated ? 1 : 0;

      //add event to log
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Details of production order '{$orderCode}' was updated";

      $this->genmod->addevent("Production Order Update", $orderId, $desc, 'production_orders', $this->session->admin_id);
    } else {
      $json['status'] = 0;
      $json = $this->form_validation->error_array();
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

  public function update_status()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;

    $orderId = $this->input->post('orderId', TRUE);
    $newStatus = $this->input->post('newStatus', TRUE);

    if ($orderId && $newStatus && in_array($newStatus, ['Pending', 'In Progress', 'Completed', 'Cancelled'])) {
      $updated = $this->production_order->updateStatus($orderId, $newStatus);

      if ($updated) {
        $json['status'] = 1;
        $json['msg'] = "Order status updated to '{$newStatus}'";

        //add event to log
        $desc = "Production order ID {$orderId} status changed to '{$newStatus}'";
        $this->genmod->addevent("Production Order Status Update", $orderId, $desc, 'production_orders', $this->session->admin_id);
      }
    } else {
      $json['msg'] = "Invalid status value";
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

  public function log_production()
  {
    $this->genlib->ajaxOnly();

    $this->load->library('form_validation');

    $this->form_validation->set_error_delimiters('', '');

    $this->form_validation->set_rules('orderId', 'Order ID', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('machineId', 'Machine', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('shift', 'Shift', ['required', 'trim', 'in_list[Morning,Afternoon,Night]'], ['required' => "required"]);
    $this->form_validation->set_rules('quantity', 'Quantity', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('remarks', 'Remarks', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $this->db->trans_start(); //start transaction

      $insertedId = $this->production_order->logProduction(
        set_value('orderId'),
        set_value('machineId'),
        set_value('shift'),
        set_value('quantity'),
        set_value('remarks')
      );

      $orderId = set_value('orderId');
      $qty = set_value('quantity');
      $shift = set_value('shift');

      //insert into eventlog
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Logged {$qty} units produced for order ID {$orderId} during {$shift} shift";

      $insertedId ? $this->genmod->addevent("Production Log Entry", $insertedId, $desc, "production_logs", $this->session->admin_id) : "";

      $this->db->trans_complete();

      $json = $this->db->trans_status() !== FALSE ?
        ['status' => 1, 'msg' => "Production log entry added successfully"]
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

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function delete()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;
    $order_id = $this->input->post('i', TRUE);

    if ($order_id) {
      $this->db->where('id', $order_id)->delete('production_orders');

      $json['status'] = 1;
    }

    //set final output
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
