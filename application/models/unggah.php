<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class unggah extends CI_Model
{
    public function upload($file, $where = '.')
    {
        // Multiii!!!
        for ($i = 0; $i < sizeof($file['name']); ++$i) {
            // Cek apakah file tidak kosong (0 Byte)
            if ($file['size'][$i] > 1) {
                // Lalu cek apakah muat di kuota
                if ($this->check_rem($this->session->userdata('user'), $file['size'][$i]) == false) {
                    echo 'Kuota tidak cukup.';

                    return;
                }
                // Beri nama baru
                $this->load->model('Util');
                $new_name = $this->Util->new_name($file['name'][$i]);

                // Cek konflik
                $new_name = $this->Util->pacify($new_name, $where);

                // Potong nama agar tidak melebihi 256 huruf
                $new_name = substr($new_name, 0, 256);

                // Bikin kode
                $CODE = $this->Util->randoco();

                // Masukkin ke database
                $this->db->query('INSERT INTO `file` (`user`, `path`, `filename`, `filesize`, `code`, `uploaddate`) VALUES (?, ?, ?, ?, ?, NOW())',
                    array($this->session->userdata('user'), $where, $new_name, $file['size'][$i], $CODE)
                );

                // Buat direktori dan upload file
                mkdir($this->config->item('phys_path').$this->session->userdata('user').'/'.$CODE);
                move_uploaded_file($file['tmp_name'][$i], $this->config->item('phys_path').$this->session->userdata('user').'/'.$CODE.'/'.$new_name);

                echo 'Berkas '.$file['name'][$i].' berhasil diunggah. ';
            }
        }
    }

    // Cek sisa kuota
    public function check_rem($u, $f)
    {
        $this->load->model('User', '', true);
        $r = $this->User->statistics($u);

        return $f <= $r['remaining_raw'];
    }
}
