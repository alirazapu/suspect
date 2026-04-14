<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persons extends Authenticated_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('pid');
        $this->load->model('Person_model');
    }

    // GET /persons  — listing with filters
    public function index() {
        $filters = [
            'name'        => $this->input->get('name',        TRUE),
            'cnic'        => $this->input->get('cnic',        TRUE),
            'mobile'      => $this->input->get('mobile',      TRUE),
            'category'    => $this->input->get('category',    TRUE),
            'district'    => $this->input->get('district',    TRUE),
        ];

        $page  = max(1, (int)$this->input->get('page'));
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $persons    = $this->Person_model->get_persons_list($filters, $limit, $offset);
        $total      = $this->Person_model->count_persons($filters);
        $categories = $this->Person_model->get_categories();
        $districts  = $this->Person_model->get_districts();

        $data = [
            'persons'    => $persons,
            'total'      => $total,
            'page'       => $page,
            'limit'      => $limit,
            'filters'    => $filters,
            'categories' => $categories,
            'districts'  => $districts,
            'user'       => $this->current_user,
        ];

        $this->load->view('layout/header', ['title' => 'Persons']);
        $this->load->view('layout/sidebar', ['user' => $this->current_user, 'active' => 'persons']);
        $this->load->view('persons/index', $data);
        $this->load->view('layout/footer');
    }

    // GET /persons/profile?id=<encrypted_pid>
    public function profile() {
        $enc_id = $this->input->get('id', TRUE);
        if (empty($enc_id)) {
            show_error('Person ID is required', 400);
            return;
        }

        $pid = (int) pid_decrypt($enc_id);
        if ($pid <= 0) {
            show_error('Invalid person ID', 400);
            return;
        }

        $person = $this->Person_model->get_person_basic($pid);
        if (!$person) {
            show_error('Person not found', 404);
            return;
        }

        $active_tab = $this->input->get('tab', TRUE) ?: 'basicinfo';

        $data = [
            'pid'        => $pid,
            'enc_id'     => $enc_id,
            'person'     => $person,
            'active_tab' => $active_tab,
            'user'       => $this->current_user,
        ];

        $this->load->view('layout/header', ['title' => $person->first_name . ' ' . $person->last_name . ' — Profile']);
        $this->load->view('layout/sidebar', ['user' => $this->current_user, 'active' => 'persons']);
        $this->load->view('persons/profile', $data);
        $this->load->view('layout/footer');
    }
}
