<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

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
        // get parameters
        $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
        $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;

        // get filters
        $filters = array();

        if ($this->input->get('username'))
            $filters['username'] = $this->input->get('username', TRUE);

        if ($this->input->get('first_name'))
            $filters['first_name'] = $this->input->get('first_name', TRUE);

        if ($this->input->get('last_name'))
            $filters['last_name'] = $this->input->get('last_name', TRUE);

        // build filter string
        $filter = "";
        foreach ($filters as $key=>$value)
            $filter .= "&{$key}={$value}";

        // save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");

        // are filters being submitted?
        if ($this->input->post())
        {
            if ($this->input->post('clear'))
            {
                // reset button clicked
                redirect(THIS_URL);
            }
            else
            {
                // apply the filter(s)
                $filter = "";

                if ($this->input->post('username'))
                    $filter .= "&username=" . $this->input->post('username', TRUE);

                if ($this->input->post('first_name'))
                    $filter .= "&first_name=" . $this->input->post('first_name', TRUE);

                if ($this->input->post('last_name'))
                    $filter .= "&last_name=" . $this->input->post('last_name', TRUE);

                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
        }

        // get list
        $items = $this->users_model->get_all($limit, $offset, $filters, $sort, $dir);

        // build pagination
        $this->pagination->initialize(array(
                'base_url'    => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
                'total_rows'  => $items['total'],
                'per_page'    => $limit
            ));

        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
                'page_title'  => lang('users title user_list'),
                'active'      => THIS_URL
            ));
        $data = $this->header_data;

        // create button to add new users
        $data['controls'] = array(
                'add_new' => array(
                        'bootstrap_button_class' => 'btn-success',
                        'bootstrap_icon_class'   => 'glyphicon-plus-sign',
                        'url'                    => THIS_URL . '/add',
                        'title'                  => lang('users button add_new_user')
                    )
            );

        // set content data
        $content_data = array(
                'this_url'    => THIS_URL,
                'items'       => $items['results'],
                'total'       => $items['total'],
                'filters'     => $filters,
                'filter'      => $filter,
                'pagination'  => $this->pagination->create_links(),
                'limit'       => $limit,
                'offset'      => $offset,
                'sort'        => $sort,
                'dir'         => $dir
            );

        // load views
        $data['content'] = $this->load->view('admin/list', $content_data, TRUE);
        $this->load->view('admin_template', $data);
    }


    /**
     * Add new user
     */
    public function add()
    {
        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', lang('admin input username'), 'required|trim|xss_clean|min_length[5]|max_length[30]');
        $this->form_validation->set_rules('first_name', lang('users input first_name'), 'required|trim|xss_clean|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', lang('users input last_name'), 'required|trim|xss_clean|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', lang('users input email'), 'required|trim|xss_clean|max_length[128]|valid_email');
        $this->form_validation->set_rules('status', lang('users input is_admin'), 'required|xss_clean|numeric');
        $this->form_validation->set_rules('is_admin', lang('users input status'), 'required|xss_clean|numeric');
        $this->form_validation->set_rules('password', lang('admin input password'), 'required|trim|xss_clean|min_length[5]');
        $this->form_validation->set_rules('password_repeat', lang('admin input password_repeat'), 'required|trim|xss_clean|matches[password]');

        if ($this->form_validation->run($this) == TRUE)
        {
            // save the new user
            $saved = $this->users_model->add_user($this->input->post());

            if ($saved)
                $this->session->set_flashdata('message', sprintf(lang('users msg add_user_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            else
                $this->session->set_flashdata('error', sprintf(lang('users error add_user_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));

            // return to list and display message
            redirect($this->redirect_url);
        }

        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
            'page_title'  => lang('users title user_add'),
            'active'      => THIS_URL
        ));
        $data = $this->header_data;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->redirect_url,
            'user'              => NULL,
            'password_required' => TRUE
        );

        // load views
        $data['content'] = $this->load->view('admin/form', $content_data, TRUE);
        $this->load->view('admin_template', $data);
    }


    /**
     * Edit existing user
     *
     * @param int $id
     */
    public function edit($id=NULL)
    {
        // make sure we have a numeric id
        if (is_null($id) || ! is_numeric($id))
            redirect($this->redirect_url);

        // get the data
        $item = $this->users_model->get_user($id);

        // if empty results, return to list
        if ( ! $item)
            redirect($this->redirect_url);

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', lang('admin input username'), 'required|trim|xss_clean|min_length[5]|max_length[30]');
        $this->form_validation->set_rules('first_name', lang('users input first_name'), 'required|trim|xss_clean|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', lang('users input last_name'), 'required|trim|xss_clean|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', lang('users input email'), 'required|trim|xss_clean|max_length[128]|valid_email');
        $this->form_validation->set_rules('status', lang('users input status'), 'required|numeric');
        $this->form_validation->set_rules('is_admin', lang('users input is_admin'), 'required|numeric');
        $this->form_validation->set_rules('password', lang('admin input password'), 'min_length[5]|matches[password_repeat]');
        $this->form_validation->set_rules('password_repeat', lang('admin input password_repeat'), '');

        if ($this->form_validation->run($this) == TRUE)
        {
            // save the changes
            $saved = $this->users_model->edit_user($this->input->post());

            if ($saved)
                $this->session->set_flashdata('message', sprintf(lang('users msg edit_user_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            else
                $this->session->set_flashdata('error', sprintf(lang('users error edit_user_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));

            // return to list and display message
            redirect($this->redirect_url);
        }

        // setup page header data
        $this->header_data = array_merge_recursive($this->header_data, array(
            'page_title'  => lang('users title user_edit'),
            'active'      => THIS_URL
        ));
        $data = $this->header_data;

        // set content data
        $content_data = array(
                'cancel_url'        => $this->redirect_url,
                'user'              => $item,
                'user_id'           => $id,
                'password_required' => FALSE
            );

        // load views
        $data['content'] = $this->load->view('admin/form', $content_data, TRUE);
        $this->load->view('admin_template', $data);
    }


    /**
     * Delete a user
     *
     * @param int $id
     */
    public function delete($id=NULL)
    {
        // make sure we have a numeric id
        if ( ! is_null($id) || ! is_numeric($id))
        {
            // get user details
            $user = $this->users_model->get_user($id);

            if ($user)
            {
                // soft-delete the user
                $delete = $this->users_model->delete_user($id);

                if ($delete)
                    $this->session->set_flashdata('message', sprintf(lang('users msg delete_user'), $user['first_name'] . " " . $user['last_name']));
                else
                    $this->session->set_flashdata('error', sprintf(lang('users error delete_user'), $user['first_name'] . " " . $user['last_name']));
            }
            else
            {
                $this->session->set_flashdata('error', lang('users error user_not_exist'));
            }
        }
        else
        {
            $this->session->set_flashdata('error', lang('users error user_id_required'));
        }

        // return to list and display message
        redirect($this->redirect_url);
    }


    /**
     * Export list to CSV
     */
    function export()
    {
        // get parameters
        $sort = $this->input->get('sort') ? $this->input->get('sort', TRUE) : DEFAULT_SORT;
        $dir  = $this->input->get('dir')  ? $this->input->get('dir', TRUE)  : DEFAULT_DIR;

        // get filters
        $filters = array();

        if ($this->input->get('username'))
            $filters['username'] = $this->input->get('username', TRUE);

        if ($this->input->get('first_name'))
            $filters['first_name'] = $this->input->get('first_name', TRUE);

        if ($this->input->get('last_name'))
            $filters['last_name'] = $this->input->get('last_name', TRUE);

        // get all users
        $items = $this->users_model->get_all(0, 0, $filters, $sort, $dir);

        if ($items['total'] > 0)
        {
            // manipulate the output array
            foreach ($items['results'] as $key=>$item)
            {
                unset($items['results'][$key]['password']);
                unset($items['results'][$key]['deleted']);

                if ($item['status'] == 0)
                    $items['results'][$key]['status'] = lang('admin input inactive');
                else
                    $items['results'][$key]['status'] = lang('admin input active');
            }

            // export the file
            array_to_csv($items['results'], "users");
        }
        else
        {
            // nothing to export
            $this->session->set_flashdata('error', lang('core error no_results'));
            redirect($this->redirect_url);
        }

        exit;
    }

}
