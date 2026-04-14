<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }

    public function getPersonsList(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        $builder = $this->db->table('persons p')
            ->select('p.id, p.first_name, p.last_name, p.father_name, p.cnic,
                      p.dob, p.gender, p.district, p.category_id,
                      c.name AS category_name')
            ->join('person_categories c', 'c.id = p.category_id', 'left');

        $this->_applyFilters($builder, $filters);

        return $builder->orderBy('p.id', 'DESC')->limit($limit, $offset)->get()->getResultObject();
    }

    public function countPersons(array $filters = []): int
    {
        $builder = $this->db->table('persons p')->select('COUNT(*) as cnt');
        $this->_applyFilters($builder, $filters);
        $row = $builder->get()->getRow();
        return $row ? (int) $row->cnt : 0;
    }

    private function _applyFilters($builder, array $filters): void
    {
        if (!empty($filters['name'])) {
            $name = $this->db->escapeLikeString($filters['name']);
            $builder->groupStart()
                    ->like('p.first_name', $name)
                    ->orLike('p.last_name', $name)
                    ->orLike('p.father_name', $name)
                    ->groupEnd();
        }
        if (!empty($filters['cnic']))     $builder->like('p.cnic', $filters['cnic']);
        if (!empty($filters['category'])) $builder->where('p.category_id', (int) $filters['category']);
        if (!empty($filters['district'])) $builder->where('p.district', $filters['district']);
    }

    public function getPersonBasic(int $pid): ?object
    {
        return $this->db->table('persons')->where('id', $pid)->get()->getRow();
    }

    public function getPersonDetailed(int $pid): array
    {
        return $this->db->table('person_details')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonIdentities(int $pid): array
    {
        return $this->db->table('person_cnic')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonEducation(int $pid): array
    {
        return $this->db->table('person_education')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonIncome(int $pid): array
    {
        return $this->db->table('person_income_sources')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonBanks(int $pid): array
    {
        return $this->db->table('person_bank_accounts')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonAssets(int $pid): array
    {
        return $this->db->table('person_assets')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonMobiles(int $pid): array
    {
        return $this->db->table('person_mobiles')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonRelations(int $pid): array
    {
        return $this->db->table('person_relations')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonCriminal(int $pid): array
    {
        return $this->db->table('person_criminal_records')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonAffiliations(int $pid): array
    {
        return $this->db->table('person_affiliations')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonProjects(int $pid): array
    {
        return $this->db->table('person_projects')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getPersonCategoryHistory(int $pid): array
    {
        return $this->db->table('person_category_history')
                        ->where('person_id', $pid)
                        ->orderBy('created_at', 'DESC')
                        ->get()->getResultObject();
    }

    public function getPersonReports(int $pid): array
    {
        return $this->db->table('person_reports')->where('person_id', $pid)->get()->getResultObject();
    }

    public function getCategories(): array
    {
        return $this->db->table('person_categories')->orderBy('name')->get()->getResultObject();
    }

    public function getDistricts(): array
    {
        return $this->db->table('persons')
                        ->select('DISTINCT district')
                        ->where('district IS NOT NULL', null, false)
                        ->orderBy('district')
                        ->get()->getResultObject();
    }

    public function searchPersons(string $q): array
    {
        if (strlen($q) < 2) {
            return [];
        }
        $like = $this->db->escapeLikeString($q);
        return $this->db->table('persons')
                        ->select('id, first_name, last_name, cnic')
                        ->groupStart()
                        ->like('first_name', $like)
                        ->orLike('last_name', $like)
                        ->orLike('cnic', $like)
                        ->groupEnd()
                        ->limit(20)
                        ->get()->getResultObject();
    }
}
