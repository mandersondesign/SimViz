<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Presentation Class
 *
 * No more view repetition
 *
 * @package			  CodeIgniter
 * @subpackage		Libraries
 * @category		  Libraries
 * @author			  Chris Abrams
 * @license			  http://
 * @link			    http://getsparks.org/packages
 */
//class Presentation extends CI_Loader
class Presentation {
	
	function __construct($config = array()) {
		$this->CI =& get_instance();
	}
	
	/**
	 * Load Presentation
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @access	public
	 * @param	  string
	 * @param	  array
	 * @param	  bool
	 * @return	void
	 */
	//function template($view, $vars = array(), $return = FALSE) {
	function template($options) {
		
		/*echo "<pre>";
		print_r($options);
		echo "</pre>";
		exit;*/
		
		//Option defaults
		$data = array();
		$layout = 'index';
		$return = FALSE;
		$view = '';
		
		//Loop through options and fetch config
		foreach($options as $keyopt=>$valopt) {
			$$keyopt = $valopt;
		}
		
		$yield = $this->CI->load->view($view, $data, TRUE);
		
		$data['yield'] = $yield;

		return $this->CI->load->view("layouts/$layout", $data, $return);
	}
  
  
  
  function minimal($options) {
		
		/*echo "<pre>";
		print_r($options);
		echo "</pre>";
		exit;*/
		
		//Option defaults
		$data = array();
		$layout = 'minimal';
		$return = FALSE;
		$view = '';
		
		//Loop through options and fetch config
		foreach($options as $keyopt=>$valopt) {
			$$keyopt = $valopt;
		}
		
		$yield = $this->CI->load->view($view, $data, TRUE);
		
		$data['yield'] = $yield;

		return $this->CI->load->view("layouts/$layout", $data, $return);
	}
}