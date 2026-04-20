<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SSO Token Hook
 *
 * Runs on every request (post_controller_constructor) to enforce the
 * access-token login gate:
 *
 *  1. If the user already has a valid CI session → allow.
 *  2. If a token is present in the query string (`token` or `accesstoken`)
 *     → validate it against the `user_tokens` DB table (expires >= now).
 *     If valid  → establish CI session and continue.
 *     If invalid/expired → save current URL in session and redirect to login.
 *  3. No token and no session → save current URL in session, redirect to login.
 *
 * Excluded paths (not protected):
 *   - auth/login
 *   - auth/logout
 *   - Any URI segment starting with "assets", "dist", or "uploads"
 */
class Sso_token
{
    /** @var CI_Controller */
    private $CI;

    public function check()
    {
        $this->CI =& get_instance();

        $uri = $this->CI->uri->uri_string();

        // ------------------------------------------------------------------
        // Exclude public routes from the gate
        // ------------------------------------------------------------------
        $excluded_prefixes = array('auth/login', 'auth/logout', 'login', 'logout', 'assets', 'dist', 'uploads');
        foreach ($excluded_prefixes as $prefix) {
            if ($uri === $prefix || strpos($uri, $prefix . '/') === 0) {
                return; // allow through without checking
            }
        }

        // ------------------------------------------------------------------
        // 1. Already logged in via session?
        // ------------------------------------------------------------------
        if ($this->CI->session->userdata('user_id')) {
            return; // authenticated
        }

        // ------------------------------------------------------------------
        // 2. Token provided in query string?
        // ------------------------------------------------------------------
        $token = $this->CI->input->get('token');
        if (empty($token)) {
            $token = $this->CI->input->get('accesstoken');
        }

        if ( ! empty($token)) {
            // Sanitize: token column is varchar(40), reject if length is wrong
            $token = trim($token);
            if (strlen($token) > 40) {
                $token = '';
            }
        }

        if ( ! empty($token)) {
            $this->CI->load->database();

            $row = $this->CI->db
                ->where('token', $token)
                ->where('expires >=', time())
                ->get('user_tokens')
                ->row();

            if ($row) {
                // Valid token — load user and establish session
                $user = $this->CI->db
                    ->where('id', $row->user_id)
                    ->get('users')
                    ->row();

                if ($user) {
                    $this->_set_user_session($user);
                    return; // authenticated, let request continue
                }
            }

            // Token is present but invalid/expired — fall through to redirect
        }

        // ------------------------------------------------------------------
        // 3. No valid session and no valid token → redirect to login
        // ------------------------------------------------------------------
        // Store the full current URL so we can redirect back after login
        $current_url = current_url();
        $query_string = $this->CI->input->server('QUERY_STRING');
        if ( ! empty($query_string)) {
            // Rebuild query string without token/accesstoken params
            parse_str($query_string, $params);
            unset($params['token'], $params['accesstoken']);
            if ( ! empty($params)) {
                $current_url .= '?' . http_build_query($params);
            }
        }

        $this->CI->session->set_userdata('redirect_after_login', $current_url);
        redirect('auth/login');
        exit;
    }

    /**
     * Persist user data in CI session (mirrors dramslive login behaviour).
     *
     * @param  object $user  Row from `users` table
     */
    private function _set_user_session($user)
    {
        $data = array(
            'user_id'    => $user->id,
            'username'   => isset($user->username)   ? $user->username   : '',
            'email'      => isset($user->email)      ? $user->email      : '',
            'name'       => isset($user->name)       ? $user->name       : '',
            'logged_in'  => TRUE,
        );
        $this->CI->session->set_userdata($data);
    }
}
