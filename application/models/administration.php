<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class administration extends CI_Model
{
    public function get_users($p = 0, $filter = '')
    {
        $perpage = 10;
        $qstring = 'SELECT `email`, `fname`, `lname`, ROUND(`limit` / 1024 / 1024) as `limit`, `level` FROM `user` ORDER BY `email` ASC LIMIT '.$p * $perpage.', '.$perpage;
        $qqstring = 'SELECT COUNT(*) AS `total` FROM `user`';
        if (strlen($filter) > 0) {
            $filter = '%'.$filter.'%';
            $qstring = 'SELECT `email`, `fname`, `lname`, ROUND(`limit` / 1024 / 1024) as `limit`, `level` FROM `user` WHERE `email` LIKE ? ORDER BY `email` ASC LIMIT '.$p * $perpage.', '.$perpage;
            $qqstring = 'SELECT COUNT(*) AS `total` FROM `user` WHERE `email` LIKE ?';
        }

        $q = $this->db->query($qstring, array($filter));
        $qq = $this->db->query($qqstring, array($filter));

        return array('result' => $q->result_array(), 'pages' => ceil($qq->row()->total / $perpage));
    }

    public function update_userdata($who, $quota, $level)
    {
        $this->db->query('UPDATE `user` SET `limit` = ?, `level` = ? WHERE `email` = ?', array($quota * 1024 * 1024, $level, $who));

        return 'Berhasil diubah.';
    }

    public function report($sender, $file, $type, $desc)
    {
        // Cek keberadaan file
        $q = $this->db->query('SELECT `code` FROM `file` WHERE `code` = ?', array($file));
        if ($q->num_rows() < 1) {
            return false;
        }

        $this->db->query('INSERT IGNORE INTO `report` (`sender`, `file`, `type`, `desc`) VALUES (?, ?, ?, ?)', array($sender, $file, $type, $desc));
    }
}
