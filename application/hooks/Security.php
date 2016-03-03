<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function validate_cookies()
{
    $CI = &get_instance();
    if ($CI->input->cookie('bakarmedia_login_user')) {
        $u = $CI->input->cookie('bakarmedia_login_user');
        $k = $CI->input->cookie('bakarmedia_login_key');
        if ($k == false) {
            clear_cookies();

            return;
        }

        $CI->load->database();
        $q = $CI->db->query('SELECT password FROM user WHERE email=?', array($u));

        if ($q->num_rows() < 1) {
            clear_cookies();

            return;
        }

        if ($k != MD5($u.'-bakarmedia_key-'.$q->row()->password)) {
            echo $k.'//'.MD5($u.'-bakarmedia_key-'.$q->row()->password);
            clear_cookies();
        }
    }
}

function clear_cookies()
{
    $CI = &get_instance();
    $cookie = array(
        'name' => 'login_user',
        'value' => '',
        'expire' => time() + 1,
        'prefix' => 'bakarmedia_',
    );
    $CI->input->set_cookie($cookie);

    $cookie = array(
        'name' => 'login_key',
        'value' => '',
        'expire' => time() + 1,
        'prefix' => 'bakarmedia_',
    );
    $CI->input->set_cookie($cookie);
}
