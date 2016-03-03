<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class util extends CI_Model
{
    // Ngehash password
    // CUMA buat password, URL folder masih pakai MD5
    public function hash()
    {
        if (func_num_args() < 1) {
            return '';
        }

        $salt = $this->config->item('pass_salt');

        $hashed = '';
        foreach (func_get_args() as $input) {
            $hashed .= hash('sha256', $input.$salt);
        }

        return hash('sha256', $hashed);
    }

    // Ngecantikin filesize
    public function pretty_filesize($i)
    {
        if ($i < 1024) {
            return $i.' B';
        } elseif ($i > (1024 * 1024 * 1024)) {
            return round($i / (1024 * 1024 * 1024), 1).' GB';
        } elseif ($i > (1024 * 1024)) {
            return round($i / (1024 * 1024), 1).' MB';
        } elseif ($i > 1024) {
            return round($i / 1024, 1).' KB';
        }
    }

    // Ngebenerin filename
    public function new_name($n)
    {
        return preg_replace("/[^A-Za-z0-9()[].,_-\s]/", '_', $n);
    }

    // Anti konflik
    public function pacify($n, $p)
    {
        $this->load->database();
        $q = $this->db->query('SELECT `code` FROM `file` WHERE `user` = ? AND `path` = ? AND `filename` = ?', array($this->session->userdata('user'), $p, $n));
        while ($q->num_rows() > 0) {
            $f = explode('.', $n);
            $f[sizeof($f) - 2] .= '_BARU';
            $n = implode('.', $f);

            $q = $this->db->query('SELECT `code` FROM `file` WHERE `user` = ? AND `path` = ? AND `filename` = ?', array($this->session->userdata('user'), $p, $n));
        }

        return $n;
    }

    // Anti konflik buat para folder
    public function pacify_folder($p)
    {
        $this->load->database();
        $q = $this->db->query('SELECT `path` FROM `folder` WHERE `user` = ? AND `path` = ?', array($this->session->userdata('user'), $p));
        while ($q->num_rows() > 0) {
            $p .= '_BARU';

            $q = $this->db->query('SELECT `path` FROM `folder` WHERE `user` = ? AND `path` = ?', array($this->session->userdata('user'), $p));
        }

        return $p;
    }

    // Random code
    public function randoco()
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345678901234567890'), 0, 16).substr(md5(time()), 16);
    }
}
