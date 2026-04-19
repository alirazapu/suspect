<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Personprofile Controller
 *
 * Mirrors the dramslive `/personprofile/person_profile/` route.
 * Displays the tabbed person-profile page.  Tab data is loaded on-demand
 * via AJAX calls to the Api controller (see application/controllers/Api.php).
 *
 * Route:  GET /personprofile/person_profile?id=<encrypted_pid>
 *         (identical URL format to ctd.drams.com)
 */
class Personprofile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Person_model');
    }

    // ------------------------------------------------------------------
    // Main profile page (tabbed view)
    // ------------------------------------------------------------------
    public function person_profile()
    {
        $encrypted_id = $this->input->get('id', TRUE);

        if (empty($encrypted_id)) {
            show_error('Person ID is required.', 400);
        }

        // Decrypt the person ID (AES-256-CBC, same algorithm as dramslive).
        $person_id = $this->Person_model->decrypt_person_id($encrypted_id);

        if ( ! $person_id) {
            show_error('Invalid or expired person ID.', 400);
        }

        // Load basic info for the profile header (name, CNIC, category …)
        $person = $this->Person_model->get_person_header($person_id);

        if ( ! $person) {
            show_404();
        }

        $data = array(
            'page_title'    => 'Person Profile: ' . htmlspecialchars($person->name ?? 'Unknown'),
            'person'        => $person,
            'person_id'     => (int) $person_id,
            'encrypted_id'  => $encrypted_id,
        );

        $this->load->view('layout/header', $data);
        $this->load->view('personprofile/person_profile', $data);
        $this->load->view('layout/footer', $data);
    }
}
