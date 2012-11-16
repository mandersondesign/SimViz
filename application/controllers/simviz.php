
<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Simviz extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   * 		http://example.com/index.php/welcome
   * 	- or -  
   * 		http://example.com/index.php/welcome/index
   * 	- or -
   * Since this controller is set as the default controller in 
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  function __construct()
  {
    parent::__construct();

    $this->load->model('simulation_model', 'sims', TRUE);
    $this->load->model('testbench_model', 'tb', TRUE);
    $this->load->model('plot_model', 'plots', TRUE);

    $this->load->helper(array('form', 'url'));

    $this->data['moduleID'] = 1;
    $this->data['moduleURL'] = base_url() . 'graph';
    $this->data['breadcrumbs'] = '<li><a href="' . site_url() . '/dashboard">Dashboard</a></li>';
    $this->data['breadcrumbs'] .= '<li class="current"><a href="' . $this->data['moduleURL'] . '/" title="">Simulation Visualizer</a></li>';


    $this->counter = 1;
  }

  public function index($testBenchID = 1, $plotID = 0, $datafile = "")
  {
    $this->data['pageID'] = 0;
    $this->data['title'] = 'SimViz Home';

    $this->data['testbenches'] = $this->tb->getTestBenches();
    $this->data['configurations'] = $this->sims->getConfigurations();

    $options = array(
        'data' => $this->data,
        'view' => 'simviz/home'
    );

    $this->presentation->template($options);
  }

  public function plot($plotID = 0)
  {
    if ($plotID > 0)
    {



//Get Plot and Simulation
      $plot = $this->plots->getPlotByID($plotID);

//echo $plot->PlotTreeLocation;
//return;

      $simulation = $this->sims->getSimulationByID($plot->PlotSimID);
      $conf = $this->sims->getConfigByID($simulation->simConfigID);
      $this->loadVariables($plotID);



//Get JSON Files and Pass to Client
      $treeJson = base_url() . $plot->PlotTreeLocation;
      $treeJson = str_replace("\\", "/", $treeJson);
      
      $this->data['jsonTreeFilePath'] = $treeJson;
      $this->data['jsonPlainTest'] = $this->getJSONFilePlain($this->data['jsonTreeFilePath']);
      $this->data['jsonDecoded'] = $this->getJSONFile($this->data['jsonTreeFilePath']);


//Pass Data Members to Front
      $this->data['plot'] = $plot;
      $this->data['simID'] = $simulation->simID;
      $this->data['simulation'] = $simulation;
      $this->data['conf'] = $conf;

      $this->data['testBenchID'] = $plot->PlotTestBenchID;
      $this->data['plots'] = $this->plots->getPlotsBySimulationID($simulation->simID);

//Page Information
      $this->data['title'] = 'Plot ' . $plotID . ' Design Review';
      $this->data['pageID'] = 1;

//print_r($plot);
    }

    $options = array(
        'data' => $this->data,
        'view' => 'simviz/plot'
    );

    $this->presentation->template($options);
  }

  public function plot2($plotID = 0, $plotType = 0)
  {
    if ($plotID > 0)
    {
//Get Plot and Simulation
      $plot = $this->plots->getPlotByID($plotID);
      $simulation = $this->sims->getSimulationByID($plot->PlotSimID);
      $conf = $this->sims->getConfigByID($simulation->simConfigID);
      $this->loadVariables($plotID);

//Get JSON Files and Pass to Client
      $treeJson = base_url() . $plot->PlotTreeLocation;
      $treeJson = str_replace("\\", "/", $treeJson);
      
      $this->data['jsonTreeFilePath'] = $treeJson;
      $this->data['jsonPlainTest'] = $this->getJSONFilePlain($this->data['jsonTreeFilePath']);
      $this->data['jsonDecoded'] = $this->getJSONFile($this->data['jsonTreeFilePath']);


//Pass Data Members to Front
      $this->data['plot'] = $plot;
      $this->data['simID'] = $simulation->simID;
      $this->data['simulation'] = $simulation;
      $this->data['conf'] = $conf;

      $this->data['testBenchID'] = $plot->PlotTestBenchID;
      $this->data['plots'] = $this->plots->getPlotsBySimulationID($simulation->simID);

//Page Information
      $this->data['title'] = 'Plot ' . $plotID . ' Design Review';
      $this->data['pageID'] = 1;

//print_r($plot);
    }

    $options = array();

    if ($plotType == 0)
    {
      $options = array(
          'data' => $this->data,
          'view' => 'simviz/plot'
      );
    }
    else if ($plotType == 1)
    {
      $options = array(
          'data' => $this->data,
          'view' => 'simviz/plotCompare'
      );
    };

    $this->presentation->template($options);
  }

  public function getSearchResults($plotID)
  {
    $search = $this->input->post('variablesearch');
    $plotVariables = $this->plots->getVariablesByPlotID($plotID);
//$variables = json_decode($this->getList());

    $codes = "";
    $count = 0;

    $searchArray = explode(" ", $search);

//echo count($plotVariables)."<br/>";

    foreach ($plotVariables as $obj)
    {
      $isFound = 0;

      $varName = (string) $obj->varName;
//echo $varName.'<br/>';
      foreach ($searchArray as $obj2)
      {
        $isFoundNew = strrpos(strtolower($varName), strtolower($obj2));

        if ($isFoundNew > 0)
          $isFound++;
      }

      if ($isFound > 0 && $isFound == count($searchArray))
      {
        $codes .= '<div id="' . $obj->varID . '" class="fluid varbox';

        if ($count == 0)
          $codes .= ' first'; //add first

        $codes .= '" style="margin:5px 5px 5px 0px; padding:0 0px 0 5px; border-bottom:1px #ccc solid;" name="' . $obj->varName . '" shortname="' . $obj->varShortName . '" data_link="' . $obj->varDataLink . '">';
        $codes .= '<div class="grid12"  class="" style="font-size: 11px;">';
        $codes .= '' . $obj->varShortName . '';

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

  public function getJSONFile($jsonFilePath)
  {

    $jsonStream = file_get_contents($jsonFilePath);

//Retrieve json file
    if (json_decode($jsonStream) == NULL)
    {
      die("No valid JSON file found for this component");
    }
    else
    {
      $jsonDecoded = json_decode($jsonStream, true);
      return $jsonDecoded;
    }
  }

  public function getJSONFilePlain($jsonFilePath)
  {
    var_dump($jsonFilePath);
    $jsonStream = file_get_contents($jsonFilePath);



//Retrieve json file
    if (json_decode($jsonStream) == NULL)
    {
      return "No valid JSON file found for this component";
    }
    else
    {
      return $jsonStream;
    }
  }

  public function getJSONFileAjax($jsonFilePath)
  {
    $jsonDirectory = base_url();
    $jsonFile = $jsonDirectory . $componentName . "_data.json";
    $jsonStream = file_get_contents($jsonFile);

//Retrieve json file
    if (json_decode($jsonStream) == NULL)
    {
      echo "0|";
      echo "No valid JSON file found for this component";
    }
    else
    {
      echo "1|";
      echo $jsonStream;
    }
  }

  public function buildTreeJSON($jsonDecoded)
  {
    
  }

  public function getSearchResultsOld()
  {
    $search = $this->input->post('variablesearch');
    $variables = json_decode($this->getList());

    $codes = "";
    $count = 0;

    $searchArray = explode(" ", $search);

    foreach ($variables as $obj)
    {
      $isFound = 0;

      foreach ($searchArray as $obj2)
      {
        $isFoundNew = strrpos($obj->name, $obj2);

        if ($isFoundNew > 0)
          $isFound++;
      }

      if ($isFound > 0 && $isFound == count($searchArray))
      {
        $codes .= '<div id="' . $obj->name . '" class="fluid varbox';

        if ($count == 0)
          $codes .= ' first'; //add first

        $codes .= '" style="margin:5px 5px 5px 0px; padding:0 0px 0 5px; border-bottom:1px #ccc solid;" data_link="' . $obj->data_link . '">';
        $codes .= '<div class="grid12"  class="" style="font-size: 11px;">';
        $codes .= '' . $obj->name . '';

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

  /*   * ***********************************************************************
   * Actually Used Functions
   * *********************************************************************** */

  public function loadVariables($plotID)
  {
    $plot = $this->plots->getPlotByID($plotID);

    $jsonVariableList = $this->getList($plotID);
    $variables = json_decode($jsonVariableList);

    var_dump($jsonVariableList);
    if (count($this->plots->getVariablesByPlotID($plotID)) == 0)
    {
      foreach ($variables as $obj)
      {
//        $nameArray = explode(".", $obj->name);
//
//        $nameArrayCount = count($nameArray);
//
//        if ($nameArrayCount == 2)
//        {
//          $shortName = $nameArray[0] . ': ' . $nameArray[$nameArrayCount - 1];
//        }
//        else if ($nameArrayCount == 3)
//        {
//          $shortName = $nameArray[0] . ': ' . $nameArray[$nameArrayCount - 2] . '.' . $nameArray[$nameArrayCount - 1];
//        }
//        else if ($nameArrayCount == 4)
//        {
//          $shortName = $nameArray[0] . ': ' . '.' . $nameArray[$nameArrayCount - 3] . '.' . $nameArray[$nameArrayCount - 2] . '.' . $nameArray[$nameArrayCount - 1];
//        }
//        else
//        {
//          $shortName = $nameArray[0] . ': ' . $nameArray[$nameArrayCount - 4] . '.' . $nameArray[$nameArrayCount - 3] . '.' . $nameArray[$nameArrayCount - 2] . '.' . $nameArray[$nameArrayCount - 1];
//        }
//$shortName = $obj->name;

        $varData = array(
            'varPlotID' => $plotID,
            'varName' => $obj->name,
            'varShortName' => $obj->name,
            'varDataLink' => $obj->data_link
        );

        $this->plots->createVariable($varData);
      }
    }
  }

//List of Variables
  public function getList($plotID)
  {
    $plot = $this->plots->getPlotByID($plotID);
    $treeJson = base_url() . $plot->PlotTreeLocation;
    $treeJson = str_replace("\\", "/", $treeJson);
    
    $treeLoop = json_decode($this->getJSONFilePlain($treeJson), true);

    //var_dump($treeLoop);
    
    $finalTreeJSON = '[';

    foreach ($treeLoop as $obj)
    {
      if (!empty($obj['children']))
        $finalTreeJSON .= $this->newTraverseForList($obj['children']);
    };

    $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);
    $finalTreeJSON .= "]";

    
    
    return $finalTreeJSON;
  }

  function newTraverseForList($jsonObj)
  {
    if ($jsonObj != null)
    {
      $finalTreeJSON = "";
      
      foreach ($jsonObj as $obj)
      {
        //print_r($obj);
        if (array_key_exists('data_link', $obj))
        {
          
          $finalTreeJSON .= '{ "name" : "' . $obj['name'] . '", "full_name" : "' . $obj['full_name'] . '", "data_link" : "' . $obj['data_link'] . '" },';
        }
        else
        {
          if (!empty($obj['children']))
            $finalTreeJSON .= $this->newTraverseForList($obj['children']);
        }
      }

      return $finalTreeJSON;
    }
    else
    {
// jsonOb is a number or string

      return "";
    }
  }

  /*
   * Additional Functions
   */

  public function getListJSON()
  {
    $treeJson = base_url() . "include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));

    $finalTreeJSON = '[';

    foreach ($treeLoop as $obj)
    {
      $finalTreeJSON .= $this->traverseForList($obj->name, $obj->children);
    };

    $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);
    $finalTreeJSON .= "]";

    echo $finalTreeJSON;
  }

  function traverseForList($name, $jsonObj)
  {
    if ($jsonObj != null)
    {

      $finalTreeJSON = "";

      foreach ($jsonObj as $obj)
      {
        $newName = $name . '.' . $obj->name;

        if (!empty($obj->data_link))
        {
          $finalTreeJSON .= '{ "name" : "' . htmlspecialchars($newName) . '", "data_link" : "' . $obj->data_link . '" },';
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
// jsonOb is a number or string

      return "";
    }
  }

  function traverse($name, $jsonObj)
  {
    if ($jsonObj != null)
    {

      $finalTreeJSON = "";

      foreach ($jsonObj as $obj)
      {
// k is either an array index or object key

        $newName = $name . '.' . $obj->name;

        if (!empty($obj->data_link))
        {

//$finalTreeJSON .= '{ "data" : "'.htmlspecialchars($obj->name).'" },';
//$finalTreeJSON .= $obj->data_link;
//$finalTreeJSON .= '{ "data" : "'.htmlspecialchars($obj->name).'", "attr" : { "id" : "'.$this->counter.'", "name" : "'.$name.'", "data_link" : "'+htmlspecialchars($obj->data_link)+'" }},';

          $finalTreeJSON .= '{ "data" : "' . htmlspecialchars($obj->name) . '", "attr" : { "id" : "' . $this->counter . '", "name" : "' . htmlspecialchars($newName) . '", "data_link" : "' . htmlspecialchars($obj->data_link) . '" },';
          $finalTreeJSON .= '"children" : []';
          $finalTreeJSON .= '},';
        }
        else
        {
          $finalTreeJSON .= '{ "data" : "' . htmlspecialchars($obj->name) . '", "attr" : { "id" : "' . $this->counter . '", "name" : "' . htmlspecialchars($newName) . '" },';
          $finalTreeJSON .= '"children" : [' . $this->traverse($newName, $obj->children) . ']';
          $finalTreeJSON .= '},';
        }

        $this->counter++;

//traverse(name, v.children);
      }

      if (strlen($finalTreeJSON) > 0)
        $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);

      return $finalTreeJSON;
    }
    else
    {
// jsonOb is a number or string

      return "";
    }
  }

  function traverseForNode($id, $jsonObj)
  {
    if ($jsonObj != null)
    {

      $finalTreeJSON = "";

      foreach ($jsonObj as $obj)
      {
// k is either an array index or object key

        if (!empty($obj->data_link))
        {
          $finalTreeJSON .= '{ "data" : "' . $obj->name . '", "attributes" : { "id" : ' . $this->counter . ', "data_link" : "' + $obj->data_link + '" },';
          $finalTreeJSON .= '"children" : []';
          $finalTreeJSON .= '},';
        }
        else
        {
          $finalTreeJSON .= '{ "data" : "' . $obj->name . '", "attributes" : { "id" : ' . $this->counter . ' },';
          $finalTreeJSON .= '"children" : [' . $this->traverseForNode($id, $obj->children) . ']';
          $finalTreeJSON .= '},';
        }

        $this->counter++;
      }

      $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);
      return $finalTreeJSON;
    }
    else
    {
// jsonOb is a number or string
    }
  }

  public function getTreeNodeJSON($id)
  {
//    $treeJson = base_url()."include/data/tree.json";
//
//    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
//    
//    $finalTreeJSON = '{ "data" : [';
//
//    
//    
//    foreach($treeLoop as $obj)
//    {
//      if ($this->counter == $id)
//      {
//        foreach($obj->children as $child)
//        {
//          $this->traverse($obj->name, $obj->children)
//        }
//      }
//      $finalTreeJSON .= '{ "data" : "'.$obj->name.'", "attributes" : { "id" : "'.$this->counter.'" }, "state" : "closed"';
//      $finalTreeJSON .= '"children" : [' .  . ']';
//      $finalTreeJSON .= '},';
//      
//      $this->counter++;
//    };
//
//    $finalTreeJSON = substr($finalTreeJSON, 0,strlen($finalTreeJSON)-1);
//    $finalTreeJSON .= "]}";
//    
//    echo $finalTreeJSON;
  }

  public function getTreeJSON()
  {
    $treeJson = base_url() . "include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));

    $finalTreeJSON = '[';

    foreach ($treeLoop as $obj)
    {
      $finalTreeJSON .= '{ "data" : "' . $obj->name . '", "attr" : { "id" : "' . $this->counter . '", "name" : "' . $obj->name . '", "state" : "closed" },';
      $finalTreeJSON .= '"children" : [' . $this->traverse($obj->name, $obj->children) . ']';
      $finalTreeJSON .= '},';
      $this->counter++;
    };

    $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);
    $finalTreeJSON .= "]";

    echo $finalTreeJSON;
  }

  public function getTopTreeJSON()
  {
    $treeJson = base_url() . "include/data/testbenches/DriveTrain/cfg1/tree.json";

    $treeLoop = json_decode($this->getJSONFilePlain($treeJson));
//print_r($treeLoop);
    $finalTreeJSON = '[ ';

    foreach ($treeLoop as $obj)
    {
      $finalTreeJSON .= '{ "data" : "' . $obj->name . '", "attributes" : { "id" : "' . $this->counter . '" }, "state" : "closed" ';
      $finalTreeJSON .= '},';
      $this->counter++;
    };

    $finalTreeJSON = substr($finalTreeJSON, 0, strlen($finalTreeJSON) - 1);
    $finalTreeJSON .= " ] ";

    return $finalTreeJSON;
  }

  public function getConfigurationsJSON()
  {
    $tbID = $this->input->post('tbID');
  }

  public function testPost()
  {
    var_dump($this->input);
    echo $this->input->post('test');
    $tbID = $this->input->post('select_tb');
    echo $tbID . '<br/>';
  }

  public function addNewPlot()
  {
    var_dump($this->input);
    $plotname = $this->input->post('plot_name');
    $simID = $this->input->post('simID');
    $sim = $this->sims->getSimulationByID($simID);
    echo $plotname . '<br/>';
    
    $plotJSON = '{';
    $plotJSON .= '"Name" : "'.$plotname.'",';
    $plotJSON .= '"TestbenchID" : "'.$sim->simTestBenchID.'",';
    $plotJSON .= '"ExtremeLow" : "0",';
    $plotJSON .= '"ExtremeHigh" : "0",';
    $plotJSON .= '"series" : {},';
    $plotJSON .= '"xaxis" : "Time",';
    $plotJSON .= '"data_folder" : "'.addslashes ($sim->simDataPath).'"';
    $plotJSON .= '}';
    
    $data = array(
        "plotSimID" => $simID,
        "plotVersion" => 1,
        "plotSettings" => $plotJSON
        
    );
    
    $plotID = $this->plots->createPlot($data);
    redirect('manager/sim/'.$simID);
    
  }
  
  public function addNewSimulation()
  {
    $tbID = $this->input->post('select_tb');
    $confID = $this->input->post('select_config');
    $simName = $this->input->post('new_simulation');
    //$upFile = $this->input->post('upfile');

    var_dump($this->input);
    echo $tbID . '<br/>';
    echo $confID . '<br/>';
    ;

    $folderBase = ".\\";

    $simID = 0;
    
    if ($tbID != 0)
    {
      $tb = $this->tb->getTestBenchByID($tbID);

      if ($confID != 0)
      {
        $cfg = $this->sims->getConfigByID($confID);

        $simData = array(
            "simTestBenchID" => $tbID,
            "simConfigID" => $confID,
            "simName" => $simName
        );

        $simID = $this->sims->createSimulation($simData);
      }
      else
      {
        $confName = $this->input->post('new_config');
        $confData = array(
            "confName" => $confName,
            "confFolderName" => trim(str_replace(" ", "", $confName))
        );
        $confID = $this->sims->createConfiguration($confData);
        $simData = array(
            "simTestBenchID" => $tbID,
            "simConfigID" => $confID,
            "simName" => $simName
        );

        $simID = $this->sims->createSimulation($simData);
      }
    }
    else
    {
      $tbName = $this->input->post('new_testbench');
      $tbData = array(
          "tbProjectID" => 1,
          "tbName" => $tbName,
          "tbFolderName" => trim(str_replace(" ", "", $tbName))
      );
      $tbID = $this->tb->createTestBench($tbData);

      if ($confID != 0)
      {
        $simData = array(
            "simTestBenchID" => $tbID,
            "simConfigID" => $confID,
            "simName" => $simName
        );

        $simID = $this->sims->createSimulation($simData);
      }
      else
      {
        $confName = $this->input->post('new_config');
        $confData = array(
            "confName" => $confName,
            "confFolderName" => php_strip_whitespace($confName)
        );
        $confID = $this->sims->createConfiguration($confData);
        $simData = array(
            "simTestBenchID" => $tbID,
            "simConfigID" => $confID,
            "simName" => $simName
        );

        $simID = $this->sims->createSimulation($simData);
      }
    }

    $tb = $this->tb->getTestBenchByID($tbID);
    $cfg = $this->sims->getConfigByID($confID);

    //Make FolderPath

    $folderBase = str_replace('addNewSimulation', '', $_SERVER['PATH_TRANSLATED']);

    $folderPath = $folderBase . "include\\data\\testbenches\\" . $tb->tbFoldername . "\\" . $cfg->confFolderName . "\\" . trim(str_replace(" ", "", $simName));
    echo "<br/>" . $folderPath . "<br/>";
    if (!is_dir($folderPath))
      mkdir($folderPath,"0777",true);

    $simData = array(
      "simDataPath" => "include/data/testbenches/" . $tb->tbFoldername . "/" . $cfg->confFolderName . "/" . trim(str_replace(" ", "", $simName)) 
    );
    $this->sims->updateSimulation($simData, $simID);
    
    //Move File to new folder

    $u_config['upload_path'] = $folderPath;
    $u_config['allowed_types'] = '*';
    $u_config['max_size'] = '100000000000';

    $this->load->library('upload', $u_config);

    if (!$this->upload->do_upload('upfile'))
    {
      $error = array('error' => $this->upload->display_errors());
      var_dump($error);
      //$this->load->view('upload_form', $error);
    }
    else
    {
      $data = array('upload_data' => $this->upload->data());
      var_dump($data);
      $upload_data = $this->upload->data();

      //Run Python Script to Convert it
      $pyscript = '.\\include\\scripts\\mat_conversion_v2.py';
      $python = 'C:\\Python27\\python.exe';
      
      $newFilePath = $upload_data['full_path'];
      //$output = 'C:\\Python27\\python.exe';
      $cmd = "$python $pyscript $newFilePath $folderPath";
      exec("$cmd", $output);
      var_dump($output);

      echo 'success';
      //$this->load->view('upload_success', $data);
      
      redirect('manager/sim/'.$simID);
    }
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */