<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Person_model
 *
 * All database queries for the person-intelligence feature set.
 *
 * Shared by: Persons (listing), Personprofile (header + writes), Api (AJAX tab reads).
 *
 * Person ID encryption matches the dramslive Helpers_Utilities::encrypted_key() scheme:
 *   cipher:     AES-256-CBC
 *   key:        sha256('Irfan love CTD')      — 64-char hex, used directly (OpenSSL takes first 32 bytes)
 *   iv:         substr(sha256('SEStoPakistan'), 0, 16)
 *   encrypt:    base64( openssl_encrypt($id, ..., flags=0, ...) )   [flags=0 → openssl base64-encodes internally]
 *   decrypt:    openssl_decrypt(base64_decode($encrypted), ..., flags=0, ...)
 */
class Person_model extends CI_Model
{
    // ----------------------------------------------------------------
    // Table names — must match the aiesplus database schema exactly
    // ----------------------------------------------------------------
    const T_PERSONS             = 'person';
    const T_PERSON_INITIATE     = 'person_initiate';
    const T_PERSON_DETAIL       = 'person_detail_info';
    const T_PERSON_IDENTITY     = 'person_identities';
    const T_PERSON_EDUCATION    = 'person_education';
    const T_PERSON_INCOME       = 'person_income_sources';
    const T_PERSON_BANKS        = 'person_banks';
    const T_PERSON_ASSETS       = 'person_assets';
    const T_PERSON_MOBILES      = 'person_phone_number';
    const T_PERSON_RELATIONS    = 'person_relations';
    const T_PERSON_CRIMINAL     = 'person_criminal_record';
    const T_PERSON_AFFILIATIONS = 'person_affiliations';
    const T_PERSON_TRAININGS    = 'person_trainings';
    const T_PERSON_PROJECTS     = 'person_linked_projects';
    const T_CATEGORY_HISTORY    = 'person_category_history';
    const T_PERSON_CATEGORY     = 'person_category';
    const T_PERSON_REPORTS      = 'person_reports';
    const T_NADRA_PROFILE       = 'person_nadra_profile';
    const T_PERSON_PICTURES     = 'person_pictures';

    // Lookup / reference tables
    const T_LU_BANKS            = 'lu_banks';
    const T_LU_IDENTITY         = 'lu_identity';
    const T_LU_EDUCATION        = 'lu_education_level';
    const T_LU_RELATION         = 'lu_relation_type';
    const T_LU_RELIGION         = 'lu_religion';
    const T_LU_SECT             = 'lu_sect';
    const T_LU_CASTE            = 'lu_caste';
    const T_LU_COUNTRY          = 'lu_country';
    const T_LU_MARITAL          = 'lu_marital_status';
    const T_LU_TRAINING_CAMP    = 'lu_training_camp';
    const T_LU_ORG_STANCE       = 'lu_organization_stance';
    const T_LU_ORG_DESIGNATION  = 'lu_organization_designation';
    const T_ORGANIZATIONS       = 'banned_organizations';
    const T_MOBILE_COMPANIES    = 'mobile_companies';
    const T_POLICE_STATIONS     = 'police_stations';
    const T_DISTRICT            = 'district';
    const T_REGION              = 'region';
    const T_INT_PROJECTS        = 'int_projects';
    const T_USERS               = 'users';

    // Category labels (matches person_category.category_id)
    private static $CATEGORY_LABELS = array(0 => 'White', 1 => 'Grey', 2 => 'Black');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('suspects', TRUE);
    }

    // ==================================================================
    // Person listing / search
    // ==================================================================

    /**
     * Fetch a paginated list of persons with optional filters.
     */
    public function get_persons(array $filters = array(), $limit = 25, $offset = 0)
    {
        $this->_apply_filters($filters);
        $this->db->order_by('p.first_name', 'ASC');
        $this->db->limit($limit, $offset);
        $result = $this->db->get(self::T_PERSONS . ' p')->result();
        return $result ? $result : array();
    }

    /**
     * Count persons matching the given filters (for pagination).
     */
    public function count_persons(array $filters = array())
    {
        $this->_apply_filters($filters);
        return $this->db->from(self::T_PERSONS . ' p')->count_all_results();
    }

    /**
     * Build the query for listing/counting based on applied filters.
     */
    private function _apply_filters(array $filters)
    {
        // Base select: core person columns + computed full name + province/district names
        $this->db->select(
            "p.person_id, p.first_name, p.last_name, p.middle_name, p.father_name,
             p.address, p.district_id, p.region_id, p.image_url, p.is_complete,
             CONCAT(COALESCE(p.first_name,''),' ',COALESCE(p.middle_name,''),' ',COALESCE(p.last_name,'')) AS name,
             pi.cnic_number AS cnic,
             pdi.gender,
             r.name AS province,
             d.name AS district,
             (SELECT pc2.category_id FROM person_category pc2 WHERE pc2.person_id = p.person_id ORDER BY pc2.added_on DESC LIMIT 1) AS category_id"
        );
        $this->db->join(self::T_PERSON_INITIATE . ' pi',  'pi.person_id = p.person_id',   'left');
        $this->db->join(self::T_PERSON_DETAIL   . ' pdi', 'pdi.person_id = p.person_id',  'left');
        $this->db->join(self::T_REGION          . ' r',   'r.region_id = p.region_id',    'left');
        $this->db->join(self::T_DISTRICT        . ' d',   'd.district_id = p.district_id','left');
        $this->db->where('p.is_deleted', 0);

        // Name / father-name quick search
        if ( ! empty($filters['q'])) {
            $q = trim($filters['q']);
            $this->db->group_start()
                     ->like('p.first_name',  $q)
                     ->or_like('p.last_name',    $q)
                     ->or_like('p.middle_name',  $q)
                     ->or_like('p.father_name',  $q)
                     ->group_end();
        }

        // Gender — stored as integer (1=Male 2=Female 3=Other)
        if ( ! empty($filters['gender'])) {
            $this->db->where('pdi.gender', (int) $filters['gender']);
        }

        // Province — matched against region name
        if ( ! empty($filters['province'])) {
            $this->db->like('r.name', $filters['province'], 'both');
        }

        // District — matched against district name
        if ( ! empty($filters['district'])) {
            $this->db->like('d.name', $filters['district'], 'both');
        }

        // Category — filter on the aliased correlated-subquery result via HAVING
        if (isset($filters['category']) && $filters['category'] !== '') {
            $this->db->having('category_id', (int) $filters['category']);
        }

        // CNIC
        if ( ! empty($filters['cnic'])) {
            $this->db->like('pi.cnic_number', $filters['cnic']);
        }

        // Mobile number — join phone table only when filter is active
        if ( ! empty($filters['mobile'])) {
            $this->db->join(self::T_PERSON_MOBILES . ' mob', 'mob.person_id = p.person_id', 'inner');
            $this->db->like('mob.phone_number', $filters['mobile']);
        }

        // Affiliation — INNER join so only persons with at least one affiliation are returned
        if ( ! empty($filters['affiliation'])) {
            $this->db->join(self::T_PERSON_AFFILIATIONS . ' aff', 'aff.person_id = p.person_id', 'inner');
        }

        // Date range — based on the date the person's most-recent category was assigned
        if ( ! empty($filters['from_date'])) {
            $this->db->where(
                "(SELECT MIN(pc3.added_on) FROM person_category pc3 WHERE pc3.person_id = p.person_id) >=",
                $filters['from_date'], FALSE
            );
        }
        if ( ! empty($filters['to_date'])) {
            $this->db->where(
                "(SELECT MIN(pc3.added_on) FROM person_category pc3 WHERE pc3.person_id = p.person_id) <=",
                $filters['to_date'], FALSE
            );
        }
    }

    // ------------------------------------------------------------------
    // Filter option lists
    // ------------------------------------------------------------------

    public function get_provinces()
    {
        return array(
            'Khyber Pakhtunkhwa', 'Punjab', 'Sindh', 'Balochistan',
            'Azad Kashmir', 'Gilgit-Baltistan', 'Islamabad Capital Territory',
        );
    }

    public function get_categories()
    {
        return array(
            array('id' => 0, 'label' => 'White'),
            array('id' => 1, 'label' => 'Grey'),
            array('id' => 2, 'label' => 'Black'),
        );
    }

    // ------------------------------------------------------------------
    // Lookup / cascade helpers (used by AJAX dropdowns in the profile page)
    // ------------------------------------------------------------------

    public function get_regions()
    {
        return $this->db->order_by('name', 'ASC')->get(self::T_REGION)->result_array();
    }

    public function get_districts_by_region($region_id)
    {
        return $this->db
            ->where('region_id', (int) $region_id)
            ->order_by('name', 'ASC')
            ->get(self::T_DISTRICT)
            ->result_array();
    }

    public function get_police_stations_by_district($district_id)
    {
        return $this->db
            ->where('district_id', (int) $district_id)
            ->order_by('ps_name', 'ASC')
            ->get(self::T_POLICE_STATIONS)
            ->result_array();
    }

    public function get_sects_by_religion($religion_id = NULL)
    {
        if ($religion_id) {
            $this->db->where('religion_id', (int) $religion_id);
        }
        return $this->db->order_by('sect', 'ASC')->get(self::T_LU_SECT)->result_array();
    }

    public function get_bank_list()
    {
        return $this->db->order_by('name', 'ASC')->get(self::T_LU_BANKS)->result_array();
    }

    public function get_identity_types()
    {
        return $this->db->get(self::T_LU_IDENTITY)->result_array();
    }

    public function get_education_levels()
    {
        return $this->db->get(self::T_LU_EDUCATION)->result_array();
    }

    public function get_relation_types()
    {
        return $this->db->order_by('relation_name', 'ASC')->get(self::T_LU_RELATION)->result_array();
    }

    public function get_religions()
    {
        return $this->db->order_by('religion', 'ASC')->get(self::T_LU_RELIGION)->result_array();
    }

    public function get_countries()
    {
        return $this->db->order_by('name', 'ASC')->get(self::T_LU_COUNTRY)->result_array();
    }

    public function get_marital_statuses()
    {
        return $this->db->get(self::T_LU_MARITAL)->result_array();
    }

    public function get_castes()
    {
        return $this->db->order_by('caste', 'ASC')->get(self::T_LU_CASTE)->result_array();
    }

    public function get_organizations()
    {
        return $this->db
            ->select('org_id, org_name')
            ->order_by('org_name', 'ASC')
            ->get(self::T_ORGANIZATIONS)
            ->result_array();
    }

    public function get_training_camps()
    {
        return $this->db
            ->select('id, training_camp')
            ->order_by('training_camp', 'ASC')
            ->get(self::T_LU_TRAINING_CAMP)
            ->result_array();
    }

    public function get_org_stances()
    {
        return $this->db
            ->select('id, organization_stance')
            ->order_by('id', 'ASC')
            ->get(self::T_LU_ORG_STANCE)
            ->result_array();
    }

    public function get_org_designations()
    {
        return $this->db
            ->select('id, organization_designation')
            ->order_by('id', 'ASC')
            ->get(self::T_LU_ORG_DESIGNATION)
            ->result_array();
    }

    /**
     * Quick person lookup — returns name + CNIC for a given person_id.
     * Used by the Relations tab to show the related person's name readonly.
     */
    public function get_person_name_cnic($person_id)
    {
        $row = $this->db
            ->select("p.person_id,
                      TRIM(CONCAT(COALESCE(p.first_name,''),' ',COALESCE(p.middle_name,''),' ',COALESCE(p.last_name,''))) AS name,
                      COALESCE(pi.cnic_number, pi.cnic_number_foreigner, '') AS cnic")
            ->join(self::T_PERSON_INITIATE . ' pi', 'pi.person_id = p.person_id', 'left')
            ->where('p.person_id', (int) $person_id)
            ->get(self::T_PERSONS . ' p')
            ->row_array();
        return $row ?: null;
    }

    /**
     * Get criminal records including district_id for the form cascade.
     */
    public function get_criminal_with_district($person_id)
    {
        $case_positions = array(
            1 => 'Under Investigation', 2 => 'Under Trial',
            3 => 'Convicted', 4 => 'Discharged',
        );
        $accused_positions = array(
            1 => 'Main Accused', 2 => 'Co-Accused', 3 => 'Absconder',
            4 => 'Suspect',      5 => 'Witness',    6 => 'Arrested',
        );

        $rows = $this->db
            ->select("pcr.id, pcr.person_id,
                      pcr.fir_number,
                      pcr.police_station_id,
                      COALESCE(ps.ps_name, '') AS police_station,
                      COALESCE(ps.district_id, 0) AS district_id,
                      COALESCE(d.name, '') AS district,
                      COALESCE(d.region_id, 0) AS region_id,
                      COALESCE(r.name, '') AS region,
                      pcr.fir_date AS case_date,
                      NULL AS offence,
                      pcr.sections_applied AS section,
                      pcr.case_position,
                      pcr.accused_position", FALSE)
            ->join(self::T_POLICE_STATIONS . ' ps', 'ps.ps_id = pcr.police_station_id', 'left')
            ->join(self::T_DISTRICT        . ' d',  'd.district_id = ps.district_id',   'left')
            ->join(self::T_REGION          . ' r',  'r.region_id = d.region_id',        'left')
            ->where('pcr.person_id', (int) $person_id)
            ->order_by('pcr.id', 'ASC')
            ->get(self::T_PERSON_CRIMINAL . ' pcr')
            ->result_array();

        foreach ($rows as &$r) {
            $r['status'] = isset($case_positions[$r['case_position']])
                ? $case_positions[$r['case_position']] : '';
            $r['accused_position_label'] = isset($accused_positions[$r['accused_position']])
                ? $accused_positions[$r['accused_position']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Full row for the profile header card — includes CNIC, category, photo URL.
     */
    public function get_person_header($person_id)
    {
        $pid = (int) $person_id;

        $row = $this->db
            ->select("p.person_id, p.first_name, p.last_name, p.middle_name, p.father_name,
                      p.address, p.district_id, p.region_id, p.police_station_id,
                      p.is_complete, p.view_access_level_id, p.edit_access_level_id,
                      CONCAT(COALESCE(p.first_name,''),' ',COALESCE(p.middle_name,''),' ',COALESCE(p.last_name,'')) AS name,
                      pi.cnic_number AS cnic,
                      pdi.gender, pdi.dob, pdi.religion AS religion_id, pdi.nationality AS nationality_id,
                      pnp.person_photo_url AS image_url,
                      (SELECT pc2.category_id FROM person_category pc2 WHERE pc2.person_id = p.person_id ORDER BY pc2.added_on DESC LIMIT 1) AS category_id")
            ->join(self::T_PERSON_INITIATE . ' pi',  'pi.person_id = p.person_id',  'left')
            ->join(self::T_PERSON_DETAIL   . ' pdi', 'pdi.person_id = p.person_id', 'left')
            ->join(self::T_NADRA_PROFILE   . ' pnp', 'pnp.person_id = p.person_id', 'left')
            ->where('p.person_id', $pid)
            ->get(self::T_PERSONS . ' p')
            ->row();

        if ($row) {
            $row->category = isset(self::$CATEGORY_LABELS[$row->category_id])
                ? self::$CATEGORY_LABELS[$row->category_id]
                : '';
        }

        return $row;
    }

    // ==================================================================
    // Tab data — read methods (consumed by Api controller)
    // ==================================================================

    /**
     * Basic info tab: joins person + person_initiate + person_detail_info + current category.
     */
    public function get_basic_info($person_id)
    {
        $pid = (int) $person_id;

        $row = $this->db
            ->select("p.person_id, p.first_name, p.last_name, p.middle_name, p.father_name, p.address,
                      p.district_id, p.region_id, p.police_station_id,
                      CONCAT(COALESCE(p.first_name,''),' ',COALESCE(p.middle_name,''),' ',COALESCE(p.last_name,'')) AS name,
                      pi.cnic_number AS cnic, pi.is_foreigner,
                      pdi.alias, pdi.dob, pdi.gender, pdi.marital_status, pdi.nationality,
                      pdi.religion, pdi.sect, pdi.caste, pdi.place_of_birth,
                      pdi.mother_tongue, pdi.language_read_write, pdi.language_speak, pdi.language_accent,
                      pdi.physical_appearance, pdi.other_details,
                      d.name AS district, r.name AS region,
                      lu_rel.religion AS religion_label,
                      lu_s.sect AS sect_label,
                      lu_c.caste AS caste_label,
                      lu_co.nicename AS nationality_label,
                      lu_ms.marital_status AS marital_status_label,
                      (SELECT pc2.category_id FROM person_category pc2 WHERE pc2.person_id = p.person_id ORDER BY pc2.added_on DESC LIMIT 1) AS category_id")
            ->join(self::T_PERSON_INITIATE . ' pi',  'pi.person_id = p.person_id',       'left')
            ->join(self::T_PERSON_DETAIL   . ' pdi', 'pdi.person_id = p.person_id',      'left')
            ->join(self::T_DISTRICT        . ' d',   'd.district_id = p.district_id',    'left')
            ->join(self::T_REGION          . ' r',   'r.region_id = p.region_id',        'left')
            ->join(self::T_LU_RELIGION     . ' lu_rel', 'lu_rel.id = pdi.religion',      'left')
            ->join(self::T_LU_SECT         . ' lu_s',   'lu_s.id = pdi.sect',            'left')
            ->join(self::T_LU_CASTE        . ' lu_c',   'lu_c.id = pdi.caste',           'left')
            ->join(self::T_LU_COUNTRY      . ' lu_co',  'lu_co.id = pdi.nationality',    'left')
            ->join(self::T_LU_MARITAL      . ' lu_ms',  'lu_ms.id = pdi.marital_status', 'left')
            ->where('p.person_id', $pid)
            ->get(self::T_PERSONS . ' p')
            ->row_array();

        if ($row) {
            $row['category'] = isset(self::$CATEGORY_LABELS[$row['category_id']])
                ? self::$CATEGORY_LABELS[$row['category_id']]
                : '';
            // Provide human-readable gender label
            $genders = array(1 => 'Male', 2 => 'Female', 3 => 'Other');
            $row['gender_label'] = isset($genders[$row['gender']]) ? $genders[$row['gender']] : '';
        }

        return $row ?: null;
    }

    /**
     * Detailed info tab: from person_detail_info with lookup labels.
     */
    public function get_detailed_info($person_id)
    {
        $pid = (int) $person_id;

        $row = $this->db
            ->select("pdi.*, pdi.marital_status AS marital_status_id,
                      lu_ms.marital_status AS marital_status,
                      lu_rel.religion AS religion_label,
                      lu_s.sect AS sect_label,
                      lu_c.caste AS caste_label,
                      lu_co.nicename AS nationality_label")
            ->join(self::T_LU_MARITAL  . ' lu_ms',  'lu_ms.id = pdi.marital_status', 'left')
            ->join(self::T_LU_RELIGION . ' lu_rel', 'lu_rel.id = pdi.religion',      'left')
            ->join(self::T_LU_SECT     . ' lu_s',   'lu_s.id = pdi.sect',            'left')
            ->join(self::T_LU_CASTE    . ' lu_c',   'lu_c.id = pdi.caste',           'left')
            ->join(self::T_LU_COUNTRY  . ' lu_co',  'lu_co.id = pdi.nationality',    'left')
            ->where('pdi.person_id', $pid)
            ->get(self::T_PERSON_DETAIL . ' pdi')
            ->row_array();

        return $row ?: null;
    }

    /**
     * Identities tab: joins lu_identity for type label.
     * admin.js keys: identity_type, identity_number, issue_date, expiry_date, issue_place, status
     */
    public function get_identities($person_id)
    {
        $rows = $this->db
            ->select("pi.id, pi.person_id,
                      pi.identity_id,
                      COALESCE(lui.identity, CONCAT('Type ', pi.identity_id)) AS identity_type,
                      pi.identity_no,
                      NULL AS issue_date,
                      NULL AS expiry_date,
                      NULL AS issue_place,
                      NULL AS status", FALSE)
            ->join(self::T_LU_IDENTITY . ' lui', 'lui.id = pi.identity_id', 'left')
            ->where('pi.person_id', (int) $person_id)
            ->order_by('pi.id', 'ASC')
            ->get(self::T_PERSON_IDENTITY . ' pi')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Education tab.
     * admin.js keys: degree, institution, board_university, passing_year, grade
     */
    public function get_education($person_id)
    {
        $rows = $this->db
            ->select("pe.id, pe.person_id, pe.edu_type,
                      pe.degree_name AS degree,
                      pe.institute_name AS institution,
                      NULL AS board_university,
                      pe.complete_year AS passing_year,
                      NULL AS grade,
                      pe.education_level AS education_level_id,
                      COALESCE(lue.education_level, '') AS education_level_label", FALSE)
            ->join(self::T_LU_EDUCATION . ' lue', 'lue.id = pe.education_level', 'left')
            ->where('pe.person_id', (int) $person_id)
            ->order_by('pe.id', 'ASC')
            ->get(self::T_PERSON_EDUCATION . ' pe')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Income sources tab.
     * admin.js keys: source_type, description, monthly_amount, annual_amount, remarks
     */
    public function get_income($person_id)
    {
        $durations = array(1 => 'Daily', 2 => 'Monthly', 3 => 'Yearly');
        $rows = $this->db
            ->select("pis.id, pis.person_id,
                      pis.income_source_name AS source_type,
                      pis.details AS description,
                      pis.income_amount AS monthly_amount,
                      NULL AS annual_amount,
                      pis.income_source_duration,
                      pis.file_link,
                      NULL AS remarks", FALSE)
            ->where('pis.person_id', (int) $person_id)
            ->order_by('pis.id', 'ASC')
            ->get(self::T_PERSON_INCOME . ' pis')
            ->result_array();

        foreach ($rows as &$r) {
            $r['duration_label'] = isset($durations[$r['income_source_duration']])
                ? $durations[$r['income_source_duration']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Banks tab.
     * admin.js keys: bank_name, branch, account_number, account_type, iban, balance
     */
    public function get_banks($person_id)
    {
        $rows = $this->db
            ->select("pb.id, pb.person_id,
                      pb.bank_name AS bank_id,
                      COALESCE(lb.name, '') AS bank_display,
                      pb.branch_name AS branch,
                      pb.account_number,
                      pb.atm_number,
                      pb.is_internet_banking,
                      pb.ban_bank,
                      NULL AS account_type,
                      NULL AS iban,
                      NULL AS balance", FALSE)
            ->join(self::T_LU_BANKS . ' lb', 'lb.id = pb.bank_name', 'left')
            ->where('pb.person_id', (int) $person_id)
            ->order_by('pb.id', 'ASC')
            ->get(self::T_PERSON_BANKS . ' pb')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Assets tab.
     * admin.js keys: asset_type, description, location, value, registered_name
     */
    public function get_assets($person_id)
    {
        $moveable = array(0 => 'Unknown', 1 => 'Moveable', 2 => 'Immovable');
        $rows = $this->db
            ->select("pa.id, pa.person_id,
                      pa.asset_name AS asset_type,
                      pa.details AS description,
                      NULL AS location,
                      pa.asset_value AS value,
                      NULL AS registered_name,
                      pa.moveable_immovable,
                      pa.since_year,
                      pa.asset_acquired_how,
                      pa.file_link", FALSE)
            ->where('pa.person_id', (int) $person_id)
            ->order_by('pa.id', 'ASC')
            ->get(self::T_PERSON_ASSETS . ' pa')
            ->result_array();

        foreach ($rows as &$r) {
            $r['moveable_label'] = isset($moveable[$r['moveable_immovable']])
                ? $moveable[$r['moveable_immovable']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Mobiles tab.
     * admin.js keys: mobile_number, operator, sim_owner, status, remarks
     */
    public function get_mobiles($person_id)
    {
        $rows = $this->db
            ->select("ppn.id, ppn.person_id,
                      ppn.phone_number AS mobile_number,
                      COALESCE(mc.company_name, CONCAT('MNC-',ppn.mnc)) AS operator,
                      ppn.sim_owner,
                      ppn.status,
                      ppn.connection_type,
                      ppn.contact_type,
                      ppn.sim_activated_at,
                      ppn.sim_last_used_at,
                      NULL AS remarks", FALSE)
            ->join(self::T_MOBILE_COMPANIES . ' mc', 'mc.mnc = ppn.mnc', 'left')
            ->where('ppn.person_id', (int) $person_id)
            ->order_by('ppn.id', 'ASC')
            ->get(self::T_PERSON_MOBILES . ' ppn')
            ->result_array();

        $contact_types = array(1 => 'Personal', 2 => 'WhatsApp', 3 => 'Official', 4 => 'Other');
        foreach ($rows as &$r) {
            $r['status_label'] = $r['status'] ? 'Active' : 'Inactive';
            $r['connection_type_label'] = $r['connection_type'] ? 'Pre-Paid' : 'Post-Paid';
            $r['contact_type_label'] = isset($contact_types[$r['contact_type']]) ? $contact_types[$r['contact_type']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Relations tab — bidirectional, matches dramslive behaviour.
     * Returns rows where this person is either the initiator OR the related-person.
     * admin.js keys: rel_from_id, rel_from_name, relation_type, rel_to_id, rel_to_name, cnic, under_custodian
     */
    public function get_relations($person_id)
    {
        $pid = (int) $person_id;

        // Use a raw UNION so we get both directions without cross-joining twice.
        // Left half: this person initiated the relation → "relation from" is THIS person, "relation with" is the other
        // Right half: another person added THIS person → "relation from" is the other person, "relation with" is THIS person
        $sql = "
            SELECT
                pr.person_id            AS rel_from_id,
                TRIM(CONCAT(COALESCE(fp.first_name,''),' ',COALESCE(fp.middle_name,''),' ',COALESCE(fp.last_name,'')))  AS rel_from_name,
                COALESCE(fpi.cnic_number, fpi.cnic_number_foreigner, '') AS rel_from_cnic,
                pr.person_relation_type AS relation_type_id,
                COALESCE(lrt.relation_name, '')                          AS relation_type,
                pr.relation_with        AS rel_to_id,
                TRIM(CONCAT(COALESCE(tp.first_name,''),' ',COALESCE(tp.middle_name,''),' ',COALESCE(tp.last_name,'')))  AS rel_to_name,
                COALESCE(tpi.cnic_number, tpi.cnic_number_foreigner, '') AS cnic,
                COALESCE(tco.nicename, '') AS country,
                pr.under_custodian
            FROM " . self::T_PERSON_RELATIONS . " pr
            LEFT JOIN " . self::T_LU_RELATION . "      lrt ON lrt.id   = pr.person_relation_type
            LEFT JOIN " . self::T_PERSONS     . "      fp  ON fp.person_id  = pr.person_id
            LEFT JOIN " . self::T_PERSON_INITIATE . "  fpi ON fpi.person_id = pr.person_id
            LEFT JOIN " . self::T_PERSONS     . "      tp  ON tp.person_id  = pr.relation_with
            LEFT JOIN " . self::T_PERSON_INITIATE . "  tpi ON tpi.person_id = pr.relation_with
            LEFT JOIN " . self::T_PERSON_DETAIL   . "  tpd ON tpd.person_id = pr.relation_with
            LEFT JOIN " . self::T_LU_COUNTRY      . "  tco ON tco.id        = tpd.nationality
            WHERE pr.person_id = {$pid} OR pr.relation_with = {$pid}
            ORDER BY pr.person_id ASC";

        $rows = $this->db->query($sql)->result_array();
        return $rows ?: array();
    }

    /**
     * Criminal record tab.
     * admin.js keys: fir_number, police_station, district, case_date, offence, section, status
     */
    public function get_criminal($person_id)
    {
        $case_positions = array(
            1 => 'Under Investigation', 2 => 'Under Trial',
            3 => 'Convicted', 4 => 'Discharged',
        );

        $rows = $this->db
            ->select("pcr.id, pcr.person_id,
                      pcr.fir_number,
                      COALESCE(ps.ps_name, '') AS police_station,
                      COALESCE(d.name, '') AS district,
                      pcr.fir_date AS case_date,
                      NULL AS offence,
                      pcr.sections_applied AS section,
                      pcr.case_position,
                      pcr.accused_position", FALSE)
            ->join(self::T_POLICE_STATIONS . ' ps', 'ps.ps_id = pcr.police_station_id', 'left')
            ->join(self::T_DISTRICT        . ' d',  'd.district_id = ps.district_id',   'left')
            ->where('pcr.person_id', (int) $person_id)
            ->order_by('pcr.id', 'ASC')
            ->get(self::T_PERSON_CRIMINAL . ' pcr')
            ->result_array();

        foreach ($rows as &$r) {
            $r['status'] = isset($case_positions[$r['case_position']])
                ? $case_positions[$r['case_position']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Affiliations tab (organization + ideological stance + designation).
     * admin.js keys: organization_name, ideological_stance, designation, is_trained, remarks
     */
    public function get_affiliations($person_id)
    {
        $rows = $this->db
            ->select("pa.id, pa.person_id, pa.organization_id,
                      COALESCE(bo.org_name, '') AS organization_name,
                      pa.ideological_stance AS ideological_stance_id,
                      COALESCE(los.organization_stance, '') AS ideological_stance,
                      pa.designation AS designation_id,
                      COALESCE(lod.organization_designation, '') AS designation,
                      pa.details AS remarks,
                      pa.self_recruitment_details, pa.is_trained,
                      NULL AS affiliation_type,
                      NULL AS name,
                      NULL AS role,
                      NULL AS from_date,
                      NULL AS to_date", FALSE)
            ->join(self::T_ORGANIZATIONS . ' bo', 'bo.org_id = pa.organization_id', 'left')
            ->join(self::T_LU_ORG_STANCE . ' los', 'los.id = pa.ideological_stance', 'left')
            ->join(self::T_LU_ORG_DESIGNATION . ' lod', 'lod.id = pa.designation', 'left')
            ->where('pa.person_id', (int) $person_id)
            ->order_by('pa.id', 'ASC')
            ->get(self::T_PERSON_AFFILIATIONS . ' pa')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Trainings tab — joined with banned_organizations to resolve org name.
     * admin.js keys: organization_name, training_camp, training_site, training_year,
     *                training_duration, training_purpose, material_taught, other_details
     */
    public function get_trainings($person_id)
    {
        $rows = $this->db
            ->select("pt.id, pt.person_id, pt.organization_id,
                      pt.training_camp AS training_camp_id,
                      COALESCE(ltc.training_camp, '') AS training_camp,
                      pt.training_site, pt.training_type_id, pt.training_duration,
                      pt.training_year, pt.training_purpose, pt.material_taught, pt.other_details,
                      COALESCE(bo.org_name, '') AS organization_name", FALSE)
            ->join(self::T_LU_TRAINING_CAMP . ' ltc', 'ltc.id = pt.training_camp', 'left')
            ->join(self::T_ORGANIZATIONS . ' bo', 'bo.org_id = pt.organization_id', 'left')
            ->where('pt.person_id', (int) $person_id)
            ->order_by('pt.id', 'ASC')
            ->get(self::T_PERSON_TRAININGS . ' pt')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Returns the distinct organizations a person is already affiliated with.
     * Used to populate the training form's organization dropdown.
     * Mirrors dramslive Model_Personprofile::get_person_affiliated_org().
     */
    public function get_affiliated_organizations($person_id)
    {
        $rows = $this->db
            ->select("pa.organization_id, COALESCE(bo.org_name, '') AS org_name", FALSE)
            ->join(self::T_ORGANIZATIONS . ' bo', 'bo.org_id = pa.organization_id', 'left')
            ->where('pa.person_id', (int) $person_id)
            ->group_by('pa.organization_id')
            ->order_by('bo.org_name', 'ASC')
            ->get(self::T_PERSON_AFFILIATIONS . ' pa')
            ->result_array();

        return $rows ?: array();
    }

    /**
     * Linked projects tab.
     * admin.js keys: project_name, project_type, role, start_date, end_date, status
     */
    public function get_projects($person_id)
    {
        $rows = $this->db
            ->select("plp.person_id, plp.project_id, plp.request_time,
                      COALESCE(ip.project_name, plp.project_id) AS project_name,
                      NULL AS project_type,
                      NULL AS role,
                      NULL AS start_date,
                      NULL AS end_date,
                      COALESCE(ip.project_status, 0) AS status_id", FALSE)
            ->join(self::T_INT_PROJECTS . ' ip', 'ip.id = plp.project_id', 'left')
            ->where('plp.person_id', (int) $person_id)
            ->group_by('plp.project_id')
            ->get(self::T_PERSON_PROJECTS . ' plp')
            ->result_array();

        foreach ($rows as &$r) {
            $r['status'] = $r['status_id'] == 1 ? 'Closed' : 'Open';
        }

        return $rows ?: array();
    }

    /**
     * Category change history tab.
     * admin.js keys: old_category, new_category, changed_by, changed_at, reason
     * DB column: added_on (not changed_at)
     */
    public function get_category_history($person_id)
    {
        $rows = $this->db
            ->select("pch.person_id, pch.old_category_id, pch.new_category_id,
                      pch.added_on AS changed_at,
                      pch.reason,
                      COALESCE(u.username, CONCAT('User #', pch.user_id)) AS changed_by")
            ->join(self::T_USERS . ' u', 'u.id = pch.user_id', 'left')
            ->where('pch.person_id', (int) $person_id)
            ->order_by('pch.added_on', 'DESC')
            ->get(self::T_CATEGORY_HISTORY . ' pch')
            ->result_array();

        foreach ($rows as &$r) {
            $r['old_category'] = isset(self::$CATEGORY_LABELS[$r['old_category_id']])
                ? self::$CATEGORY_LABELS[$r['old_category_id']] : '';
            $r['new_category'] = isset(self::$CATEGORY_LABELS[$r['new_category_id']])
                ? self::$CATEGORY_LABELS[$r['new_category_id']] : '';
        }

        return $rows ?: array();
    }

    /**
     * Person reports tab.
     * admin.js keys: report_type, report_date, reported_by, summary, status
     */
    public function get_reports($person_id)
    {
        $report_types = array(
            1 => 'Interrogation Report', 2 => 'Investigation Report', 3 => 'Special Report',
            4 => 'Intelligence Report',  5 => 'Ground Check Report',  6 => 'FIR Copy',
            7 => 'Recommendations/Remarks', 8 => 'Other',
        );

        $rows = $this->db
            ->select("pr.id, pr.person_id, pr.report_type,
                      pr.report_reference_no,
                      pr.report_date,
                      pr.report_details AS summary,
                      pr.file_link,
                      NULL AS reported_by,
                      NULL AS status", FALSE)
            ->where('pr.person_id', (int) $person_id)
            ->order_by('pr.report_date', 'DESC')
            ->get(self::T_PERSON_REPORTS . ' pr')
            ->result_array();

        foreach ($rows as &$r) {
            $r['report_type_label'] = isset($report_types[$r['report_type']])
                ? $report_types[$r['report_type']] : 'Other';
        }

        return $rows ?: array();
    }

    // ==================================================================
    // Write methods — update / insert / delete (used by Personprofile controller)
    // ==================================================================

    /**
     * Update the core person row (basic info tab).
     */
    public function update_basic_info($data, $person_id)
    {
        $allowed = array('first_name', 'last_name', 'middle_name', 'father_name', 'address',
                         'district_id', 'region_id', 'police_station_id');
        $update  = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { return FALSE; }

        $this->db->where('person_id', (int) $person_id)->update(self::T_PERSONS, $update);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Update/insert the person_detail_info row (detailed info tab).
     */
    public function update_detail_info($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('alias', 'dob', 'religion', 'marital_status', 'temporary_address',
                         'police_station_id', 'district_id', 'region_id', 'physical_appearance',
                         'place_of_birth', 'sect', 'caste', 'gender', 'nationality',
                         'is_sensitive_department', 'mother_tongue', 'language_read_write',
                         'language_speak', 'language_accent', 'other_details');
        $update  = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { return FALSE; }

        $exists = $this->db->where('person_id', $pid)->count_all_results(self::T_PERSON_DETAIL);
        if ($exists) {
            $this->db->where('person_id', $pid)->update(self::T_PERSON_DETAIL, $update);
        } else {
            $update['person_id'] = $pid;
            $this->db->insert(self::T_PERSON_DETAIL, $update);
        }
        return TRUE;
    }

    // ----- Education -----

    public function insert_update_education($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'edu_type', 'degree_name', 'complete_year',
                         'institute_name', 'education_level');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_EDUCATION, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_EDUCATION, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete_education($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_EDUCATION);
        return $this->db->affected_rows() > 0;
    }

    // ----- Identities -----

    public function insert_update_identity($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'identity_id', 'identity_no');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_IDENTITY, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_IDENTITY, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete_identity($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_IDENTITY);
        return $this->db->affected_rows() > 0;
    }

    // ----- Banks -----

    public function insert_update_bank($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'account_number', 'atm_number', 'branch_name',
                         'is_internet_banking', 'bank_name', 'ban_bank');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_BANKS, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_BANKS, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete_bank($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_BANKS);
        return $this->db->affected_rows() > 0;
    }

    // ----- Assets -----

    public function insert_update_asset($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'asset_name', 'details', 'moveable_immovable',
                         'since_year', 'asset_value', 'asset_acquired_how', 'asset_type', 'file_link');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_ASSETS, $row);
            return (int) $data['id'];
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_ASSETS, $row);
            return (int) $this->db->insert_id();
        }
    }

    public function delete_asset($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_ASSETS);
        return $this->db->affected_rows() > 0;
    }

    // ----- Mobiles -----

    public function insert_update_mobile($data, $person_id, $user_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'phone_number', 'imsi_number', 'sim_activated_at', 'sim_last_used_at',
                         'status', 'mnc', 'connection_type', 'contact_type', 'sim_owner');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;
        $row['user_id']   = (int) $user_id;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_MOBILES, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_MOBILES, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    // ----- Relations -----

    public function insert_update_relation($data, $person_id, $user_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('person_relation_type', 'relation_with', 'under_custodian');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;
        $row['user_id']   = (int) $user_id;

        // Relations table has no PK — check for existing row first
        $exists = $this->db
            ->where('person_id', $pid)
            ->where('relation_with', (int)($data['relation_with'] ?? 0))
            ->count_all_results(self::T_PERSON_RELATIONS);

        if ($exists) {
            $this->db->where('person_id', $pid)
                     ->where('relation_with', (int)$data['relation_with'])
                     ->update(self::T_PERSON_RELATIONS, $row);
        } else {
            $this->db->insert(self::T_PERSON_RELATIONS, $row);
        }
        return TRUE;
    }

    // ----- Criminal records -----

    public function insert_update_criminal($data, $person_id, $user_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'fir_number', 'fir_date', 'police_station_id',
                         'sections_applied', 'case_position', 'accused_position');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;
        $row['user_id']   = (int) $user_id;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_CRIMINAL, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_CRIMINAL, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete_criminal($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_CRIMINAL);
        return $this->db->affected_rows() > 0;
    }

    // ----- Affiliations -----

    public function insert_update_affiliation($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'organization_id', 'ideological_stance', 'details',
                         'self_recruitment_details', 'is_trained', 'designation');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_AFFILIATIONS, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_AFFILIATIONS, $row);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete_affiliation($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_AFFILIATIONS);
        return $this->db->affected_rows() > 0;
    }

    // ----- Trainings -----

    public function insert_update_training($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'organization_id', 'training_camp', 'training_site',
                         'training_type_id', 'training_duration', 'training_year',
                         'training_purpose', 'material_taught', 'other_details');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_TRAININGS, $row);
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_TRAININGS, $row);
        }

        // Mirror dramslive: mark the affiliated org as trained
        if ( ! empty($data['organization_id'])) {
            $this->db->where('person_id', $pid)
                     ->where('organization_id', (int)$data['organization_id'])
                     ->update(self::T_PERSON_AFFILIATIONS, array('is_trained' => 1));
        }

        return $this->db->affected_rows() >= 0;
    }

    public function delete_training($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_TRAININGS);
        return $this->db->affected_rows() > 0;
    }

    // ----- Income sources -----

    public function insert_update_income($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'income_source_name', 'details', 'income_source_duration', 'income_amount', 'file_link');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_INCOME, $row);
            return (int) $data['id'];
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_INCOME, $row);
            return (int) $this->db->insert_id();
        }
    }

    public function delete_income($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_INCOME);
        return $this->db->affected_rows() > 0;
    }

    // ----- Reports -----

    public function insert_update_report($data, $person_id)
    {
        $pid     = (int) $person_id;
        $allowed = array('id', 'report_type', 'report_reference_no', 'report_date', 'report_details', 'file_link');
        $row     = array_intersect_key($data, array_flip($allowed));
        $row['person_id'] = $pid;

        if ( ! empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->where('person_id', $pid)
                     ->update(self::T_PERSON_REPORTS, $row);
            return (int) $data['id'];
        } else {
            unset($row['id']);
            $this->db->insert(self::T_PERSON_REPORTS, $row);
            return (int) $this->db->insert_id();
        }
    }

    public function delete_report($id, $person_id)
    {
        $this->db->where('id', (int)$id)->where('person_id', (int)$person_id)
                 ->delete(self::T_PERSON_REPORTS);
        return $this->db->affected_rows() > 0;
    }

    // ==================================================================
    // Person ID encryption / decryption — matches dramslive exactly
    //
    // dramslive Helpers_Utilities::encrypted_key($uID, 'encrypt'):
    //   $key = hash('sha256', $secret_key);
    //   $iv  = substr(hash('sha256', $secret_iv), 0, 16);
    //   $out = openssl_encrypt($uID, 'AES-256-CBC', $key, 0, $iv);
    //   return base64_encode($out);
    //
    // The secret_key and secret_iv default to the values shipped in
    // dramslive's source.  Override via environment variables:
    //   SUSPECT_PID_SECRET_KEY  (default: 'Irfan love CTD')
    //   SUSPECT_PID_SECRET_IV   (default: 'SEStoPakistan')
    // ==================================================================

    private static function _pid_key_and_iv()
    {
        $secret_key = getenv('SUSPECT_PID_SECRET_KEY') ?: 'Irfan love CTD';
        $secret_iv  = getenv('SUSPECT_PID_SECRET_IV')  ?: 'SEStoPakistan';
        $key = hash('sha256', $secret_key);
        $iv  = substr(hash('sha256', $secret_iv), 0, 16);
        return array($key, $iv);
    }

    /**
     * Decrypt an encrypted person ID string coming from dramslive URLs.
     *
     * @param  string $encrypted_id  The ?id= value (base64 string from dramslive)
     * @return int|false             Integer person ID, or FALSE on failure
     */
    public function decrypt_person_id($encrypted_id)
    {
        if (empty($encrypted_id)) {
            return FALSE;
        }

        try {
            list($key, $iv) = self::_pid_key_and_iv();

            // dramslive stores: base64_encode(openssl_encrypt($pid, ..., flags=0, ...))
            // So we reverse: openssl_decrypt(base64_decode($encrypted), ..., flags=0, ...)
            $plain = openssl_decrypt(base64_decode($encrypted_id), 'AES-256-CBC', $key, 0, $iv);

            if ($plain === FALSE) {
                log_message('error', 'Person_model::decrypt_person_id – openssl_decrypt failed for id=' . substr($encrypted_id, 0, 30) . '...');
                return FALSE;
            }

            $id = trim($plain);
            return (ctype_digit($id) && (int)$id > 0) ? (int)$id : FALSE;

        } catch (Exception $e) {
            log_message('error', 'Person_model::decrypt_person_id – ' . $e->getMessage());
            return FALSE;
        }
    }

    /**
     * Encrypt a person ID to the same format dramslive generates for URLs.
     *
     * @param  int $person_id
     * @return string  base64-encoded encrypted string
     */
    public function encrypt_person_id($person_id)
    {
        list($key, $iv) = self::_pid_key_and_iv();
        // flags=0: openssl returns base64-encoded ciphertext internally
        $out = openssl_encrypt((string)(int)$person_id, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($out);
    }
}
