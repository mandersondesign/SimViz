<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Review Session Model
*
* Author:  Michael Andesimon
*          mandesimon@isis.vanderbilt.edu
*
* Description:  Setters, Getters and functionalities for Review Sessions
*
* Requirements: PHP5 or above
*
*/

//  CI 2.0 Compatibility
if(!class_exists('CI_Model')) { class CI_Model extends Model {} }

class Testbench_Model extends CI_Model
{
    /*
     * Get all Review Sessions
     * return: array()
     */
    function getTestBenches()
    {
      return $this->db->get( 'test_bench' )->result();
    }

    function getConfigurations()
    {
      return $this->db->get('configuration')->result();
    }
    
    /*
     * Get Review Session by simID
     * return: array()
     */
    function getTestbenchByID( $tbID )
    {
      $data = array(
        'tbID' => $tbID
      );
      return $this->db->get_where( 'test_bench', $data, 1 )->row();
    }
    
    function getSimulationByTestBenchID( $tbID )
    {
      $this->db->where('simTestBenchID',$tbID);
      return $this->db->get( 'simulation')->row();
    }
    
    function getSimulationByTestBenchIDAndConfigID( $tbID, $confID )
    {
      $this->db->where('simTestBenchID',$tbID);
      $this->db->where('simConfigID',$confID);
      return $this->db->get( 'simulation' )->result();
    }

    function createTestBench( $data )
    {
      $data['tbCreateDate'] = date("Y-m-d H:i:s");
      $this->db->insert('test_bench', $data);

      return $this->db->insert_id();
    }

    function updateTestBench( $data, $tbID )
    {
        
      $this->db->where('tbID', $tbID);
      $this->db->update('test_bench', $data);

      return $this->db->affected_rows() == 1;
    }
    
    function removeSimulation( $simID )
    {
      $data = array(
        'simIsActive'             => 0,
        'simRemoveDate'    => date("Y-m-d H:i:s")
      );
      
      $this->db->where('simID', $simID);
      $this->db->update('simulation', $data);

      return $this->db->affected_rows() == 1;
    }

}
