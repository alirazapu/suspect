<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/*
| -------------------------------------------------------------------------
| SSO Token Gate
| -------------------------------------------------------------------------
| Validates every request against an active CI session or a valid
| access token (query param `token` or `accesstoken`) from the
| `user_tokens` table.  Unauthenticated requests are redirected to
| the login page and the intended URL is saved in the session.
*/
$hook['post_controller_constructor'] = array(
    'class'    => 'Sso_token',
    'function' => 'check',
    'filename' => 'Sso_token.php',
    'filepath' => 'hooks',
    'params'   => array(),
);

