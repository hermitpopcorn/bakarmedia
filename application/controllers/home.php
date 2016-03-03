<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class home extends CI_Controller
{
    public function index()
    {
        $this->load->model('User', '', true);
        // Set body components
        $passvar = array('components' => array());
        if ($this->session->userdata('user') === false) {
            $passvar['components']['home'] = $this->load->view('com_home', null, true);
        } else {
            if ($this->User->level() == 'a') {
                // Admin
                $this->load->model('Administration', '', true);
                $users = $this->Administration->get_users();
                $passvar['components']['dirlist'] = $this->load->view('com_admin', array('users' => $users), true);
            } else {
                // User biasa
                $passvar['components']['dirlist'] = $this->load->view('com_listing', array('rumah' => 'rumah_sendiri'), true);
            }
        }

        // Load everything
        $this->load->view('_skeleton', $passvar);
    }
}
