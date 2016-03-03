<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class kebijakan extends CI_Controller
{
    public function ketentuan()
    {
        // Set body components
        $body_components = array('components' => array(
            'home' => $this->load->view('com_tos', null, true),
        ));

        // Load everything
        $this->load->view('_skeleton', $body_components);
    }

    public function privasi()
    {
        // Set body components
        $body_components = array('components' => array(
            'home' => $this->load->view('com_pripol', null, true),
        ));

        // Load everything
        $this->load->view('_skeleton', $body_components);
    }
}
