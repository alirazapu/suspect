<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Personprofile Controller
 *
 * Mirrors the dramslive `/personprofile/person_profile/` route including all
 * AJAX update/delete endpoints used by the tabbed profile page.
 *
 * Routes:
 *   GET  /personprofile/person_profile?id=<encrypted_pid>   — main profile page
 *   POST /personprofile/update_basic_info                   — save basic info
 *   POST /personprofile/update_detail_info                  — save detailed info
 *   POST /personprofile/get_district                        — cascade: districts for region
 *   POST /personprofile/get_police_station                  — cascade: police stations for district
 *   POST /personprofile/get_sect                            — cascade: sects for religion
 *   POST /personprofile/update_education                    — save/insert education record
 *   POST /personprofile/delete_education                    — delete education record
 *   POST /personprofile/update_identity                     — save/insert identity record
 *   POST /personprofile/delete_identity                     — delete identity record
 *   POST /personprofile/update_banks                        — save/insert bank record
 *   POST /personprofile/delete_bank                         — delete bank record
 *   POST /personprofile/update_personassets                 — save/insert asset record
 *   POST /personprofile/delete_asset                        — delete asset record
 *   POST /personprofile/update_mobiles                      — save/insert mobile number
 *   POST /personprofile/update_relations                    — save/insert relation
 *   POST /personprofile/update_criminalr                    — save/insert criminal record
 *   POST /personprofile/delete_criminalrecord               — delete criminal record
 *   POST /personprofile/update_affiliations                 — save/insert affiliation
 *   POST /personprofile/delete_affiliations                 — delete affiliation
 *   POST /personprofile/update_trainings                    — save/insert training
 *   POST /personprofile/delete_training                     — delete training record
 *   POST /personprofile/update_personincomesource           — save/insert income source
 *   POST /personprofile/deletesource                        — delete income source
 *   POST /personprofile/update_personreports                — save/insert person report
 *   POST /personprofile/deletereport                        — delete person report
 */
class Personprofile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Person_model');
        // Auth is enforced globally by the Sso_token hook (application/hooks/Sso_token.php).
    }

    // ------------------------------------------------------------------
    // Helper: return JSON response (used by AJAX write endpoints)
    // ------------------------------------------------------------------

    private function _json($data, $status_code = 200)
    {
        $this->output
             ->set_status_header($status_code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }

    private function _ok($message = 'Saved successfully.', $extra = array())
    {
        $this->_json(array_merge(array('status' => 'ok', 'message' => $message), $extra));
    }

    private function _error($message = 'An error occurred.', $code = 400)
    {
        $this->_json(array('status' => 'error', 'message' => $message), $code);
    }

    /**
     * Get the logged-in user_id from session (set by Auth / Sso_token hook).
     */
    private function _user_id()
    {
        return (int) $this->session->userdata('user_id');
    }

    /**
     * Get and validate a person_id from POST/GET.
     * Accepts either 'id' (encrypted) or 'pid' (plain integer — only used internally).
     *
     * @return int|false
     */
    private function _get_pid_from_post()
    {
        $raw = $this->input->post('pid', TRUE) ?: $this->input->post('id', TRUE);
        $pid = filter_var($raw, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        return ($pid !== FALSE && $pid !== NULL) ? (int) $pid : FALSE;
    }

    // ==================================================================
    // Main profile page (read-only, tabbed view)
    // ==================================================================

    public function person_profile()
    {
        $encrypted_id = $this->input->get('id', TRUE);

        if (empty($encrypted_id)) {
            show_error('Person ID is required.', 400);
        }

        // Decrypt the person ID — matches dramslive AES-256-CBC scheme exactly.
        $person_id = $this->Person_model->decrypt_person_id($encrypted_id);

        if ( ! $person_id) {
            show_error('Invalid or expired person ID.', 400);
        }

        // Increment view count
        $this->db->where('person_id', $person_id)->set('view_count', 'view_count+1', FALSE)
                 ->update('person');

        // Load header block for the profile card
        $person = $this->Person_model->get_person_header($person_id);

        if ( ! $person) {
            show_404();
        }

        $name = trim(implode(' ', array_filter(array(
            $person->first_name ?? '',
            $person->middle_name ?? '',
            $person->last_name   ?? '',
        )))) ?: 'Unknown';

        $data = array(
            'page_title'   => 'Person Profile: ' . htmlspecialchars($name),
            'person'       => $person,
            'person_id'    => (int) $person_id,
            'encrypted_id' => $encrypted_id,
        );

        $this->load->view('layout/header', $data);
        $this->load->view('personprofile/person_profile', $data);
        $this->load->view('layout/footer', $data);
    }

    // ==================================================================
    // Cascade dropdown AJAX helpers
    // ==================================================================

    /**
     * POST /personprofile/get_district
     * Body: region_id
     * Returns JSON array of districts.
     */
    public function get_district()
    {
        $region_id = (int) $this->input->post('region_id', TRUE);
        if ( ! $region_id) { $this->_error('region_id required.'); return; }
        $districts = $this->Person_model->get_districts_by_region($region_id);
        $this->_json($districts);
    }

    /**
     * POST /personprofile/get_police_station
     * Body: district_id
     * Returns JSON array of police stations.
     */
    public function get_police_station()
    {
        $district_id = (int) $this->input->post('district_id', TRUE);
        if ( ! $district_id) { $this->_error('district_id required.'); return; }
        $stations = $this->Person_model->get_police_stations_by_district($district_id);
        $this->_json($stations);
    }

    /**
     * POST /personprofile/get_sect
     * Body: religion_id (optional)
     * Returns JSON array of sects.
     */
    public function get_sect()
    {
        $religion_id = (int) $this->input->post('religion_id', TRUE);
        $sects = $this->Person_model->get_sects_by_religion($religion_id ?: NULL);
        $this->_json($sects);
    }

    // ==================================================================
    // Basic info — update
    // ==================================================================

    /**
     * POST /personprofile/update_basic_info
     * Body: pid, first_name, last_name, middle_name, father_name, address,
     *       district_id, region_id, police_station_id
     */
    public function update_basic_info()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }

        $data = $this->input->post(NULL, TRUE);
        $ok   = $this->Person_model->update_basic_info($data, $pid);
        $ok ? $this->_ok('Basic info updated.') : $this->_error('No changes saved.');
    }

    // ==================================================================
    // Detailed info — update
    // ==================================================================

    /**
     * POST /personprofile/update_detail_info
     * Body: pid + all person_detail_info columns
     */
    public function update_detail_info()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }

        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->update_detail_info($data, $pid);
        $this->_ok('Detail info updated.');
    }

    // ==================================================================
    // Education
    // ==================================================================

    /**
     * POST /personprofile/update_education
     * Body: pid, id (optional), edu_type, degree_name, complete_year, institute_name, education_level
     */
    public function update_education()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_education($data, $pid);
        $this->_ok('Education record saved.');
    }

    /**
     * POST /personprofile/delete_education
     * Body: pid, id
     */
    public function delete_education()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_education($id, $pid)
            ? $this->_ok('Education record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Identities
    // ==================================================================

    /**
     * POST /personprofile/update_identity
     * Body: pid, id (optional), identity_id, identity_no
     */
    public function update_identity()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_identity($data, $pid);
        $this->_ok('Identity record saved.');
    }

    /**
     * POST /personprofile/delete_identity
     * Body: pid, id
     */
    public function delete_identity()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_identity($id, $pid)
            ? $this->_ok('Identity record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Banks
    // ==================================================================

    /**
     * POST /personprofile/update_banks
     * Body: pid, id (optional), account_number, atm_number, branch_name,
     *       is_internet_banking, bank_name, ban_bank
     */
    public function update_banks()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_bank($data, $pid);
        $this->_ok('Bank record saved.');
    }

    /**
     * POST /personprofile/delete_bank
     * Body: pid, id
     */
    public function delete_bank()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_bank($id, $pid)
            ? $this->_ok('Bank record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Assets
    // ==================================================================

    /**
     * POST /personprofile/update_personassets
     * Body: pid, id (optional), asset_name, details, moveable_immovable,
     *       since_year, asset_value, asset_acquired_how, asset_type
     */
    public function update_personassets()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_asset($data, $pid);
        $this->_ok('Asset record saved.');
    }

    /**
     * POST /personprofile/delete_asset
     * Body: pid, id
     */
    public function delete_asset()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_asset($id, $pid)
            ? $this->_ok('Asset record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Mobiles
    // ==================================================================

    /**
     * POST /personprofile/update_mobiles
     * Body: pid, id (optional), phone_number, imsi_number, sim_activated_at,
     *       sim_last_used_at, status, mnc, connection_type, contact_type, sim_owner
     */
    public function update_mobiles()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_mobile($data, $pid, $this->_user_id());
        $this->_ok('Mobile number saved.');
    }

    // ==================================================================
    // Relations
    // ==================================================================

    /**
     * POST /personprofile/update_relations
     * Body: pid, person_relation_type, relation_with, under_custodian
     */
    public function update_relations()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_relation($data, $pid, $this->_user_id());
        $this->_ok('Relation saved.');
    }

    // ==================================================================
    // Criminal records
    // ==================================================================

    /**
     * POST /personprofile/update_criminalr
     * Body: pid, id (optional), fir_number, fir_date, police_station_id,
     *       sections_applied, case_position, accused_position
     */
    public function update_criminalr()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_criminal($data, $pid, $this->_user_id());
        $this->_ok('Criminal record saved.');
    }

    /**
     * POST /personprofile/delete_criminalrecord
     * Body: pid, id
     */
    public function delete_criminalrecord()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_criminal($id, $pid)
            ? $this->_ok('Criminal record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Affiliations
    // ==================================================================

    /**
     * POST /personprofile/update_affiliations
     * Body: pid, id (optional), organization_id, ideological_stance, details,
     *       self_recruitment_details, is_trained, designation
     */
    public function update_affiliations()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_affiliation($data, $pid);
        $this->_ok('Affiliation saved.');
    }

    /**
     * POST /personprofile/delete_affiliations
     * Body: pid, id
     */
    public function delete_affiliations()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_affiliation($id, $pid)
            ? $this->_ok('Affiliation deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Trainings
    // ==================================================================

    /**
     * POST /personprofile/update_trainings
     * Body: pid, id (optional), organization_id, training_camp, training_site,
     *       training_type_id, training_duration, training_year, training_purpose,
     *       material_taught, other_details
     */
    public function update_trainings()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_training($data, $pid);
        $this->_ok('Training record saved.');
    }

    /**
     * POST /personprofile/delete_training
     * Body: pid, id
     */
    public function delete_training()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_training($id, $pid)
            ? $this->_ok('Training record deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Income sources
    // ==================================================================

    /**
     * POST /personprofile/update_personincomesource
     * Body: pid, id (optional), income_source_name, details, income_source_duration, income_amount
     */
    public function update_personincomesource()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_income($data, $pid);
        $this->_ok('Income source saved.');
    }

    /**
     * POST /personprofile/deletesource
     * Body: pid, id
     */
    public function deletesource()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_income($id, $pid)
            ? $this->_ok('Income source deleted.')
            : $this->_error('Record not found.', 404);
    }

    // ==================================================================
    // Reports
    // ==================================================================

    /**
     * POST /personprofile/update_personreports
     * Body: pid, id (optional), report_type, report_reference_no, report_date, report_details
     */
    public function update_personreports()
    {
        $pid = $this->_get_pid_from_post();
        if ( ! $pid) { $this->_error('Invalid person ID.'); return; }
        $data = $this->input->post(NULL, TRUE);
        $this->Person_model->insert_update_report($data, $pid);
        $this->_ok('Report saved.');
    }

    /**
     * POST /personprofile/deletereport
     * Body: pid, id
     */
    public function deletereport()
    {
        $pid = $this->_get_pid_from_post();
        $id  = (int) $this->input->post('id', TRUE);
        if ( ! $pid || ! $id) { $this->_error('Invalid parameters.'); return; }
        $this->Person_model->delete_report($id, $pid)
            ? $this->_ok('Report deleted.')
            : $this->_error('Record not found.', 404);
    }
}
