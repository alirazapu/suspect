<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| ---------------------------------------------------------------------------
| Suspect — Application-specific configuration
| ---------------------------------------------------------------------------
|
| Person ID encryption
| ---------------------
| Person IDs in URLs are encrypted using the same AES-256-CBC scheme as
| the dramslive project (Helpers_Utilities::encrypted_key).  The keys are
| compiled into Person_model::decrypt_person_id() / encrypt_person_id()
| and do NOT need to be set via environment variable.
|
*/

/*
| ---------------------------------------------------------------------------
| Password hashing — dramslive compatibility
| ---------------------------------------------------------------------------
|
| auth_hash_key
| -------------
| The HMAC key used by dramslive's Kohana Auth module to hash passwords.
| This MUST match the `hash_key` value in the dramslive project's
| `application/config/auth.php` so that passwords hashed by dramslive can
| be verified here.
|
| Set this via an environment variable in your web-server vhost config:
|   SetEnv SUSPECT_AUTH_HASH_KEY "your-hmac-key-here"
|
| The default below matches the value shipped in dramslive's auth config.
|
*/
$config['auth_hash_key'] = getenv('SUSPECT_AUTH_HASH_KEY') ?: 'Never gonna give you up';

/*
| ---------------------------------------------------------------------------
| SSO / Token configuration
| ---------------------------------------------------------------------------
|
| token_ttl
| ---------
| How long (in seconds) an access token remains valid once issued.
| Default: 3600 (1 hour).  Production tokens from ctd.drams.com carry their
| own `expires` timestamp in the `user_tokens` table — this value is only
| used when generating new tokens internally.
|
*/
$config['token_ttl'] = 3600;

/*
| ---------------------------------------------------------------------------
| Person listing defaults
| ---------------------------------------------------------------------------
*/
$config['persons_per_page'] = 25;
