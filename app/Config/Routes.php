<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Root
$routes->get('/', 'Auth::login');

// Auth routes
$routes->get('auth/login',  'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/sso',    'Auth::sso');

// Persons (protected by AuthFilter in Filters.php)
$routes->get('persons',         'Persons::index');
$routes->get('persons/profile', 'Persons::profile');

// API (protected by AuthFilter in Filters.php)
$routes->get('api/persons/(:num)/basic',            'Api::persons_basic/$1');
$routes->get('api/persons/(:num)/detailed',         'Api::persons_detailed/$1');
$routes->get('api/persons/(:num)/identities',       'Api::persons_identities/$1');
$routes->get('api/persons/(:num)/education',        'Api::persons_education/$1');
$routes->get('api/persons/(:num)/income',           'Api::persons_income/$1');
$routes->get('api/persons/(:num)/banks',            'Api::persons_banks/$1');
$routes->get('api/persons/(:num)/assets',           'Api::persons_assets/$1');
$routes->get('api/persons/(:num)/mobiles',          'Api::persons_mobiles/$1');
$routes->get('api/persons/(:num)/relations',        'Api::persons_relations/$1');
$routes->get('api/persons/(:num)/criminal',         'Api::persons_criminal/$1');
$routes->get('api/persons/(:num)/affiliations',     'Api::persons_affiliations/$1');
$routes->get('api/persons/(:num)/projects',         'Api::persons_projects/$1');
$routes->get('api/persons/(:num)/category_history', 'Api::persons_category_history/$1');
$routes->get('api/persons/(:num)/reports',          'Api::persons_reports/$1');
$routes->get('api/persons/search',                  'Api::persons_search');
