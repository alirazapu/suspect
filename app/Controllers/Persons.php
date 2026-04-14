<?php

namespace App\Controllers;

use App\Models\PersonModel;

class Persons extends BaseController
{
    private PersonModel $personModel;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->personModel = new PersonModel();
    }

    // GET /persons  — listing with filters
    public function index()
    {
        $filters = [
            'name'     => $this->request->getGet('name'),
            'cnic'     => $this->request->getGet('cnic'),
            'mobile'   => $this->request->getGet('mobile'),
            'category' => $this->request->getGet('category'),
            'district' => $this->request->getGet('district'),
        ];

        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $data = [
            'persons'    => $this->personModel->getPersonsList($filters, $limit, $offset),
            'total'      => $this->personModel->countPersons($filters),
            'page'       => $page,
            'limit'      => $limit,
            'filters'    => $filters,
            'categories' => $this->personModel->getCategories(),
            'districts'  => $this->personModel->getDistricts(),
            'user'       => $this->currentUser,
        ];

        return view('layout/header', ['title' => 'Persons', 'user' => $this->currentUser])
             . view('layout/sidebar', ['user' => $this->currentUser, 'active' => 'persons'])
             . view('persons/index', $data)
             . view('layout/footer');
    }

    // GET /persons/profile?id=<encrypted_pid>
    public function profile()
    {
        $encId = $this->request->getGet('id');
        if (empty($encId)) {
            return $this->response->setStatusCode(400)->setBody('Person ID is required.');
        }

        $pid = (int) pid_decrypt($encId);
        if ($pid <= 0) {
            return $this->response->setStatusCode(400)->setBody('Invalid person ID.');
        }

        $person = $this->personModel->getPersonBasic($pid);
        if (!$person) {
            return $this->response->setStatusCode(404)->setBody('Person not found.');
        }

        $activeTab = $this->request->getGet('tab') ?: 'basicinfo';

        $data = [
            'pid'        => $pid,
            'enc_id'     => $encId,
            'person'     => $person,
            'active_tab' => $activeTab,
            'user'       => $this->currentUser,
        ];

        $title = esc($person->first_name . ' ' . $person->last_name) . ' — Profile';

        return view('layout/header', ['title' => $title, 'user' => $this->currentUser])
             . view('layout/sidebar', ['user' => $this->currentUser, 'active' => 'persons'])
             . view('persons/profile', $data)
             . view('layout/footer');
    }
}
