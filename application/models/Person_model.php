<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Person_model extends CI_Model {

    public function get_persons_list($filters = [], $limit = 25, $offset = 0) {
        $this->db->select('p.id, p.first_name, p.last_name, p.father_name, p.cnic,
                           p.dob, p.gender, p.district, p.category_id,
                           c.name AS category_name')
                 ->from('persons p')
                 ->join('person_categories c', 'c.id = p.category_id', 'left');

        $this->_apply_filters($filters);
        $this->db->limit($limit, $offset);
        $this->db->order_by('p.id', 'DESC');
        return $this->db->get()->result();
    }

    public function count_persons($filters = []) {
        $this->db->select('COUNT(*) as cnt')
                 ->from('persons p');
        $this->_apply_filters($filters);
        $row = $this->db->get()->row();
        return $row ? (int)$row->cnt : 0;
    }

    private function _apply_filters($filters) {
        if (!empty($filters['name'])) {
            $name = $this->db->escape_like_str($filters['name']);
            $this->db->group_start()
                     ->like('p.first_name', $name)
                     ->or_like('p.last_name', $name)
                     ->or_like('p.father_name', $name)
                     ->group_end();
        }
        if (!empty($filters['cnic']))     $this->db->like('p.cnic', $filters['cnic']);
        if (!empty($filters['category'])) $this->db->where('p.category_id', (int)$filters['category']);
        if (!empty($filters['district'])) $this->db->where('p.district', $filters['district']);
    }

    public function get_person_basic($pid) {
        return $this->db->where('id', (int)$pid)->get('persons')->row();
    }

    public function get_person_detailed($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_details')->result();
    }

    public function get_person_identities($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_cnic')->result();
    }

    public function get_person_education($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_education')->result();
    }

    public function get_person_income($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_income_sources')->result();
    }

    public function get_person_banks($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_bank_accounts')->result();
    }

    public function get_person_assets($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_assets')->result();
    }

    public function get_person_mobiles($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_mobiles')->result();
    }

    public function get_person_relations($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_relations')->result();
    }

    public function get_person_criminal($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_criminal_records')->result();
    }

    public function get_person_affiliations($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_affiliations')->result();
    }

    public function get_person_projects($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_projects')->result();
    }

    public function get_person_category_history($pid) {
        return $this->db->where('person_id', (int)$pid)
                        ->order_by('created_at', 'DESC')
                        ->get('person_category_history')->result();
    }

    public function get_person_reports($pid) {
        return $this->db->where('person_id', (int)$pid)->get('person_reports')->result();
    }

    public function get_categories() {
        return $this->db->order_by('name')->get('person_categories')->result();
    }

    public function get_districts() {
        return $this->db->select('DISTINCT district')->where('district IS NOT NULL', NULL, FALSE)
                        ->order_by('district')->get('persons')->result();
    }

    public function search_persons($q) {
        if (strlen($q) < 2) return [];
        $like = $this->db->escape_like_str($q);
        return $this->db->select('id, first_name, last_name, cnic')
                        ->group_start()
                        ->like('first_name', $like)->or_like('last_name', $like)->or_like('cnic', $like)
                        ->group_end()
                        ->limit(20)->get('persons')->result();
    }
}
