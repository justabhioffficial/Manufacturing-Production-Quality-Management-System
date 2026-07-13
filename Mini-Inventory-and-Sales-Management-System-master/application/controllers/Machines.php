<?php
defined('BASEPATH') or exit('');

/**
 * Description of Machines
 *
 * Manages manufacturing machines and their statuses
 */
class Machines extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();
    $this->genlib->superOnly();

    $this->load->model(['machine']);
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
    $data['pageContent'] = $this->load->view('machines/machines', '', TRUE);
    $data['pageTitle'] = "Machine Status";

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
   * "lilt" = "load Items List Table"
   */
  public function lilt()
  {
    $this->genlib->ajaxOnly();

    $this->load->helper('text');

    //set the sort order
    $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "name";
    $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";

    //count the total number of machines in db
    $totalMachines = $this->db->count_all('machines');

    $this->load->library('pagination');

    $pageNumber = $this->uri->segment(3, 0); //set page number to zero if the page number is not set in the third segment of uri

    $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10; //show $limit per page
    $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit; //start from 0 if pageNumber is 0, else start from the next iteration

    //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
    $config = $this->genlib->setPaginationConfig($totalMachines, "machines/lilt", $limit, ['onclick' => 'return lilt(this.href);']);

    $this->pagination->initialize($config); //initialize the library class

    //get all machines from db
    $data['allMachines'] = $this->machine->getAll($orderBy, $orderFormat, $start, $limit);
    $data['range'] = $totalMachines > 0 ? "Showing " . ($start + 1) . "-" . ($start + count($data['allMachines'])) . " of " . $totalMachines : "";
    $data['links'] = $this->pagination->create_links(); //page links
    $data['sn'] = $start + 1;

    $json['machinesListTable'] = $this->load->view('machines/machineslisttable', $data, TRUE); //get view with populated machines table

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
      'machineName',
      'Machine name',
      ['required', 'trim', 'max_length[80]'],
      ['required' => "required"]
    );
    $this->form_validation->set_rules(
      'machineCode',
      'Machine Code',
      ['required', 'trim', 'max_length[20]', 'is_unique[machines.code]'],
      ['required' => "required", 'is_unique' => "There is already a machine with this code"]
    );
    $this->form_validation->set_rules('machineType', 'Machine Type', ['trim']);
    $this->form_validation->set_rules('machineLocation', 'Location', ['trim']);
    $this->form_validation->set_rules('machineNotes', 'Notes', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $this->db->trans_start(); //start transaction

      /**
       * insert info into db
       */
      $insertedId = $this->machine->add(
        set_value('machineName'),
        set_value('machineCode'),
        set_value('machineType'),
        set_value('machineLocation'),
        set_value('machineNotes')
      );

      $machineName = set_value('machineName');

      //insert into eventlog
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Addition of new machine '{$machineName}' to the system";

      $insertedId ? $this->genmod->addevent("Creation of new machine", $insertedId, $desc, "machines", $this->session->admin_id) : "";

      $this->db->trans_complete();

      $json = $this->db->trans_status() !== FALSE ?
        ['status' => 1, 'msg' => "Machine successfully added"]
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

    $this->form_validation->set_rules('machineId', 'Machine ID', ['required', 'trim', 'numeric']);
    $this->form_validation->set_rules('machineName', 'Machine Name', ['required', 'trim', 'max_length[80]'], ['required' => 'required']);
    $this->form_validation->set_rules('machineType', 'Machine Type', ['trim']);
    $this->form_validation->set_rules('machineLocation', 'Location', ['trim']);
    $this->form_validation->set_rules('machineNotes', 'Notes', ['trim']);

    if ($this->form_validation->run() !== FALSE) {
      $machineId = set_value('machineId');
      $machineName = set_value('machineName');
      $machineType = set_value('machineType');
      $machineLocation = set_value('machineLocation');
      $machineNotes = set_value('machineNotes');

      //update machine in db
      $updated = $this->machine->edit($machineId, $machineName, $machineType, $machineLocation, $machineNotes);

      $json['status'] = $updated ? 1 : 0;

      //add event to log
      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $desc = "Details of machine '{$machineName}' was updated";

      $this->genmod->addevent("Machine Update", $machineId, $desc, 'machines', $this->session->admin_id);
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

    $machineId = $this->input->post('machineId', TRUE);
    $newStatus = $this->input->post('newStatus', TRUE);

    if ($machineId && $newStatus) {
      $updated = $this->machine->updateStatus($machineId, $newStatus);

      if ($updated) {
        $json['status'] = 1;
        $json['msg'] = "Machine status updated successfully";

        //add event to log
        $desc = "Machine ID {$machineId} status changed to '{$newStatus}'";
        $this->genmod->addevent("Machine Status Update", $machineId, $desc, 'machines', $this->session->admin_id);
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

  public function delete()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;
    $machine_id = $this->input->post('i', TRUE);

    if ($machine_id) {
      $this->db->where('id', $machine_id)->delete('machines');

      $json['status'] = 1;
    }

    //set final output
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
