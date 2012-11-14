<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Graph extends CI_Controller {

  function __construct()
  {
      parent::__construct();

      $this->load->model('testbench_model', 'testbench', TRUE);
      $this->load->model('simulation_model', 'sims', TRUE);
      $this->load->model('plot_model', 'plots', TRUE);
      $this->data['moduleID'] = 2;
      $this->data['moduleURL'] = base_url() . 'graph';
      $this->data['breadcrumbs'] = '<li><a href="' . base_url() . 'dashboard">Dashboard</a></li>';
      $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Graph</a></li>';
  }
	public function index($testBenchID=1, $plotID=0, $datafile="")
	{
    $this->data['pageID'] = 0;
    $this->data['title'] = 'Graph Home';
    //echo "datafile: ".$datafile;
    $this->data['datafile'] = $datafile;
    $this->data['testBenchID'] = $testBenchID;
    
    $simulation = $this->sims->getSimulationByTestBenchID($testBenchID);
    
    if (empty($simulation))
    {
      $revData = array();
      $revData['simTestBenchID'] = $testBenchID; 
      $simID = $this->sims->createSimulation($revData);
      $simulation = $this->sims->getSimulationByID($simID);
    }
    
    $this->data['plots'] = $this->plots->getPlotsBySimulationID($simulation->simID);
    
    if($plotID > 0)
    {
      $this->data['plot'] = $this->plots->getPlotByID($plotID);
    }
    
    $this->data['simID'] = $simulation->simID;
    $this->data['simulation'] = $simulation;
    
    $options = array(
        'data' => $this->data,
        'view' => 'graph'
    );

    $this->presentation->template($options);
	}
  
  public function plot($testBenchID=1, $plotID=0, $datafile="")
	{
    $this->data['title'] = 'TestBench '.$testBenchID.' Design Review';
    $this->data['pageID'] = 1;
    $this->data['datafile'] = $datafile;
    $this->data['testBenchID'] = $testBenchID;
    
    $this->data['jsonTreeFilePathTest'] = base_url()."include/data/treeTemplate.json";
    $this->data['jsonDecodedTest'] = $this->getJSONFile($this->data['jsonTreeFilePathTest']);
    $this->data['jsonPlainTest'] = $this->getJSONFilePlain($this->data['jsonTreeFilePathTest']);
    
    $this->data['jsonTreeFilePath'] = base_url()."include/data/tempTree.json";
    $this->data['jsonDecoded'] = $this->getJSONFile($this->data['jsonTreeFilePath']);
    
    $simulation = $this->sims->getSimulationByTestBenchID($testBenchID);
    
    if (empty($simulation))
    {
      $revData = array();
      $revData['simTestBenchID'] = $testBenchID; 
      $simID = $this->sims->createSimulation($revData);
      $simulation = $this->sims->getSimulationByID($simID);
    }
    
    $this->data['plots'] = $this->plots->getPlotsBySimulationID($simulation->simID);
    
    if($plotID > 0)
    {
      $plot = $this->plots->getPlotByID($plotID);
      $this->data['plot'] = $plot;
    }
    
    $this->data['simID'] = $simulation->simID;
    $this->data['simulation'] = $simulation;
    
    $options = array(
        'data' => $this->data,
        'view' => 'graph'
    );

    $this->presentation->template($options);
	}
  
  
  public function getJSONFile($jsonFilePath)
  {
    
    $jsonStream = file_get_contents($jsonFilePath);
    
    //Retrieve json file
    if (json_decode($jsonStream) == NULL) {
      die("No valid JSON file found for this component");
    }
    else {
      $jsonDecoded = json_decode($jsonStream, true);
      return $jsonDecoded;
    }
  }
  
  public function getJSONFilePlain($jsonFilePath)
  {
    
    $jsonStream = file_get_contents($jsonFilePath);
    
    
    
    //Retrieve json file
    if (json_decode($jsonStream) == NULL) {
      return "No valid JSON file found for this component";
    }
    else {
      return $jsonStream;
    }
  }
  
  public function getJSONFileAjax($jsonFilePath)
  {
    $jsonDirectory = base_url()."include/";
    $jsonFile = $jsonDirectory . $componentName . "_data.json";
    $jsonStream = file_get_contents($jsonFile);
    
    //Retrieve json file
    if (json_decode($jsonStream) == NULL) {
      echo "0|";
      echo "No valid JSON file found for this component";
    }
    else {
      echo "1|";
      echo  $jsonStream;
    }
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */