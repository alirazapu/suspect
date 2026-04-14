<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('pid');
        $this->load->config('suspect');
    }

    // GET /auth/login  — show login form
    // POST /auth/login — process credentials
    public function login() {
        if ($this->current_user) {
            redirect('persons');
        }

        $data['error'] = '';

        if ($this->input->method() === 'post') {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);

            $user = $this->_authenticate($username, $password);
            if ($user) {
                $this->_start_session($user);
                $return = $this->input->post('return', TRUE);
                redirect($return ?: 'persons');
            } else {
                $data['error'] = 'Invalid username or password.';
            }
        }

        $this->load->view('layout/header', ['title' => 'Login', 'body_class' => 'hold-transition login-page']);
        $this->load->view('auth/login', $data);
        $this->load->view('layout/footer_plain');
    }

    // GET /auth/sso?token=...&pid=...&return=...
    public function sso() {
        $token  = $this->input->get('token',  TRUE);
        $pid    = $this->input->get('pid',    TRUE);   // encrypted PID string
        $return = $this->input->get('return', TRUE);

        if (empty($token)) {
            show_error('Missing SSO token', 400);
            return;
        }

        // Look up user by login_token in the shared aiesplus DB
        $this->db->where('login_token', $token);
        $query = $this->db->get('users');
        $user  = $query->row();

        if (!$user) {
            show_error('Invalid or already-used SSO token.', 403);
            return;
        }

        // Check expiry
        if (!empty($user->token_expires) && strtotime($user->token_expires) < time()) {
            show_error('SSO token has expired. Please go back and try again.', 403);
            return;
        }

        // Invalidate token (one-time use)
        $this->db->where('id', $user->id)
                 ->update('users', ['login_token' => NULL, 'token_expires' => NULL]);

        // Start session
        $this->_start_session($user);

        // Build redirect destination
        if (!empty($pid)) {
            $numeric_pid = (int) pid_decrypt($pid);
            if ($numeric_pid > 0) {
                redirect('persons/profile?id=' . urlencode($pid));
                return;
            }
        }
        redirect($return ?: 'persons');
    }

    // GET /auth/logout
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // ----------------------------------------------------------------
    private function _authenticate($username, $password) {
        // Kohana ORM Auth hashes as: hash('sha256', hash_key . password)
        // The hash_key is configured in dramslive's application/config/auth.php
        // and must be supplied via the DRAMS_HASH_KEY environment variable.
        $hash_key = $this->config->item('drams_hash_key');
        $hashed   = hash('sha256', $hash_key . $password);

        $this->db->where('username', $username)
                 ->where('password', $hashed)
                 ->where('is_active', 1);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        // Fallback: try plain sha256 (no pepper) in case legacy users exist
        $hashed_plain = hash('sha256', $password);
        if ($hashed_plain !== $hashed) {
            $this->db->where('username', $username)
                     ->where('password', $hashed_plain)
                     ->where('is_active', 1);
            $q2 = $this->db->get('users');
            if ($q2->num_rows() > 0) {
                return $q2->row();
            }
        }

        return false;
    }

    private function _start_session($user) {
        $this->session->set_userdata('suspect_user', [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => isset($user->email) ? $user->email : '',
        ]);
    }
}
