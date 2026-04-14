<?php

namespace App\Controllers;

use App\Models\PersonModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    private PersonModel $personModel;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->personModel = new PersonModel();
    }

    public function persons_basic(int $pid = 0)            { return $this->_json($this->personModel->getPersonBasic($pid)); }
    public function persons_detailed(int $pid = 0)         { return $this->_json($this->personModel->getPersonDetailed($pid)); }
    public function persons_identities(int $pid = 0)       { return $this->_json($this->personModel->getPersonIdentities($pid)); }
    public function persons_education(int $pid = 0)        { return $this->_json($this->personModel->getPersonEducation($pid)); }
    public function persons_income(int $pid = 0)           { return $this->_json($this->personModel->getPersonIncome($pid)); }
    public function persons_banks(int $pid = 0)            { return $this->_json($this->personModel->getPersonBanks($pid)); }
    public function persons_assets(int $pid = 0)           { return $this->_json($this->personModel->getPersonAssets($pid)); }
    public function persons_mobiles(int $pid = 0)          { return $this->_json($this->personModel->getPersonMobiles($pid)); }
    public function persons_relations(int $pid = 0)        { return $this->_json($this->personModel->getPersonRelations($pid)); }
    public function persons_criminal(int $pid = 0)         { return $this->_json($this->personModel->getPersonCriminal($pid)); }
    public function persons_affiliations(int $pid = 0)     { return $this->_json($this->personModel->getPersonAffiliations($pid)); }
    public function persons_projects(int $pid = 0)         { return $this->_json($this->personModel->getPersonProjects($pid)); }
    public function persons_category_history(int $pid = 0) { return $this->_json($this->personModel->getPersonCategoryHistory($pid)); }
    public function persons_reports(int $pid = 0)          { return $this->_json($this->personModel->getPersonReports($pid)); }

    public function persons_search()
    {
        $q = $this->request->getGet('q') ?? '';
        return $this->_json($this->personModel->searchPersons($q));
    }

    private function _json($data): ResponseInterface
    {
        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode(['status' => 'success', 'data' => $data]));
    }
}
