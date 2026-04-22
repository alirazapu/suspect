<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Persons Controller
 *
 * Provides:
 *   GET  /persons           — person listing with advanced filters
 *   GET  /persons/index     — same
 *   GET  /persons/profile   — redirect to PersonProfile (kept for backwards compat)
 */
class Persons extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Person_model');
    }

    // ------------------------------------------------------------------
    // Listing page
    // ------------------------------------------------------------------
    public function index()
    {
        $filters = array(
            'gender'      => $this->input->get('gender',      TRUE),
            'province'    => $this->input->get('province',    TRUE),
            'district'    => $this->input->get('district',    TRUE),
            'category'    => $this->input->get('category',    TRUE),
            'cnic'        => $this->input->get('cnic',        TRUE),
            'mobile'      => $this->input->get('mobile',      TRUE),
            'affiliation' => $this->input->get('affiliation', TRUE),
            'from_date'   => $this->input->get('from_date',   TRUE),
            'to_date'     => $this->input->get('to_date',     TRUE),
        );

        $page   = max(1, (int) $this->input->get('page'));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $total   = $this->Person_model->count_persons($filters);
        $persons = $this->Person_model->get_persons($filters, $limit, $offset);

        $data = array(
            'page_title'   => 'Persons',
            'filters'      => $filters,
            'persons'      => $persons,
            'total'        => $total,
            'page'         => $page,
            'limit'        => $limit,
            'total_pages'  => ceil($total / $limit),
            'provinces'    => $this->Person_model->get_provinces(),
            'categories'   => $this->Person_model->get_categories(),
        );

        $this->load->view('layout/header', $data);
        $this->load->view('persons/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Legacy redirect: /persons/profile?id=... → /personprofile/person_profile?id=...
     */
    public function profile()
    {
        $id = $this->input->get('id', TRUE);
        redirect('personprofile/person_profile' . ($id ? '?id=' . urlencode($id) : ''));
    }
}
