<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Admin_Controller {

    /**
     * @var string
     */
    private $redirect_url;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // load the language files
        $this->lang->load('admin');
        $this->lang->load('users');

        // load the users model
        $this->load->model('users_model');

        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url() . "admin/users");
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");

        // use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER))
            $this->redirect_url = $this->session->userdata(REFERRER);
        else
            $this->redirect_url = THIS_URL;
    }


    /**
     * User list page
     */
    public function index()
    {