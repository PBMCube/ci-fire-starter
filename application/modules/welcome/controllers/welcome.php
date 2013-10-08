<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Public_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }


    /**
	 * Default
     */
	public function index()
	{
        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
            'page_title' => "Welcome to " . $this->config->item('site_name')
        ));
        $data = $this->header_data;

        // load views
        $data['content'] = $this->load->view('welcome_message', NULL, TRUE);
		$this->load->view('template', $data);
	}

}