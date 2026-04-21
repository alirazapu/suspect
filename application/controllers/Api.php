<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api Controller
 *
 * JSON endpoints consumed by admin.js for the person-profile tabs.
 *
 * All endpoints return:
 *   { "status": "ok",    "data": … }   on success
 *   { "status": "error", "message": … } on failure
 *
 * Routes (defined in config/routes.php):
 *   GET /api/persons/:id/basic
 *   GET /api/persons/:id/detailed
 *   GET /api/persons/:id/identities
 *   GET /api/persons/:id/education
 *   GET /api/persons/:id/income
 *   GET /api/persons/:id/banks
 *   GET /api/persons/:id/assets
 *   GET /api/persons/:id/mobiles
 *   GET /api/persons/:id/relations
 *   GET /api/persons/:id/criminal
 *   GET /api/persons/:id/affiliations
 *   GET /api/persons/:id/projects
 *   GET /api/persons/:id/category_history
 *   GET /api/persons/:id/reports
 *   GET /api/persons/search?q=
 */
class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Person_model');

        // All API responses are JSON.
        $this->output->set_content_type('application/json');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function _ok($data)
    {
        $this->output->set_output(json_encode(array('status' => 'ok', 'data' => $data)));
    }

    private function _error($message, $code = 400)
    {
        $this->output->set_status_header($code);
        $this->output->set_output(json_encode(array('status' => 'error', 'message' => $message)));
    }

    /**
     * Validate and return a safe person ID from the URI segment.
     * Returns (int) person_id or FALSE on failure.
     */
    private function _get_person_id($segment = 3)
    {
        $id = (int) $this->uri->segment($segment);
        return ($id > 0) ? $id : FALSE;
    }

    // ------------------------------------------------------------------
    // Lookups — all reference data for front-end dropdowns
    // ------------------------------------------------------------------

    public function lookups()
    {
        try {
            $data = array(
                'regions'          => $this->Person_model->get_regions(),
                'religions'        => $this->Person_model->get_religions(),
                'sects'            => $this->Person_model->get_sects_by_religion(NULL),
                'castes'           => $this->Person_model->get_castes(),
                'marital_statuses' => $this->Person_model->get_marital_statuses(),
                'countries'        => $this->Person_model->get_countries(),
                'banks'            => $this->Person_model->get_bank_list(),
                'education_levels' => $this->Person_model->get_education_levels(),
                'identity_types'   => $this->Person_model->get_identity_types(),
                'relation_types'   => $this->Person_model->get_relation_types(),
                'organizations'    => $this->Person_model->get_organizations(),
            );
            $this->_ok($data);
        } catch (Exception $e) {
            log_message('error', 'Api::lookups – ' . $e->getMessage());
            $this->_error('Failed to load lookup data.');
        }
    }

    // ------------------------------------------------------------------
    // Tab endpoints
    // ------------------------------------------------------------------

    public function persons_basic($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_basic_info($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_basic pid=' . $pid . ' – ' . $e->getMessage());
            $this->_error('Failed to load basic info.');
        }
    }

    public function persons_detailed($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_detailed_info($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_detailed pid=' . $pid . ' – ' . $e->getMessage());
            $this->_error('Failed to load detailed info.');
        }
    }

    public function persons_identities($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_identities($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_identities pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_education($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_education($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_education pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_income($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_income($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_income pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_banks($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_banks($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_banks pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_assets($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_assets($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_assets pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_mobiles($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_mobiles($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_mobiles pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_relations($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_relations($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_relations pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_criminal($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_criminal_with_district($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_criminal pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_affiliations($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_affiliations($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_affiliations pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_trainings($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_trainings($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_trainings pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_projects($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_projects($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_projects pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_category_history($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_category_history($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_category_history pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    public function persons_reports($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        try {
            $this->_ok($this->Person_model->get_reports($pid));
        } catch (Exception $e) {
            log_message('error', 'Api::persons_reports pid=' . $pid . ' – ' . $e->getMessage());
            $this->_ok(array());
        }
    }

    // ------------------------------------------------------------------
    // Search / autocomplete
    // ------------------------------------------------------------------
    public function persons_search()
    {
        $q      = $this->input->get('q', TRUE);
        $limit  = min((int) $this->input->get('limit', TRUE) ?: 20, 100);
        $offset = (int) $this->input->get('offset', TRUE);

        if (empty($q)) { $this->_error('Query required.'); return; }

        $filters = array('q' => $q);
        $results = $this->Person_model->get_persons($filters, $limit, $offset);
        $total   = $this->Person_model->count_persons($filters);

        $this->_ok(array('results' => $results, 'total' => $total));
    }

    /**
     * GET /api/persons/:id/name_cnic
     * Returns { name, cnic } for a given person_id — used by the Relations form.
     */
    public function persons_name_cnic($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $row = $this->Person_model->get_person_name_cnic($pid);
        if ($row) {
            $this->_ok($row);
        } else {
            $this->_error('Person not found.', 404);
        }
    }
}
