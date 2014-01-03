<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        
        // load the language file
        $this->lang->load('dashboard');        
    }


    /**
     * Main admin page
     */
    public function index()
    {
        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
                'page_title' => lang('admin title admin'),
                'active'     => "admin/dashboard",
                'js_files_i18n' => array(
                        $this->jsi18n->translate("/application/modules/dashboard/assets/js/dashboard_i18n.js")
                    )
            ));
        $data = $this->header_data;

        // load views
        $data['content'] = $this->load->view('dashboard', NULL, TRUE);
        $this->load->view('admin_template', $data);
    }

}
