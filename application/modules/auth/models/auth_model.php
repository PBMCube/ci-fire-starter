<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }


    /**
     * Check for valid login credentials
     * 
     * @param string $username
     * @param string $password
     * @return array|bool
     */
    function login($username=NULL, $password=NULL)
    {
        if (is_null($username) || is_null($password))
            return FALSE;

        $sql = "
                SELECT 
                    id,
                    username,
                    first_name,
                    last_name,
                    email,
                    is_admin,
                    status,
                    created,
                    updated
                FROM users
                WHERE (username = '{$username}'
                        OR email = '{$username}')
                    AND password = '" . md5($password) . "'
                    AND status = '1'
                    AND deleted = '0'
            ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
            return $query->row_array();
        else
            return FALSE;
    }

}