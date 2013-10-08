<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends Private_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // load the admin language file
        $this->lang->load('profile');
    }


    /**
	 * Default
     */
	public function index()
	{
        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
            'page_title' => lang('profile title')
        ));
        $data = $this->header_data;

        // set content data
        $logged_in_user = $this->session->userdata('logged_in');
        $content_data = array(
            'username'   => $logged_in_user['username'],
            'first_name' => $logged_in_user['first_name'],
            'last_name'  => $logged_in_user['last_name'],
            'email'      => $logged_in_user['email']
        );

        // load views
        $data['content'] = $this->load->view('view_profile', $content_data, TRUE);
		$this->load->view('template', $data);
	}

}