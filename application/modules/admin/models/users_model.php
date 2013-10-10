<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }


    /**
     * Get list of non-deleted users
     *
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @param string $sort
     * @param string $dir
     * @return array|bool
     */
    function get_all($limit=0, $offset=0, $filters=array(), $sort='last_name', $dir='ASC')
    {
        $sql = "
                SELECT SQL_CALC_FOUND_ROWS *
                FROM users
                WHERE deleted = '0'
            ";

        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
                $sql .= " AND {$key} LIKE '%{$value}%'";
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
            $sql .= " LIMIT {$offset}, {$limit}";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            $results['results'] = $query->result_array();
        else
            $results['results'] = NULL;

        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;

        return $results;
    }

    
    /**
     * Get specific user
     * 
     * @param int $id
     * @return array|bool
     */
    function get_user($id=NULL)
    {
        if (is_null($id))
            return FALSE;

        $sql = "
                SELECT *
                FROM users
                WHERE id = {$id}
                    AND deleted = '0'
            ";
                
        $query = $this->db->query($sql);

        if ($query->num_rows())
            return $query->row_array();
        else
            return FALSE;
    }


    /**
     * Edit an existing user
     * 
     * @param array $data
     * @return bool
     */
    function edit_user($data=array())
    {
        if (empty($data))
            return FALSE;

        $sql = "
                UPDATE users
                SET
                    username = '" . $data['username'] . "',
            ";

        if ($data['password'] != '')
        {
            // secure password
            $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
            $password = hash('sha512', $data['password'] . $salt);

            $sql .= "
                    password = '{$password}',
                    salt = '{$salt}',
                ";
        }

        $sql .= "
                    first_name = '" . $data['first_name'] . "',
                    last_name = '" . $data['last_name'] . "',
                    email = '" . $data['email'] . "',
                    is_admin = '" . $data['is_admin'] . "',
                    status = '" . $data['status'] . "',
                    updated = NOW()
                WHERE id = " . $data['id'] . "
                    AND deleted = '0'
            ";

        $this->db->query($sql);
        
        if ($this->db->affected_rows())
            return TRUE;
        else
            return FALSE;        
    }


    /**
     * Add a new user
     * 
     * @param array $data
     * @return mixed|bool
     */
    function add_user($data=array())
    {
        if (empty($data))
            return FALSE;

        // secure password
        $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
        $password = hash('sha512', $data['password'] . $salt);

        $sql = "
                INSERT INTO users (
                    username, password, salt, first_name, last_name, email, is_admin, status, deleted, created, updated
                ) VALUES (
                    '" . $data['username'] . "',
                    '" . $password . "',
                    '" . $salt . "',
                    '" . $data['first_name'] . "',
                    '" . $data['last_name'] . "',
                    '" . $data['email'] . "',
                    '" . $data['is_admin'] . "',
                    '" . $data['status'] . "',
                    '0',
                    NOW(),
                    NOW()
                )
            ";
        
        $this->db->query($sql);
        
        if ($id = $this->db->insert_id())
            return $id;
        else
            return FALSE;        

    }

    
    /**
     * Soft delete an existing user
     * 
     * @param int $id
     * @return bool
     */
    function delete_user($id=NULL)
    {
        if (is_null($id))
            return FALSE;

        $sql = "
                UPDATE users
                SET
                    is_admin = '0',
                    status = '0',
                    deleted = '1',
                    updated = NOW()
                WHERE id = {$id}
                    AND id > 1
            ";

        $this->db->query($sql);
        
        if ($this->db->affected_rows())
            return TRUE;
        else
            return FALSE;
    }

}