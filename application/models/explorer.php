<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class explorer extends CI_Model
{
    public function dir($key = null, $current = null)
    {
        // kalau user tidak login dan mencoba melihat dir tanpa key
        if ($this->session->userdata('user') === false and $key == null) {
            return;
        }

        // Kalau dengan key (lihat dir orang lain)
        if ($key != null) {
            // Cek visibilitas
            $q = $this->db->query('SELECT `user`, `visibility` FROM `folder` WHERE `key` = ?', array($key));
            if ($q->num_rows() < 1) {
                return 'GAADA';
            } else {
                if ($q->row()->visibility != 2) {
                    return 'NOVIS';
                } else {
                    $USER = $q->row()->user;
                }
            }
        } else {
            $USER = $this->session->userdata('user');
        }

        $return_data = array();

        // Kalau dir = root (dasar), maka ambil dir selain dasar
        // kalau dir bukan root, ambil root aja
        if ($current == '.') {
            $c = "!= '.'";
        } else {
            $c = "= '.'";
        }

        if ($key == null) {
            $q = $this->db->query("SELECT `path`, `key`, `visibility` FROM `folder` WHERE `user` = ? AND `path` {$c} ORDER BY `path` ASC", array($this->session->userdata('user')));
            $v = $q->result();

            foreach ($v as $folder) {
                $return_data['folder'] = $v;
            }
        }

        // Ambil file
        if ($key == null) { // Dir sendiri
            $q = $this->db->query('SELECT `user`, `code`, `path`, `filename`, `uploaddate` FROM `file` WHERE `user` = ? AND `path` = ? ORDER BY `filename` ASC', array($this->session->userdata('user'), $current));
        } else { // Dir orang
            $up = $this->db->query('SELECT `user`, `path` FROM `folder` WHERE `key` = ?', array($key));
            $q = $this->db->query('SELECT `user`, `code`, `path`, `filename`, `uploaddate` FROM `file` WHERE `user` = ? AND `path` = ? ORDER BY `filename` ASC', array($up->row()->user, $up->row()->path));
        }

        $v = $q->result();

        foreach ($v as $file) {
            // GET THEM FILESAIZS
            $this->load->model('Util');
            $file->filesaiz = $this->Util->pretty_filesize(filesize($this->config->item('phys_path').$file->user.'/'.$file->code.'/'.$file->filename));
            // OK
            $return_data['file'][] = $file;
        }

        $return_data['self'] = ($key == null);

        $this->load->model('User', '', true);
        $return_data['premium'] = ($this->User->level($USER) == 'p');

        return $return_data;
    }

    // Hapus file
    public function del_file($what)
    {
        // Cek apakah file ada di database
        $q = $this->db->query('SELECT `user`, `code`, `filename` FROM `file` WHERE `code` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        // Hapus file dan direktori berCODE
        unlink($this->config->item('phys_path').$q->row()->user.'/'.$q->row()->code.'/'.$q->row()->filename);
        rmdir($this->config->item('phys_path').$q->row()->user.'/'.$q->row()->code);
        // Hapus dari DB
        $this->db->query('DELETE FROM `file` WHERE `code` = ?', array($what));

        return 'Berhasil.';
    }

    // Ganti nama
    public function rename_file($what, $to)
    {
        // Cek apakah file ada di database
        $q = $this->db->query('SELECT `user`, `code`, `filename`, `path` FROM `file` WHERE `code` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        // Dapatkan nama baru
        $this->load->model('Util');
        $new_name = $this->Util->new_name($to);
        $new_name = $this->Util->pacify($new_name, $q->row()->path);

        // Ganti nama
        rename($this->config->item('phys_path').$q->row()->user.'/'.$q->row()->code.'/'.$q->row()->filename, $this->config->item('phys_path').$q->row()->user.'/'.$q->row()->code.'/'.$new_name);
        $this->db->query('UPDATE `file` SET `filename` = ? WHERE `code` = ? AND `user` = ?', array($new_name, $what, $this->session->userdata('user')));
    }

    // Pindahkan file
    public function move_file($what, $where)
    {
        // Cek apakah file ada
        $q = $this->db->query('SELECT `code`, `filename`, `user` FROM `file` WHERE `code` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        $filename = $q->row()->filename; // ambil dulu filenamenya
        $oldname = $filename; // ingat yang lama
        $user = $q->row()->user; // usernya juga

        // Cek apakah folder ada
        $q = $this->db->query('SELECT `path` FROM `folder` WHERE `user` = ? AND `path` = ?', array($this->session->userdata('user'), $where));
        if ($q->num_rows < 1) {
            // Jika tidak, buat yang baru
            $where = $this->new_folder($where, 1);
        }

        // Cek apakah namanya tubrukan
        $this->load->model('Util', '', true);
        $filename = $this->Util->pacify($filename, $where);

        // Pindahkan file
        $this->db->query('UPDATE `file` SET `filename` = ?, `path` = ? WHERE `code` = ?', array($filename, $where, $what));
        rename($this->config->item('phys_path').$user.'/'.$what.'/'.$oldname,
               $this->config->item('phys_path').$user.'/'.$what.'/'.$filename);

        return 'Berhasil.';
    }

    // Hapus folder
    public function del_folder($what)
    {
        // Cek apakah folder ada
        $q = $this->db->query('SELECT `user`, `path` FROM `folder` WHERE `key` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        // Hapus semua file di dalam folder itu
        $qq = $this->db->query('SELECT `code`, `filename` FROM `file` WHERE `user` = ? AND `path` = ?', array($q->row()->user, $q->row()->path));
        foreach ($qq->result() as $item) {
            unlink($this->config->item('phys_path').$q->row()->user.'/'.$item->code.'/'.$item->filename);
            rmdir($this->config->item('phys_path').$q->row()->user.'/'.$item->code);
            $this->db->query('DELETE FROM `file` WHERE `user` = ? AND `code` = ?', array($q->row()->user, $item->code));
        }
        // Hapus folder
        $this->db->query('DELETE FROM `folder` WHERE `key` = ?', array($what));

        return 'Jangan menyesal, ya.';
    }

    // Ganti visibilitas
    public function switch_folder($what)
    {
        // Cek apakah folder ada
        $q = $this->db->query('SELECT `visibility` FROM `folder` WHERE `key` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        // Ganti visibilitas
        $v = $q->row()->visibility;
        ++$v;
        if ($v >= 3) {
            $v = 0;
        }
        $this->db->query('UPDATE `folder` SET `visibility`=? WHERE `key` = ?', array($v, $what));
    }

    // Ganti nama folder
    public function rename_folder($what, $to)
    {
        // Cek apakah folder ada
        $q = $this->db->query('SELECT `path` FROM `folder` WHERE `key` = ?', array($what));
        if ($q->num_rows() < 1) {
            return 'Gagal.';
        }

        // Nama lama
        $old = $q->row()->path;
        $this->load->model('Util');
        $new_name = $this->Util->new_name($to);
        $new_name = $this->Util->pacify_folder($new_name);
        $new_name = substr($new_name, 0, 256); // Potong nama agar tidak melebihi 256 huruf

        // Update database
        $this->db->query('UPDATE `folder` SET `path` = ? WHERE `key` = ? AND `user` = ?', array($new_name, $what, $this->session->userdata('user')));
        $this->db->query('UPDATE `file` SET `path` = ? WHERE `path` = ? AND `user` = ?', array($new_name, $old, $this->session->userdata('user')));
    }

    // Dapatkan nama folder dari key, serta pemiliknya
    public function folder_name($key)
    {
        // Cek apakah folder ada
        $q = $this->db->query("SELECT f.`visibility`, f.`path`, CONCAT(u.`fname`, ' ', u.`lname`) AS `owner` FROM `folder` f INNER JOIN `user` u ON u.`email` = f.`user` WHERE `key` = ?", array($key));
        if ($q->num_rows() > 0) {
            if ($q->row()->visibility == 2) {
                // Kalau visibilitas publik
                return array('name' => $q->row()->path, 'owner' => $q->row()->owner);
            } else {
                // Kalau visibilitas tertutup atau pribadi
                return array('name' => '???', 'owner' => '???');
            }
        } else {
            return array('name' => '???', 'owner' => '???');
        }
    }

    // Buat folder baru
    public function new_folder($n, $t = 0)
    {
        $this->load->model('Util');
        $new_name = $this->Util->new_name($n);
        $new_name = $this->Util->pacify_folder($new_name);
        $new_name = substr($new_name, 0, 256); // Potong nama agar tidak melebihi 256 huruf
        $KEY = 'm_'.substr($this->Util->randoco(), 0, 14);

        // Masukkan ke database
        $this->db->query('INSERT INTO `folder` (`user`, `path`, `key`, `visibility`) VALUES (?, ?, ?, ?)', array($this->session->userdata('user'), $new_name, $KEY, $t));

        return $new_name;
    }
}
