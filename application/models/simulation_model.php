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

class Simulation_Model extends CI_Model
{
    /*
     * Get all Review Sessions
     * return: array()
     */
    function getSimulations()
    {
      return $this->db->get( 'simulation' )->result();
    }

    /*
     * Get Review Session by simID
     * return: array()
     */
    function getSimulationByID( $simID )
    {
      $data = array(
        'simID' => $simID
      );
      return $this->db->get_where( 'simulation', $data, 1 )->row();
    }
    
    
    function getConfigurations()
    {
      return $this->db->get( 'configuration' )->result();
    }
    
    function getConfigByID( $configID )
    {
      $data = array(
        'confID' => $configID
      );
      return $this->db->get_where( 'configuration', $data, 1 )->row();
    }
    
    function getSimulationByTestBenchID( $tbID )
    {
      $this->db->where('simTestBenchID',$tbID);
      return $this->db->get( 'simulation')->row();
    }

    function createConfiguration( $data )
    {
      $data['confCreateDate'] = date("Y-m-d H:i:s");
      $this->db->insert('configuration', $data);

      return $this->db->insert_id();
    }
    
    function createSimulation( $data )
    {
      $data['simCreateDate'] = date("Y-m-d H:i:s");
      $this->db->insert('simulation', $data);

      return $this->db->insert_id();
    }

    function updateSimulation( $data, $simID )
    {
        
      $this->db->where('simID', $simID);
      $this->db->update('simulation', $data);

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
