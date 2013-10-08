<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base classes similar to the methods described by Phil Sturgeon
 * See http://philsturgeon.co.uk/blog/2010/02/CodeIgniter-Base-Classes-Keeping-it-DRY
 */

class MY_Controller extends MX_Controller {

    /**
     * Common data
     */
    public $header_data;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // Set global header data - can be merged with or overwritten in module controllers
        $this->header_data = array(
                'site_title'    => $this->config->item('site_name'),
                'site_version'  => $this->config->item('site_version'),
                'keywords'      => "these, are, keywords",
                'description'   => "This is the description.",
                'css_files'     => array(
                    ),
                'js_files'      => array(
                    ),
                'js_files_i18n' => array(
                    )
            );

        // enable the profiler?
        $this->output->enable_profiler($this->config->item('profiler'));
    }

}


/**
 * Base Public Class - used for all public pages
 */
class Public_Controller extends MY_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }

}


/**
 * Base Private Class - used for all private pages
 */
class Private_Controller extends MY_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // must be logged in
        if ( ! $this->session->userdata('logged_in'))
        {
            if (current_url() != base_url())
            {
                //store requested URL to session - will load once logged in
                $data = array('redirect' => current_url());
                $this->session->set_userdata($data);
            }

            redirect('login');
        }
    }

}


/**
 * Base Admin Class - used for all administration pages
 */
class Admin_Controller extends MY_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // load the configured admin theme
        $this->load->set_theme($this->load->admin_theme); // could also do $this->load->set_theme($this->config->item('default_admin_theme')), but the current method is shorter

        // must be logged in
        if ( ! $this->session->userdata('logged_in'))
        {
            if (current_url() != base_url())
            {
                //store requested URL to session - will load once logged in
                $data = array('redirect' => current_url());
                $this->session->set_userdata($data);
            }

            redirect('login');
        }

        // make sure this user is setup as admin
        $logged_in_user = $this->session->userdata('logged_in');
        if ( ! $logged_in_user['is_admin'])
            redirect(base_url());

        // load the admin language file
        $this->lang->load('admin');

        // set up global header data
        $this->header_data = array_merge_recursive($this->header_data, array(
                'css_files'     => array(
                        "/themes/admin/css/admin.css"
                    ),
                'js_files_i18n' => array(
                        $this->jsi18n->translate("/themes/admin/js/admin_i18n.js")
                    )
            ));
    }

}


/**
 * Base API Class - used for all API calls
 */
class API_Controller extends MY_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }

}
