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
 *   - Otherwise the user is sent to the persons listing (default dashboard).
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
            log_message('debug', 'Auth::login – already logged in (user_id=' . $this->session->userdata('user_id') . '), redirecting.');
            $this->_redirect_after_login();
            return;
        }

        $data = array('error' => '');

        if ($this->input->method() === 'post') {
            $login    = $this->input->post('login',    TRUE);
            $password = $this->input->post('password', TRUE);

            if (empty($login) || empty($password)) {
                $data['error'] = 'Please enter your username/email and password.';
            } else {
                log_message('debug', 'Auth::login – attempting login for: ' . $login);
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
                    log_message('info', 'Auth::login – session established for user_id=' . $user->id);

                    $this->_redirect_after_login();
                    return;
                } else {
                    log_message('info', 'Auth::login – failed login attempt for: ' . $login);
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
     * SSO hook) or fall back to the persons dashboard.
     *
     * If the stored URL is the login page itself (e.g. the user came here
     * directly rather than being bounced by the gate), ignore it and send
     * the user to the dashboard so we don't loop back to the login form.
     */
    private function _redirect_after_login()
    {
        $url = $this->session->userdata('redirect_after_login');
        $this->session->unset_userdata('redirect_after_login');

        // Treat any URL that contains a login segment as "no valid redirect".
        $is_login_url = ! empty($url) && (
            strpos($url, 'auth/login') !== FALSE ||
            preg_match('#/login/?(\?.*)?$#i', $url)
        );

        if ( ! empty($url) && ! $is_login_url) {
            log_message('debug', 'Auth::_redirect_after_login – redirecting to stored URL: ' . $url);
            redirect($url);
        } else {
            log_message('debug', 'Auth::_redirect_after_login – redirecting to default dashboard (persons). stored_url=' . (empty($url) ? '(empty)' : $url));
            redirect('persons');
        }
    }
}
