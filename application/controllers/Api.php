<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Authenticated_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Person_model');
        $this->output->set_content_type('application/json');
    }

    private function _pid($segment = 3) {
        return (int) $this->uri->segment($segment);
    }

    public function persons_basic($pid = 0)      { $this->_json($this->Person_model->get_person_basic($pid ?: $this->_pid())); }
    public function persons_detailed($pid = 0)   { $this->_json($this->Person_model->get_person_detailed($pid ?: $this->_pid())); }
    public function persons_identities($pid = 0) { $this->_json($this->Person_model->get_person_identities($pid ?: $this->_pid())); }
    public function persons_education($pid = 0)  { $this->_json($this->Person_model->get_person_education($pid ?: $this->_pid())); }
    public function persons_income($pid = 0)     { $this->_json($this->Person_model->get_person_income($pid ?: $this->_pid())); }
    public function persons_banks($pid = 0)      { $this->_json($this->Person_model->get_person_banks($pid ?: $this->_pid())); }
    public function persons_assets($pid = 0)     { $this->_json($this->Person_model->get_person_assets($pid ?: $this->_pid())); }
    public function persons_mobiles($pid = 0)    { $this->_json($this->Person_model->get_person_mobiles($pid ?: $this->_pid())); }
    public function persons_relations($pid = 0)  { $this->_json($this->Person_model->get_person_relations($pid ?: $this->_pid())); }
    public function persons_criminal($pid = 0)   { $this->_json($this->Person_model->get_person_criminal($pid ?: $this->_pid())); }
    public function persons_affiliations($pid=0) { $this->_json($this->Person_model->get_person_affiliations($pid ?: $this->_pid())); }
    public function persons_projects($pid = 0)   { $this->_json($this->Person_model->get_person_projects($pid ?: $this->_pid())); }
    public function persons_category_history($pid=0){ $this->_json($this->Person_model->get_person_category_history($pid ?: $this->_pid())); }
    public function persons_reports($pid = 0)    { $this->_json($this->Person_model->get_person_reports($pid ?: $this->_pid())); }

    public function persons_search() {
        $q = $this->input->get('q', TRUE);
        $this->_json($this->Person_model->search_persons($q));
    }

    private function _json($data) {
        echo json_encode(['status' => 'success', 'data' => $data]);
    }
}
