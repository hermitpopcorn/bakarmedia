<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class berkas extends CI_Controller
{
    // -------------
    // Public Access
    // -------------
    public function unduh($id)
    {
        $this->load->model('Unduh', '', true);

        $passvar = array('components' => array());
        // Prep download (masukkin IP & Code ke slot)
        $file = $this->Unduh->prep_download($id);
        // Cek file juga
        if (gettype($file) != 'string') {
            $passvar['components']['filedownload'] = $this->load->view('com_filedownload', array('filedata' => $file), true);
            $passvar['title'] = $file['filename'];
        } else {
            $passvar['components']['filefail'] = $this->load->view('com_filefail', null, true);
            $passvar['title'] = 'Berkas tidak ditemukan';
        }

        // LOAD EVERYTHING
        $this->load->view('_skeleton', $passvar);
    }

    // Halaman untuk browse dir orang
    public function map($where = '')
    {
        if ($where === false or strlen($where) < 1) {
            header('Location: '.base_url());
        }
        $this->load->model('Explorer', '', true);
        $title = $this->Explorer->folder_name($where);
        $passvar['components']['dirlist'] = $this->load->view('com_listing', array('rumah' => 'rumah_orang', 'key' => $where, 'owner' => $title['owner'], 'name' => ($title['name'] == '.' ? 'dasar' : $title['name']), 'title' => 'Map '.($title['name'] == '.' ? 'dasar' : $title['name'])), true);
        $this->load->view('_skeleton', $passvar);
    }

    public function lapor($code = '')
    {
        if ($code == '' && $this->input->post('code') !== false && $this->input->post('type') !== false && $this->input->post('desc') !== false) {
            // Sedang melapor
            $this->load->model('Administration', '', true);
            $this->Administration->report($_SERVER['REMOTE_ADDR'], $this->input->post('code'), $this->input->post('type'), $this->input->post('desc'));

            $passvar['components']['laporform'] = $this->load->view('com_report', array('msg' => 'proses', 'code' => ''), true);
            $this->load->view('_skeleton', $passvar);
        } else {
            // Ingin melapor
            $passvar['components']['laporform'] = $this->load->view('com_report', array('code' => $code), true);
            $this->load->view('_skeleton', $passvar);
        }
    }

    // -------------
    // AJAX Return / Boneless
    // -------------
    public function hijau($id)
    {
        $this->load->model('Unduh', '', true);

        // Status Green
        echo $this->Unduh->green_download($id) ? 'siap' : null;
    }

    public function ambil($id)
    {
        $this->load->model('Unduh', '', true);

        // Yaudah DL aja
        $this->Unduh->download($id);
    }

    // Browse dir sendiri
    public function rumah_sendiri()
    {
        $current = $this->input->post('target');
        if ($current === false or strlen($current) < 1) {
            return;
        }

        $this->load->model('Explorer', '', true);
        $print = $this->Explorer->dir(null, $current);
        $this->load->model('User', '', true);
        $tmp = $this->User->statistics($this->session->userdata('user'));
        $print['remaining'] = $tmp['remaining'];

        $this->load->view('com_diritem', $print);
    }

    // Browse dir orang lain
    public function rumah_orang()
    {
        $key = $this->input->post('target');
        if ($key === false or strlen($key) < 1) {
            return;
        }

        $this->load->model('Explorer', '', true);

        $print = $this->Explorer->dir($key, null);

        if (gettype($print) == 'string') {
            $print = array();
            $print['error'] = array('Map ini tidak bisa diakses.');
        }

        $this->load->view('com_diritem', $print);
    }

    public function unggah()
    {
        $where = $this->input->post('target');

        if (sizeof($_FILES['file']['name']) > 0 && $this->session->userdata('user') !== false && strlen($where) >= 1) {
            $this->load->model('Unggah', null, true);
            $this->Unggah->upload($_FILES['file'], $where);
        }
    }

    public function hapus_berkas()
    {
        $what = $this->input->post('target');
        if ($what === false or strlen($what) < 1) {
            return;
        }

        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->del_file($what));
        } else {
            echo 'Gagal.';
        }
    }

    public function ganti_nama_berkas()
    {
        $what = $this->input->post('target');
        if ($what === false or strlen($what) < 1) {
            return;
        }

        if ($this->input->post('nama_baru') === false or strlen($this->input->post('nama_baru')) < 1) {
            return 'Gagal.';
        }
        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->rename_file($what, $this->input->post('nama_baru')));
        } else {
            echo 'Gagal.';
        }
    }

    public function berkas_pindah()
    {
        $what = $this->input->post('target');
        $where = $this->input->post('map_baru');
        if ($what === false or strlen($what) < 1 or $where === false or strlen($where) < 1) {
            return;
        }

        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->move_file($what, $where));
        } else {
            echo 'Gagal.';
        }
    }

    public function hapus_map()
    {
        $what = $this->input->post('target');
        if ($what === false or strlen($what) < 1) {
            return;
        }

        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->del_folder($what));
        } else {
            echo 'Gagal.';
        }
    }

    public function ganti_map()
    {
        $what = $this->input->post('target');
        if ($what === false or strlen($what) < 1) {
            return;
        }

        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            $this->Explorer->switch_folder($what);
        } else {
            echo 'Gagal.';
        }
    }

    public function ganti_nama_map()
    {
        $what = $this->input->post('target');
        if ($what === false or strlen($what) < 1) {
            return;
        }

        if ($this->input->post('nama_baru') === false or strlen($this->input->post('nama_baru')) < 1) {
            return 'Gagal.';
        }
        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->rename_folder($what, $this->input->post('nama_baru')));
        } else {
            echo 'Gagal.';
        }
    }

    public function map_baru()
    {
        $n = $this->input->post('nama_map');
        $t = $this->input->post('tipe_map');

        if ($n === false or strlen($n) < 1) {
            return 'Gagal.';
        }
        if ($this->session->userdata('user') !== false) {
            $this->load->model('Explorer', null, true);
            echo($this->Explorer->new_folder($n, $t));
        } else {
            echo 'Gagal.';
        }
    }
}
