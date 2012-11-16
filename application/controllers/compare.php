<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Compare extends CI_Controller {

  function __construct()
  {
      parent::__construct();
      $this->data['moduleID'] = 2;
      $this->load->model('simulation_model', 'sims', TRUE);
      $this->load->model('testbench_model');
      $this->load->model('plot_model', 'plots', TRUE);

      $this->data['moduleURL'] = site_url() . '/compare';
      $this->data['breadcrumbs'] = '<li><a href="' . site_url() . '/dashboard">Dashboard</a></li>';
      
      $this->counter = 1;
  }

    public function index()
    {
        $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Compare Design</a></li>';
        $this->data['pageID'] = 0;
        $this->data['title'] = 'Compare Design';
        $this->data['testbenches'] = $this->testbench_model->getTestBenches();
        $options = array(
            'data' => $this->data,
            'view' => 'compare/home'
        );

        $this->presentation->template($options);
    }
    public function testbench($tbID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Compare Design</a></li>';
    $this->data['breadcrumbs'] .= '<li class = "current"><a href="' . $this->data['moduleURL'] . '/" title="">Test Bench</a></li>';
    $this->data['pageID'] = 1;
    $this->data['title'] = 'Testbench';
    $this->data['tbID'] = $tbID;
    $this->data['configurations'] = $this->testbench_model->getConfigurations();

    $options = array(
        'data' => $this->data,
        'view' => 'compare/testbench'
    );

    $this->presentation->template($options);
  }
  public function configs($tbID=0, $confID = 0)
  {
      $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Compare Design</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Configuration: '.$confID.'</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Configuration';
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['simulations'] = $this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID );

    $options = array(
        'data' => $this->data,
        'view' => 'compare/configuration'
    );

    $this->presentation->template($options);
  }

  public function sim($simID=0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Simulation: '.$simID.'</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Simulation';
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['plots'] = $this->plots->getPlotsBySimulationID( $simID );

    $options = array(
        'data' => $this->data,
        'view' => 'compare/sim'
    );

    $this->presentation->template($options);
  }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */