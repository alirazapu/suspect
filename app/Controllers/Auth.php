<?php

namespace App\Controllers;

use Config\Suspects;

class Auth extends BaseController
{
    private Suspects $suspectConfig;

    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->suspectConfig = config('Suspects');
    }

    // GET /auth/login  — show login form
    // POST /auth/login — process credentials
    public function login()
    {
        if ($this->currentUser) {
            return redirect()->to(base_url('persons'));
        }

        $data = ['error' => ''];

        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $this->_authenticate($username, $password);
            if ($user) {
                $this->_startSession($user);
                $return = $this->request->getPost('return');
                return redirect()->to($return ?: base_url('persons'));
            }
            $data['error'] = 'Invalid username or password.';
        }

        return view('layout/header', ['title' => 'Login', 'body_class' => 'hold-transition login-page'])
             . view('auth/login', $data)
             . view('layout/footer_plain');
    }

    // GET /auth/sso?token=...&pid=...&return=...
    public function sso()
    {
        $token  = $this->request->getGet('token');
        $pid    = $this->request->getGet('pid');    // encrypted PID string
        $return = $this->request->getGet('return');

        if (empty($token)) {
            return $this->response->setStatusCode(400)->setBody('Missing SSO token.');
        }

        $db   = db_connect();
        $user = $db->table('users')->where('login_token', $token)->get()->getRow();

        if (!$user) {
            return $this->response->setStatusCode(403)->setBody('Invalid or already-used SSO token.');
        }

        if (!empty($user->token_expires) && strtotime($user->token_expires) < time()) {
            return $this->response->setStatusCode(403)->setBody('SSO token has expired. Please go back and try again.');
        }

        // Invalidate token (one-time use)
        $db->table('users')->where('id', $user->id)
           ->update(['login_token' => null, 'token_expires' => null]);

        $this->_startSession($user);

        if (!empty($pid)) {
            $numericPid = (int) pid_decrypt($pid);
            if ($numericPid > 0) {
                return redirect()->to(base_url('persons/profile?id=' . urlencode($pid)));
            }
        }

        return redirect()->to($return ?: base_url('persons'));
    }

    // GET /auth/logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }

    // ----------------------------------------------------------------
    private function _authenticate(string $username, string $password): object|false
    {
        $db      = db_connect();
        $hashKey = $this->suspectConfig->dramsHashKey;
        $hashed  = hash('sha256', $hashKey . $password);

        $user = $db->table('users')
                   ->where('username', $username)
                   ->where('password', $hashed)
                   ->where('is_active', 1)
                   ->get()->getRow();
        if ($user) {
            return $user;
        }

        // Fallback: plain sha256 (no pepper) for legacy users
        $hashedPlain = hash('sha256', $password);
        if ($hashedPlain !== $hashed) {
            $user = $db->table('users')
                       ->where('username', $username)
                       ->where('password', $hashedPlain)
                       ->where('is_active', 1)
                       ->get()->getRow();
            if ($user) {
                return $user;
            }
        }

        return false;
    }

    private function _startSession(object $user): void
    {
        session()->set('suspect_user', [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email ?? '',
        ]);
    }
}
