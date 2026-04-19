<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Person_model
 *
 * All database queries for the person-intelligence feature set.
 *
 * This model is shared by three controllers:
 *   - Persons        (listing + filtering)
 *   - Personprofile  (header block)
 *   - Api            (AJAX tab data)
 *
 * Database: aiesplus (shared with dramslive)
 *
 * Person ID encryption
 * ---------------------
 * Person IDs are transmitted as AES-256-CBC base64-url strings (the same
 * scheme used by the dramslive project).  The encryption key is read from
 * the application config `suspects.php` → `$config['pid_key']`.
 * Set that to the same value as `hash_key` in dramslive's auth.php.
 *
 * TODO: if the dramslive PID uses a different cipher/format, update
 *       decrypt_person_id() and encrypt_person_id() accordingly.
 */
class Person_model extends CI_Model
{
    // ----------------------------------------------------------------
    // Table names (aiesplus DB schema)
    // ----------------------------------------------------------------
    const T_PERSONS           = 'persons';
    const T_PERSON_DETAIL     = 'person_detail';
    const T_PERSON_IDENTITY   = 'person_identities';
    const T_PERSON_EDUCATION  = 'person_education';
    const T_PERSON_INCOME     = 'person_income_sources';
    const T_PERSON_BANKS      = 'person_bank_details';
    const T_PERSON_ASSETS     = 'person_asset_details';
    const T_PERSON_MOBILES    = 'person_mobiles';
    const T_PERSON_RELATIONS  = 'person_relations';
    const T_PERSON_CRIMINAL   = 'person_criminal_records';
    const T_PERSON_AFFILIATIONS = 'person_affiliations';
    const T_PERSON_PROJECTS   = 'person_projects';
    const T_CATEGORY_HISTORY  = 'person_category_history';
    const T_PERSON_REPORTS    = 'person_reports';

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
     *
     * @param  array  $filters  Associative array of filter values
     * @param  int    $limit
     * @param  int    $offset
     * @return array  Array of stdClass rows
     */
    public function get_persons(array $filters = array(), $limit = 25, $offset = 0)
    {
        $this->_apply_filters($filters);
        $this->db->order_by('p.name', 'ASC');
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
     * Apply filters to the active DB builder.
     */
    private function _apply_filters(array $filters)
    {
        // Full-text / general search
        if ( ! empty($filters['q'])) {
            $q = trim($filters['q']);
            $this->db->group_start()
                     ->like('p.name',        $q)
                     ->or_like('p.father_name', $q)
                     ->or_like('p.cnic',      $q)
                     ->group_end();
        }

        if ( ! empty($filters['gender']))
            $this->db->where('p.gender', $filters['gender']);

        if ( ! empty($filters['province']))
            $this->db->where('p.province', $filters['province']);

        if ( ! empty($filters['district']))
            $this->db->like('p.district', $filters['district']);

        if ( ! empty($filters['category']))
            $this->db->where('p.category', $filters['category']);

        if ( ! empty($filters['cnic']))
            $this->db->like('p.cnic', $filters['cnic']);

        // Default select — overridden to DISTINCT when a join adds duplicate rows
        $this->db->select('p.*');

        if ( ! empty($filters['mobile'])) {
            // Join mobiles table for mobile number search; use DISTINCT to avoid duplicates
            $this->db->join(self::T_PERSON_MOBILES . ' mob', 'mob.person_id = p.id', 'left');
            $this->db->like('mob.mobile_number', $filters['mobile']);
            $this->db->select('DISTINCT p.*');
        }

        if ( ! empty($filters['affiliation'])) {
            $this->db->join(self::T_PERSON_AFFILIATIONS . ' aff', 'aff.person_id = p.id', 'left');
            $this->db->like('aff.name', $filters['affiliation']);
        }

        if ( ! empty($filters['from_date']))
            $this->db->where('p.created_at >=', $filters['from_date']);

        if ( ! empty($filters['to_date']))
            $this->db->where('p.created_at <=', $filters['to_date'] . ' 23:59:59');
    }

    // ------------------------------------------------------------------
    // Filter option lists
    // ------------------------------------------------------------------

    public function get_provinces()
    {
        // TODO: if a provinces table exists in aiesplus, query it.
        // Fallback: hardcoded KPK / Pakistani provinces
        return array(
            'Khyber Pakhtunkhwa', 'Punjab', 'Sindh', 'Balochistan',
            'Azad Kashmir', 'Gilgit-Baltistan', 'Islamabad Capital Territory',
        );
    }

    public function get_categories()
    {
        $rows = $this->db
            ->select('DISTINCT category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->order_by('category', 'ASC')
            ->get(self::T_PERSONS)
            ->result_array();

        return array_column($rows, 'category');
    }

    // ==================================================================
    // Profile header
    // ==================================================================

    /**
     * Lightweight row for the profile page header card.
     */
    public function get_person_header($person_id)
    {
        return $this->db
            ->where('id', (int) $person_id)
            ->get(self::T_PERSONS)
            ->row();
    }

    // ==================================================================
    // Tab data
    // ==================================================================

    public function get_basic_info($person_id)
    {
        $row = $this->db
            ->where('id', (int) $person_id)
            ->get(self::T_PERSONS)
            ->row_array();
        return $row ?: null;
    }

    public function get_detailed_info($person_id)
    {
        // Try person_detail table; fall back to persons columns.
        $row = $this->db
            ->where('person_id', (int) $person_id)
            ->get(self::T_PERSON_DETAIL)
            ->row_array();

        if ( ! $row) {
            // Fall back to persons table extended columns (if they exist)
            $row = $this->db
                ->select('marital_status, spouse_name, children_count, occupation,
                          designation, organization, education_level, email, website, remarks')
                ->where('id', (int) $person_id)
                ->get(self::T_PERSONS)
                ->row_array();
        }

        return $row ?: null;
    }

    public function get_identities($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_IDENTITY, $person_id);
    }

    public function get_education($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_EDUCATION, $person_id);
    }

    public function get_income($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_INCOME, $person_id);
    }

    public function get_banks($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_BANKS, $person_id);
    }

    public function get_assets($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_ASSETS, $person_id);
    }

    public function get_mobiles($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_MOBILES, $person_id);
    }

    public function get_relations($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_RELATIONS, $person_id);
    }

    public function get_criminal($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_CRIMINAL, $person_id);
    }

    public function get_affiliations($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_AFFILIATIONS, $person_id);
    }

    public function get_projects($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_PROJECTS, $person_id);
    }

    public function get_category_history($person_id)
    {
        return $this->_tab_rows(self::T_CATEGORY_HISTORY, $person_id, 'changed_at', 'DESC');
    }

    public function get_reports($person_id)
    {
        return $this->_tab_rows(self::T_PERSON_REPORTS, $person_id, 'report_date', 'DESC');
    }

    // ------------------------------------------------------------------
    // Generic helper: fetch all rows linked by person_id
    // ------------------------------------------------------------------
    private function _tab_rows($table, $person_id, $order_by = 'id', $direction = 'ASC')
    {
        $rows = $this->db
            ->where('person_id', (int) $person_id)
            ->order_by($order_by, $direction)
            ->get($table)
            ->result_array();

        return $rows ?: array();
    }

    // ==================================================================
    // Person ID encryption / decryption
    // ==================================================================

    /**
     * Decrypt an encrypted person ID string (base64-url → AES-256-CBC).
     *
     * The encryption scheme mirrors dramslive:
     *   encrypted = base64url( AES-256-CBC( base64( pid ), key, iv ) )
     * where iv is the first 16 bytes of the cipher text.
     *
     * If decryption fails or the result is not numeric, returns FALSE.
     *
     * @param  string $encrypted_id  URL-safe base64 string
     * @return int|false
     */
    public function decrypt_person_id($encrypted_id)
    {
        if (empty($encrypted_id)) {
            return FALSE;
        }

        $key = $this->config->item('pid_key', 'suspects');

        // Fallback: if no key configured, only allow plain base64 in development.
        // In production (ENVIRONMENT !== 'development') refuse to decode without a key.
        if (empty($key)) {
            if (ENVIRONMENT === 'development') {
                log_message('error', 'Person_model: SUSPECT_PID_KEY is not set. Using insecure base64 fallback (development only).');
                $plain = base64_decode(strtr($encrypted_id, '-_', '+/'));
                if ($plain !== FALSE && ctype_digit(trim($plain))) {
                    return (int) trim($plain);
                }
            } else {
                log_message('error', 'Person_model: SUSPECT_PID_KEY is not configured. Cannot decrypt person ID in production.');
            }
            return FALSE;
        }

        try {
            // base64-url decode
            $decoded = base64_decode(strtr($encrypted_id, '-_', '+/'));
            if ($decoded === FALSE || strlen($decoded) < 17) {
                return FALSE;
            }

            // First 16 bytes = IV
            $iv   = substr($decoded, 0, 16);
            $data = substr($decoded, 16);

            $plain = openssl_decrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            if ($plain === FALSE) {
                return FALSE;
            }

            // dramslive wraps the ID in another base64 layer
            $inner = base64_decode($plain);
            $id    = ($inner !== FALSE) ? trim($inner) : trim($plain);

            return (ctype_digit($id) && $id > 0) ? (int) $id : FALSE;

        } catch (Exception $e) {
            log_message('error', 'Person_model::decrypt_person_id — ' . $e->getMessage());
            return FALSE;
        }
    }

    /**
     * Encrypt a person ID (integer) to the base64-url string used in URLs.
     *
     * @param  int $person_id
     * @return string
     */
    public function encrypt_person_id($person_id)
    {
        $key = $this->config->item('pid_key', 'suspects');

        if (empty($key)) {
            if (ENVIRONMENT === 'development') {
                // Development fallback: simple base64 (insecure — set SUSPECT_PID_KEY for production)
                log_message('error', 'Person_model: SUSPECT_PID_KEY is not set. Using insecure base64 fallback (development only).');
                return rtrim(strtr(base64_encode((string) $person_id), '+/', '-_'), '=');
            }
            // Production without a key: return an empty string to avoid exposing raw IDs
            log_message('error', 'Person_model: SUSPECT_PID_KEY is not configured. Cannot encrypt person ID in production.');
            return '';
        }

        $iv     = random_bytes(16);
        $inner  = base64_encode((string) $person_id);
        $cipher = openssl_encrypt($inner, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $raw    = $iv . $cipher;

        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
