<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Suspect SSO shared secret — set via environment variable, no default committed
$config['sso_secret'] = getenv('SSO_SECRET') ?: '';

// Token validity window in seconds (default 5 minutes)
$config['sso_token_ttl'] = 300;

// Password hash pepper shared with dramslive (Kohana Auth hash_key).
// Must match the value in dramslive application/config/auth.php hash_key.
// Override via DRAMS_HASH_KEY environment variable.
$config['drams_hash_key'] = getenv('DRAMS_HASH_KEY') ?: '';
