<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Suspect SSO shared secret — set via environment variable, no default committed
$config['sso_secret'] = getenv('SSO_SECRET') ?: '';

// Token validity window in seconds (default 5 minutes)
$config['sso_token_ttl'] = 300;
