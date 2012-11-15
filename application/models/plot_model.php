<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
/**
 * Name:  Plot Model
 *
 * Author:  Michael Anderson
 *          manderson@isis.vanderbilt.edu
 *
 * Description:  Setters, Getters and functionalities for Plots
 *
 * Requirements: PHP5 or above
 *
 */
//  CI 2.0 Compatibility
if (!class_exists('CI_Model'))
{

  class CI_Model extends Model
  {
    
  }

}

class Plot_Model extends CI_Model
{
  /*
   * Get all Review Sessions
   * return: array()
   */

  function getPlots()
  {
    return $this->db->get('vw_plot')->result();
  }

  function getVariablesByPlotID($plotID)
  {
    $data = array(
        'varPlotID' => $plotID
    );
    return $this->db->get_where('variable', $data)->result();
  }

  /*
   * Get Review Session by plotID
   * return: array()
   */

  function getPlotByID($plotID)
  {
    $data = array(
        'plotID' => $plotID
    );
    return $this->db->get_where('vw_plot', $data, 1)->row();
  }

  function getPlotsBySimulationID($simID)
  {
    $this->db->where('PlotSimID', $simID);
    return $this->db->get('vw_plot')->result();
  }

  function createPlot($data)
  {
    $data['plotCreateDate'] = date("Y-m-d H:i:s");
    $this->db->insert('plot', $data);

    return $this->db->affected_rows() == 1;
  }

  function createVariable($data)
  {
    $this->db->insert('variable', $data);

    return $this->db->affected_rows() == 1;
  }

  function updatePlot($data, $plotID)
  {

    $this->db->where('plotID', $plotID);
    $this->db->update('plot', $data);

    return $this->db->affected_rows() == 1;
  }

  function removePlot($plotID)
  {
    $data = array(
        'plotIsActive' => 0,
        'plotRemoveDate' => date("Y-m-d H:i:s")
    );

    $this->db->where('plotID', $plotID);
    $this->db->update('plot', $data);

    return $this->db->affected_rows() == 1;
  }

}
