<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Graph extends CI_Controller {

  function __construct()
  {
      parent::__construct();

      $this->load->model('simulation_model', 'sims', TRUE);
      $this->load->model('plot_model', 'plots', TRUE);
      
      $this->data['moduleID'] = 2;
      $this->data['moduleURL'] = base_url() . 'graph';
      $this->data['breadcrumbs'] = '<li><a href="' . base_url() . 'index.php/dashboard">Dashboard</a></li>';
      $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Graph</a></li>';
      
      
      $this->counter = 1;
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
        //'view' => 'graph'
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
    
    $this->data['jsonTreeFilePathTest'] = base_url()."include/data/testbenches/DriveTrain/cfg1/tree.json";
    $this->data['jsonDecodedTest'] = $this->getJSONFile($this->data['jsonTreeFilePathTest']);
    $this->data['jsonPlainTest'] = $this->getJSONFilePlain($this->data['jsonTreeFilePathTest']);
    
    $this->data['jsonTreeFilePath'] = base_url()."include/data/tempTree.json";
    $this->data['jsonDecoded'] = $this->getJSONFile($this->data['jsonTreeFilePath']);
    
    $simulation = $this->sims->getSimulationByTestBenchID($testBenchID);
    
    //Create New Json Tree
    $treeJson = $this->getJSONFilePlain($this->data['jsonTreeFilePathTest']);
    
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
        'view' => 'manager/home'
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
  
  
  public function buildTreeJSON($jsonDecoded)
  {
    
  }
  
  public function getSearchResults()
  {
    $search = $this->input->post('variablesearch');
    $variables = json_decode($this->getList());
    
    $codes = "";
    $count = 0;
    
    $searchArray = explode(" ", $search);
    
    foreach($variables as $obj)
    {
      $isFound = 0;
      
      foreach($searchArray as $obj2)
      {
        $isFoundNew = strrpos($obj->name, $obj2);
        
        if ($isFoundNew > 0)
          $isFound++;
      }
      
      if ($isFound >0 && $isFound == count($searchArray))
      {
        $codes .= '<div id="'.$obj->name.'" class="fluid varbox';

        if ($count == 0)
          $codes .= ' first'; //add first

        $codes .= '" style="margin:5px 5px 5px 0px; padding:0 0px 0 5px; border-bottom:1px #ccc solid;" data_link="'.$obj->data_link.'">';
        $codes .= '<div class="grid12"  class="" style="font-size: 11px;">';
        $codes .= ''. $obj->name . '';

        $codes .= '<div class="clear"></div></div></div>';

        $count++;
      }
    }
    
    if ($count == 0)
    {
      $codes .= '<div id="0" class="fluid contactbox" style="margin:5px 0 5px 0; border:1px #ccc solid;">';
      $codes .= '<div class="grid12" class="gameInfo">';
      $codes .= '<h6>No Matching Variables Found</h6>';
      $codes .= '<div class="clear"></div></div></div>';
    }
    
    echo $codes;
  }
          
  
  
  function traverse($name, $jsonObj) {
    if( $jsonObj != null) {

      $finalTreeJSON = "";

      foreach($jsonObj as $obj) {
          // k is either an array index or object key

          $newName = $name . '.' . $obj->name;

          if (!empty($obj->data_link))
          {
            $finalTreeJSON .= '{ "data" : "'.htmlspecialchars($obj->name).'", "attr" : { "id" : "'.$this->counter.'", "name" : "'.htmlspecialchars($newName).'", "data_link" : "'.htmlspecialchars($obj->data_link).'" },';
            $finalTreeJSON .= '"children" : []';
            $finalTreeJSON .= '},';
            
          }
          else
          {
            $finalTreeJSON .= '{ "data" : "'.htmlspecialchars($obj->name).'", "attr" : { "id" : "'.$this->counter.'", "name" : "'.htmlspecialchars($newName).'" },';
            $finalTreeJSON .= '"children" : [' . $this->traverse($newName, $obj->children) . ']';
            $finalTreeJSON .= '},';
          }
          
          $this->counter++;
          
          //traverse(name, v.children);
      }

      if (strlen($finalTreeJSON) > 0)
        $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);

      return $finalTreeJSON;
    }
    else 
    {
      return "";
    }
  }
  
  function traverseForList($name, $jsonObj) {
    if( $jsonObj != null) {

      $finalTreeJSON = "";

      foreach($jsonObj as $obj) {
        $newName = $name . '.' . $obj->name;

        if (!empty($obj->data_link))
        {
          $finalTreeJSON .= '{ "name" : "'.htmlspecialchars($newName).'", "data_link" : "'.htmlspecialchars($obj->data_link).'" },';
        }
        else
        {
          $finalTreeJSON .= $this->traverseForList($newName, $obj->children);
        }
      }

      return $finalTreeJSON;
    }
    else 
    {
      return "";
    }
  }
  
  function traverseForNode($id, $jsonObj) {
    if( $jsonObj != null) {

      $finalTreeJSON = "";

      foreach($jsonObj as $obj) {
          // k is either an array index or object key

          if (!empty($obj->data_link))
          {
            $finalTreeJSON .= '{ "data" : "'.$obj->name.'", "attributes" : { "id" : '.$this->counter.', "data_link" : "'+$obj->data_link+'" },';
            $finalTreeJSON .= '"children" : []';
            $finalTreeJSON .= '},';
          }
          else
          {
            $finalTreeJSON .= '{ "data" : "'.$obj->name.'", "attributes" : { "id" : '.$this->counter.' },';
            $finalTreeJSON .= '"children" : [' . $this->traverseForNode($id, $obj->children) . ']';
            $finalTreeJSON .= '},';
          }
          
          $this->counter++;
      }

      $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
      return $finalTreeJSON;
    }
    else 
    {
    }
  }

  public function getTreeJSON()
  {
    $treeJson = base_url()."include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
    
    $finalTreeJSON = '[';

    foreach($treeLoop as $obj)
    {
      $finalTreeJSON .= '{ "data" : "'.$obj->name.'", "attr" : { "id" : "'.$this->counter.'", "name" : "'.$obj->name.'", "state" : "closed" },';
      $finalTreeJSON .= '"children" : [' . $this->traverse($obj->name, $obj->children) . ']';
      $finalTreeJSON .= '},';
      $this->counter++;
    };

    $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
    $finalTreeJSON .= "]";
    
    echo $finalTreeJSON;
  }
  
  public function getListJSON()
  {
    $treeJson = base_url()."include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
    
    $finalTreeJSON = '[';

    foreach($treeLoop as $obj)
    {
      $finalTreeJSON .= $this->traverseForList($obj->name, $obj->children);
    };

    $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
    $finalTreeJSON .= "]";
    
    echo $finalTreeJSON;
  }
  
  public function getList()
  {
    $treeJson = base_url()."include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
    
    $finalTreeJSON = '[';

    foreach($treeLoop as $obj)
    {
      $finalTreeJSON .= $this->traverseForList($obj->name, $obj->children);
    };

    $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
    $finalTreeJSON .= "]";
    
    return $finalTreeJSON;
  }
  
  
  public function getTopTreeJSON()
  {
    $treeJson = base_url()."include/data/testbenches/DriveTrain/cfg1/tree.json";
    
    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
    //print_r($treeLoop);
    $finalTreeJSON = '[ ';
    
    foreach($treeLoop as $obj)
    {
      $finalTreeJSON .= '{ "data" : "'.$obj->name.'", "attributes" : { "id" : "'.$this->counter.'" }, "state" : "closed" ';
      $finalTreeJSON .= '},';
      $this->counter++;
    };

    $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
    $finalTreeJSON .= " ] ";
    
    return $finalTreeJSON;
  }
  
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */