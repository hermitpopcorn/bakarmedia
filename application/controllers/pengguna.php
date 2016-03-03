<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class pengguna extends CI_Controller
{
    public function masuk()
    {
        if ($this->input->post('email') === false or $this->input->post('pass') === false) {
            echo 'Alamat surel atau kata sandi kosong.';

            return;
        }

        $this->load->model('User', '', true);
        echo($this->User->logon(strtolower($this->input->post('email')), $this->input->post('pass'), $this->input->post('remember')));
    }

    public function keluar()
    {
        $this->load->model('User');
        $this->User->logout();
    }

    public function daftar()
    {
        if (strpos($this->input->post('email'), '"') !== false or strpos($this->input->post('email'), "'") !== false) {
            echo 'Ada isian yang kosong atau salah.';

            return;
        }

        if ($this->input->post('email') === false or $this->input->post('pass') === false or $this->input->post('fname') === false or $this->input->post('lname') === false) {
            echo 'Ada isian yang kosong atau salah.';

            return;
        } elseif (strlen($this->input->post('email')) < 4 or strlen($this->input->post('pass')) < 6 or strlen($this->input->post('fname')) < 1 or strlen($this->input->post('lname')) < 1) {
            echo 'Ada isian yang kosong atau salah.';

            return;
        } elseif (strlen(trim($this->input->post('fname'))) < 1 || strlen(trim($this->input->post('lname'))) < 1) {
            echo 'Ada isian yang kosong atau salah.';

            return;
        }

        if (sizeof(explode($this->input->post('email'), '@')) < 1 or sizeof(explode($this->input->post('email'), '.')) < 1) {
            echo 'Alamat surel tidak valid.';
        }

        $this->load->model('User', '', true);
        $r = $this->User->register(strtolower($this->input->post('email')), $this->input->post('pass'), $this->input->post('fname'), $this->input->post('lname'));
        if ($r === true) {
            echo 'berhasil';
        } else {
            echo $r;
        }
    }

    public function perbarui_pengaturan()
    {
        if ($this->input->post('pass') === false or strlen($this->input->post('pass')) < 1) {
            echo 'a';

            return;
        } elseif ($this->input->post('newpass1') !== $this->input->post('newpass2')) {
            echo 'b';

            return;
        } elseif ($this->input->post('fname') === false or strlen($this->input->post('fname')) < 4 or $this->input->post('lname') === false or strlen($this->input->post('lname')) < 4) {
            echo 'c';

            return;
        }

        $pass = MD5($this->input->post('pass'));
        $newpass = strlen($this->input->post('newpass1')) > 0 ? MD5($this->input->post('newpass1')) : null;
        $fname = $this->input->post('fname');
        $lname = $this->input->post('lname');
        $rootvis = $this->input->post('rootmap');

        $this->load->model('User', '', true);
        echo($this->User->update($pass, $newpass, $fname, $lname, $rootvis));
    }

    public function pengaturan()
    {
        if ($this->session->userdata('user') === false) {
            $passvar['components']['bah'] = $this->load->view('com_restrict', null, true);
        } else {
            $this->load->model('User', '', true);
            $account = $this->User->credentials($this->session->userdata('user'));
            $statistics = $this->User->statistics($this->session->userdata('user'));
            $passvar['components']['pengaturan'] = $this->load->view('com_settings', array_merge($account, $statistics), true);
        }
        $this->load->view('_skeleton', $passvar);
    }

    public function bantuan()
    {
        $passvar = array();
        if ($this->session->userdata('user') !== false) {
            $passvar['components']['help'] = $this->load->view('com_help', null, true);
        } else {
            $passvar['components']['bah'] = $this->load->view('com_restrict', null, true);
        }
        // Load everything
        $this->load->view('_skeleton', $passvar);
    }

    // Cari sisa kuota doang
    public function sisa()
    {
        if ($this->session->userdata('user') === false) {
            echo '???';

            return;
        }
        $this->load->model('User', '', true);
        $tmp = $this->User->statistics($this->session->userdata('user'));
        echo $tmp['remaining'].'//'.$tmp['remaining_raw'];
    }
}
