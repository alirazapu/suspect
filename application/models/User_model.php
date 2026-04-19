<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model
 *
 * Authenticates against the shared dramslive `users` table.
 * Login behaviour mirrors dramslive: username or email + hashed password.
 *
 * Supported password hash formats (dramslive compatible):
 *   - SHA-1  : sha1($password)           (older dramslive records)
 *   - MD5    : md5($password)            (legacy fallback)
 *   - bcrypt : password_hash()           (preferred, PHP password_verify)
 */
class User_model extends CI_Model
{
    /** Name of the users table shared with dramslive */
    const USERS_TABLE = 'users';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
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
     * This is a no-op if the password is already stored as bcrypt.
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

        $new_hash = password_hash($plain, PASSWORD_DEFAULT);
        $this->db
            ->where('id', (int) $user_id)
            ->update(self::USERS_TABLE, array('password' => $new_hash));
    }

    /**
     * Supports bcrypt (password_hash), SHA-1, and MD5 to stay compatible
     * with all dramslive password formats.
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

        // SHA-1 (40 hex chars) — legacy dramslive format
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
