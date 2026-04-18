<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 *
 * Handles manual login and logout for the Suspect application.
 * Uses the same `users` table as the dramslive project (shared DB).
 *
 * After a successful login:
 *   - If a `redirect_after_login` URL was stored in the session
 *     (set by the SSO hook when redirecting an unauthenticated request),
 *     the user is sent back to that URL.
 *   - Otherwise the user is sent to the default dashboard.
 */
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    // ------------------------------------------------------------------
    // Login
    // ------------------------------------------------------------------

    /**
     * GET  auth/login  — show the login form
     * POST auth/login  — process credentials
     */
    public function login()
    {
        // Already logged in? Send home.
        if ($this->session->userdata('user_id')) {
            $this->_redirect_after_login();
        }

        $data = array('error' => '');

        if ($this->input->method() === 'post') {
            $login    = $this->input->post('login',    TRUE);
            $password = $this->input->post('password', TRUE);

            if (empty($login) || empty($password)) {
                $data['error'] = 'Please enter your username/email and password.';
            } else {
                $user = $this->User_model->login($login, $password);

                if ($user) {
                    // Establish session (same keys used by the SSO hook)
                    $this->session->set_userdata(array(
                        'user_id'   => $user->id,
                        'username'  => isset($user->username) ? $user->username : '',
                        'email'     => isset($user->email)    ? $user->email    : '',
                        'name'      => isset($user->name)     ? $user->name     : '',
                        'logged_in' => TRUE,
                    ));

                    $this->_redirect_after_login();
                } else {
                    $data['error'] = 'Invalid username/email or password.';
                }
            }
        }

        $this->load->view('auth/login', $data);
    }

    // ------------------------------------------------------------------
    // Logout
    // ------------------------------------------------------------------

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Redirect to the originally requested URL (stored in session by the
     * SSO hook) or fall back to the site root / dashboard.
     */
    private function _redirect_after_login()
    {
        $url = $this->session->userdata('redirect_after_login');
        $this->session->unset_userdata('redirect_after_login');

        if ( ! empty($url)) {
            redirect($url);
        } else {
            redirect('/');
        }
    }
}
