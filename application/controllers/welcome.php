<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

  function __construct()
  {
      parent::__construct();
      $this->data['moduleID'] = 4;
      $this->data['moduleURL'] = base_url();
      $this->data['breadcrumbs'] = '<li class="current"><a href="' . $this->data['moduleURL'] . 'index.php/dashboard" title="">Dashboard</a></li>';
      $this->counter = 1;
  }

	public function index()
	{
            $this->data['pageID'] = 0;
            $this->data['title'] = 'Dashboard';
            $options = array(
                'data' => $this->data,
                'view' => 'layouts/home'
            );

            $this->presentation->template($options);
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */