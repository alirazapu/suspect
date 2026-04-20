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
    // Tab endpoints
    // ------------------------------------------------------------------

    public function persons_basic($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_basic_info($pid));
    }

    public function persons_detailed($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_detailed_info($pid));
    }

    public function persons_identities($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_identities($pid));
    }

    public function persons_education($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_education($pid));
    }

    public function persons_income($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_income($pid));
    }

    public function persons_banks($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_banks($pid));
    }

    public function persons_assets($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_assets($pid));
    }

    public function persons_mobiles($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_mobiles($pid));
    }

    public function persons_relations($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_relations($pid));
    }

    public function persons_criminal($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_criminal($pid));
    }

    public function persons_affiliations($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_affiliations($pid));
    }

    public function persons_trainings($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_trainings($pid));
    }

    public function persons_projects($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_projects($pid));
    }

    public function persons_category_history($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_category_history($pid));
    }

    public function persons_reports($person_id = NULL)
    {
        $pid = $person_id ? (int) $person_id : $this->_get_person_id(3);
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $this->_ok($this->Person_model->get_reports($pid));
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
}
