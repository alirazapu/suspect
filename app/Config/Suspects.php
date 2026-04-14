<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Suspects extends BaseConfig
{
    /**
     * Password hash pepper shared with dramslive Kohana Auth.
     * Must match hash_key in dramslive application/config/auth.php.
     * Set via DRAMS_HASH_KEY environment variable.
     */
    public string $dramsHashKey = '';

    /**
     * Shared SSO secret (reserved for future HMAC validation).
     * Set via SSO_SECRET environment variable.
     */
    public string $ssoSecret = '';

    /**
     * SSO token validity window in seconds (default 5 minutes).
     */
    public int $ssoTokenTtl = 300;

    public function __construct()
    {
        parent::__construct();
        $this->dramsHashKey = env('DRAMS_HASH_KEY', '');
        $this->ssoSecret    = env('SSO_SECRET', '');
    }
}
