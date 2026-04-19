<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Welcome (Dashboard) Controller
 *
 * Default landing page after login.
 */
class Welcome extends CI_Controller
{
    public function index()
    {
        $data = array('page_title' => 'Dashboard');
        $this->load->view('layout/header', $data);
        $this->load->view('welcome_message', $data);
        $this->load->view('layout/footer', $data);
    }
}
