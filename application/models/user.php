<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class user extends CI_Model
{
    public function logon($email, $pass, $remember)
    {
        if (strlen($email) <= 0 or strlen($pass) <= 0) {
            return 'Alamat surel atau kata sandi kosong.';
        }

        $this->load->model('Util'); // Buat ngehash

        $q = $this->db->query('SELECT `email`, `fname`, `lname` FROM `user` WHERE `email` = ? AND `password` = ?', array($email, $this->Util->hash($email, $pass)));

        if ($q->num_rows() < 1) {
            return 'Alamat surel atau kata sandi salah.';
        }

        $this->session->sess_expire_on_close = true;
        if ($remember == 'on') {
            $this->session->sess_expire_on_close = false;
        }

        $this->session->set_userdata('user', $q->row()->email);
        $this->session->set_userdata('name', $q->row()->fname.' '.$q->row()->lname);

        return 'berhasil';
    }

    public function logout()
    {
        $this->session->sess_destroy();
        if (strpos($_SERVER['HTTP_REFERER'], '/pengguna/') === false) {
            header('Location: '.$_SERVER['HTTP_REFERER']);
        } else {
            header('Location: '.base_url());
        }
    }

    public function register($email, $pass, $fname, $lname)
    {
        $q = $this->db->query('SELECT `email` FROM `user` WHERE `email` = ?', array($email));
        if ($q->num_rows() > 0) {
            return 'Alamat surel sudah terdaftar sebelumnya.';
        }

        $this->load->model('Util'); // Buat ngehash

        mkdir($this->config->item('phys_path').$email);
        $this->db->query('INSERT INTO `user` (`email`, `password`, `fname`, `lname`) VALUES (?, ?, ?, ?)', array($email, $this->Util->hash($email, $pass), $fname, $lname));
        $this->db->query("INSERT INTO `folder` (`user`, `path`, `key`, `visibility`) VALUES (?, '.', ?, '1')",
            array($email, ('m_'.substr(MD5($this->session->userdata('user').'.'.time()), 0, 14))));
        $this->db->query("INSERT INTO `folder` (`user`, `path`, `key`, `visibility`) VALUES (?, 'publik', ?, '2')",
            array($email, ('m_'.substr(MD5($this->session->userdata('user').'publik'.time()), 0, 14))));
        $this->db->query("INSERT INTO `folder` (`user`, `path`, `key`, `visibility`) VALUES (?, 'tertutup', ?, '1')",
            array($email, ('m_'.substr(MD5($this->session->userdata('user').'tertutup'.time()), 0, 14))));
        $this->db->query("INSERT INTO `folder` (`user`, `path`, `key`, `visibility`) VALUES (?, 'pribadi', ?, '0')",
            array($email, ('m_'.substr(MD5($this->session->userdata('user').'pribadi'.time()), 0, 14))));

        $this->logon($email, $pass, false);

        return true;
    }

    public function update($pass, $newpass, $fname, $lname, $rootvis)
    {
        $u = $this->session->userdata('user');
        $q = $this->db->query('SELECT `password` FROM `user` WHERE `email` = ?', array($u));
        if ($pass != $q->row()->password) {
            return 'a';
        }
        if ($newpass !== null) {
            $this->db->query('UPDATE `user` SET `password` = ? WHERE `email` = ?', array($newpass, $u));
        }
        $this->db->query('UPDATE `user` SET `fname` = ?, `lname` = ? WHERE `email` = ?', array($fname, $lname, $u));
        $this->db->query("UPDATE `folder` SET `visibility` =? WHERE `path` = '.' AND `user` = ?", array($rootvis, $u));

        echo 'berhasil';
    }

    public function credentials($email)
    {
        $q1 = $this->db->query('SELECT `email`, `fname`, `lname` FROM `user` WHERE `email` = ?', array($email));
        $q2 = $this->db->query("SELECT `visibility`, `key` FROM `folder` WHERE `user` = ? AND `path` = '.'", array($email));
        $r1 = $q1->result_array();
        $r2 = $q2->result_array();

        return array_merge($r1[0], $r2[0]);
    }

    public function statistics($email)
    {
        $this->load->model('Util');
        $q = $this->db->query('SELECT IF(SUM(`filesize`) IS NOT NULL, SUM(`filesize`), 0) AS `total`, (SELECT `limit` FROM `user` WHERE `email` = ?) - IF(SUM(`filesize`) != NULL, SUM(`filesize`), 0) AS `remaining` FROM `file` WHERE `user` = ?', array($email, $email));

        return array('TotalSize' => $this->Util->pretty_filesize($q->row()->total), 'remaining' => $this->Util->pretty_filesize($q->row()->remaining), 'remaining_raw' => $q->row()->remaining);
    }

    public function level($who = null)
    {
        if ($who === null) {
            $who = $this->session->userdata('user');
        }

        return $this->db->query('SELECT `level` FROM `user` WHERE `email` = ?', array($who))->row()->level;
    }
}
