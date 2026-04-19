<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model
 *
 * Authenticates against the shared dramslive `users` table.
 * Login behaviour mirrors dramslive: username + HMAC-SHA256 password.
 *
 * Supported password hash formats (dramslive compatible):
 *   - HMAC-SHA256 : hash_hmac('sha256', $password, $hash_key)  (standard dramslive format)
 *   - SHA-1       : sha1($password)                            (older records)
 *   - MD5         : md5($password)                             (legacy fallback)
 *   - bcrypt      : password_hash()                            (PHP-native)
 */
class User_model extends CI_Model
{
    /** Name of the users table shared with dramslive */
    const USERS_TABLE = 'users';

    /** HMAC key — must match dramslive application/config/auth.php `hash_key` */
    private $_hash_key;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->config->load('suspects', TRUE);
        $this->_hash_key = $this->config->item('auth_hash_key', 'suspects');
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    /**
     * Attempt to log in with username/email and plain-text password.
     *
     * @param  string $login     Username or e-mail address
     * @param  string $password  Plain-text password supplied by the user
     * @return object|false      User row on success, FALSE on failure
     */
    public function login($login, $password)
    {
        $login = trim($login);

        // Fetch user by username OR email
        $user = $this->db
            ->where('username', $login)
            ->or_where('email', $login)
            ->get(self::USERS_TABLE)
            ->row();

        if ( ! $user) {
            return FALSE;
        }

        if ( ! $this->_verify_password($password, $user->password)) {
            return FALSE;
        }

        // Opportunistically upgrade legacy hash to bcrypt on successful login
        $this->_maybe_upgrade_hash($user->id, $user->password, $password);

        return $user;
    }

    /**
     * Find a user by primary key.
     *
     * @param  int $id
     * @return object|false
     */
    public function get_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get(self::USERS_TABLE)
            ->row();
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    /**
     * Upgrade a legacy SHA-1 or MD5 hash to bcrypt on the next successful login.
     * HMAC-SHA256 and bcrypt hashes are left untouched — changing HMAC-SHA256
     * would break the same user's login in dramslive (shared table).
     *
     * @param  int    $user_id
     * @param  string $stored_hash  Current hash in DB
     * @param  string $plain        Plain-text password that just authenticated
     */
    private function _maybe_upgrade_hash($user_id, $stored_hash, $plain)
    {
        // bcrypt hashes start with '$2' — no upgrade needed
        if (strpos($stored_hash, '$2') === 0) {
            return;
        }

        // HMAC-SHA256 hashes are 64 hex chars — shared with dramslive, do NOT change
        if (strlen($stored_hash) === 64) {
            return;
        }

        $new_hash = password_hash($plain, PASSWORD_DEFAULT);
        $this->db
            ->where('id', (int) $user_id)
            ->update(self::USERS_TABLE, array('password' => $new_hash));
    }

    /**
     * Verifies a plain-text password against a stored hash.
     *
     * Supported formats (checked in order):
     *   1. bcrypt / argon2    — PHP password_hash(); starts with '$2' or '$argon'
     *   2. HMAC-SHA256 (64 hex) — hash_hmac('sha256', $plain, $hash_key); dramslive standard
     *   3. SHA-1   (40 hex)   — sha1($plain); legacy records
     *   4. MD5     (32 hex)   — md5($plain); very old records
     *
     * @param  string $plain   Plain-text password
     * @param  string $hashed  Stored hash from DB
     * @return bool
     */
    private function _verify_password($plain, $hashed)
    {
        if (empty($plain) || empty($hashed)) {
            return FALSE;
        }

        // bcrypt / argon2 (PHP password_hash)
        if (password_verify($plain, $hashed)) {
            return TRUE;
        }

        // HMAC-SHA256 (64 hex chars) — standard dramslive format
        // dramslive: hash_hmac('sha256', $password, $config['hash_key'])
        if (strlen($hashed) === 64 && hash_equals($hashed, hash_hmac('sha256', $plain, $this->_hash_key))) {
            return TRUE;
        }

        // SHA-1 (40 hex chars) — legacy format
        if (strlen($hashed) === 40 && hash_equals($hashed, sha1($plain))) {
            return TRUE;
        }

        // MD5 (32 hex chars) — very old records
        if (strlen($hashed) === 32 && hash_equals($hashed, md5($plain))) {
            return TRUE;
        }

        return FALSE;
    }
}
