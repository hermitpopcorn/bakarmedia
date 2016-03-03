<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class administrasi extends CI_Controller
{
    // Ajax-return-page
    public function pengguna($page = 0)
    {
        // Wen de way
        $this->load->model('User', '', true);
        if ($this->User->level() != 'a') {
            return;
        }

        $this->load->model('Administration', '', true);
        $users_rdata = $this->Administration->get_users($page, $this->input->post('filter'));

        $this->load->view('com_tbody_users', array('users' => $users_rdata['result'], 'pages' => $users_rdata['pages']));
    }

    // Ajaxraptors
    public function ubah_data_pengguna()
    {
        // Wen de way
        $this->load->model('User', '', true);
        if ($this->User->level() != 'a') {
            return;
        }

        if (is_numeric($this->input->post('quota')) == false) {
            echo 'Kuota tidak valid.';

            return;
        }

        $this->load->model('Administration', '', true);
        echo $this->Administration->update_userdata($this->input->post('who'), $this->input->post('quota'), $this->input->post('level'));
    }
}
