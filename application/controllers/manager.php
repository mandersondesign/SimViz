<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Manager extends CI_Controller
{

  function __construct()
  {
    parent::__construct();

    $this->load->model('simulation_model', 'sims', TRUE);
    $this->load->model('testbench_model');
    $this->load->model('plot_model', 'plots', TRUE);

    $this->data['moduleID'] = 0;
    $this->data['moduleURL'] = site_url() . '/manager';
    $this->data['breadcrumbs'] = '<li><a href="' . site_url() . '/dashboard">Dashboard</a></li>';
    //$this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->counter = 1;
  }

  public function index()
  {
    $this->data['pageID'] = 0;
    $this->data['title'] = 'Design Manager';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['testbenches'] = $this->testbench_model->getTestBenches();


    $options = array(
        'data' => $this->data,
        'view' => 'manager/home'
    );

    $this->presentation->template($options);
  }

  public function add_plot($simID = 0)
  {
    $this->data['pageID'] = 0;
    $this->data['title'] = 'Add Plot';
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Add Plot</a></li>';
    $this->data['simID'] = $simID;
    $options = array(
        'data' => $this->data,
        'view' => 'manager/add_plot'
    );

    $this->presentation->template($options);
  }
  
  public function testbench($tbID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class = "current"><a href="' . $this->data['moduleURL'] . '/" title="">Test Bench</a></li>';
    $this->data['pageID'] = 1;
    $this->data['title'] = 'Testbench';
    $this->data['tbID'] = $tbID;
    $this->data['configurations'] = $this->testbench_model->getConfigurations();

    $options = array(
        'data' => $this->data,
        'view' => 'manager/testbench'
    );

    $this->presentation->template($options);
  }

  public function configs($tbID = 0, $confID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Configuration: ' . $confID . '</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Configuration';
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['simulations'] = $this->testbench_model->getSimulationByTestBenchIDAndConfigID($tbID, $confID);

    $options = array(
        'data' => $this->data,
        'view' => 'manager/configuration'
    );

    $this->presentation->template($options);
  }

  public function sim($simID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Simulation: ' . $simID . '</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Simulation';
    $this->data['simID'] = $simID;
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['plots'] = $this->plots->getPlotsBySimulationID($simID);

    $options = array(
        'data' => $this->data,
        'view' => 'manager/sim'
    );

    $this->presentation->template($options);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
