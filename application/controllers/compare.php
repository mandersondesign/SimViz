<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Compare extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->data['moduleID'] = 2;
    $this->load->model('simulation_model', 'sims', TRUE);
    $this->load->model('testbench_model', 'tb', TRUE);
    $this->load->model('plot_model', 'plots', TRUE);

    $this->data['moduleURL'] = base_url() . 'index.php/compare';
    $this->data['breadcrumbs'] = '<li><a href="' . site_url() . '/dashboard">Dashboard</a></li>';

    $this->counter = 1;
  }

  public function index()
  {
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Compare Design</a></li>';
    $this->data['pageID'] = 0;
    $this->data['title'] = 'Compare Design';
    
    $this->data['testbenches'] = $this->tb->getTestBenches();
    $this->data['configurations'] = $this->sims->getConfigurations();
    
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

  public function configs($tbID = 0, $confID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Compare Design</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Configuration: ' . $confID . '</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Configuration';
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['simulations'] = $this->testbench_model->getSimulationByTestBenchIDAndConfigID($tbID, $confID);

    $options = array(
        'data' => $this->data,
        'view' => 'compare/configuration'
    );

    $this->presentation->template($options);
  }

  public function sim($simID = 0)
  {
    $this->data['breadcrumbs'] .= '<li><a href="' . $this->data['moduleURL'] . '/" title="">Design Manager</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Simulation: ' . $simID . '</a></li>';

    $this->data['pageID'] = 1;
    $this->data['title'] = 'Simulation';
    //print_r($this->testbench_model->getSimulationByTestBenchIDAndConfigID( $tbID, $confID ));
    $this->data['plots'] = $this->plots->getPlotsBySimulationID($simID);

    $options = array(
        'data' => $this->data,
        'view' => 'compare/sim'
    );

    $this->presentation->template($options);
  }
  
  public function redirectToNextStep()
  {
    $tbID = $this->input->post('select_tb');
    $config1 = $this->input->post('select_config');
    $config2 = $this->input->post('select_config2');
    
    $sims1 = $this->tb->getSimulationByTestBenchIDAndConfigID( $tbID, $config1 );
    $sims2 = $this->tb->getSimulationByTestBenchIDAndConfigID( $tbID, $config2 );
    
    $this->data['sims1'] = $sims1;
    $this->data['sims2'] = $sims2;
        
    $options = array(
        'data' => $this->data,
        'view' => 'compare/pick_sims'
    );

    $this->presentation->template($options);
  }
  
  public function choosePlots()
  {
    $sim1 = $this->input->post('select_sim1');
    $sim2 = $this->input->post('select_sim2');
    
    //echo $sim1.'--'.$sim2;
    
    $plots1 = $this->plots->getPlotsBySimulationID($sim1);
    $plots2 = $this->plots->getPlotsBySimulationID($sim2);
    
    $this->data['plots1'] = $plots1;
    $this->data['plots2'] = $plots2;
        
    $options = array(
        'data' => $this->data,
        'view' => 'compare/pick_plots'
    );

    $this->presentation->template($options);
  }
  
  public function redirectToCompare()
  {
    $plot1ID = $this->input->post('select_plot1');
    $plot2ID = $this->input->post('select_plot2');
    
    //echo $plot1ID.'--'.$plot2ID;
    
    redirect('simviz/plot/'.$plot1ID.'/'.$plot2ID.'/');
    
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
