<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected $current_user = null;

    public function __construct() {
        parent::__construct();
        $this->current_user = $this->session->userdata('suspect_user');
    }
}

class Authenticated_Controller extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->current_user) {
            redirect('auth/login');
        }
    }
}
