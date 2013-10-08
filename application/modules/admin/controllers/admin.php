<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }


    /**
     * Main admin page
     */
    public function dashboard()
    {
        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
                'page_title' => lang('admin title admin'),
                'active'     => "admin/dashboard"
            ));
        $data = $this->header_data;

        // load views
        $data['content'] = $this->load->view('dashboard', NULL, TRUE);
        $this->load->view('admin_template', $data);
    }

}
