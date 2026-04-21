<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'persons';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['login']  = 'auth/login';
$route['logout'] = 'auth/logout';

// Persons listing
$route['persons']           = 'persons/index';
$route['persons/index']     = 'persons/index';
$route['persons/profile']   = 'persons/profile';

// Person Profile — mirrors dramslive URL structure
// /personprofile/person_profile?id=<encrypted_id>
$route['personprofile/person_profile'] = 'personprofile/person_profile';

// API routes — /api/persons/:id/<tab>
$route['api/lookups']                           = 'api/lookups';
$route['api/persons/search']                    = 'api/persons_search';
$route['api/persons/(:num)/basic']              = 'api/persons_basic/$1';
$route['api/persons/(:num)/detailed']           = 'api/persons_detailed/$1';
$route['api/persons/(:num)/identities']         = 'api/persons_identities/$1';
$route['api/persons/(:num)/education']          = 'api/persons_education/$1';
$route['api/persons/(:num)/income']             = 'api/persons_income/$1';
$route['api/persons/(:num)/banks']              = 'api/persons_banks/$1';
$route['api/persons/(:num)/assets']             = 'api/persons_assets/$1';
$route['api/persons/(:num)/mobiles']            = 'api/persons_mobiles/$1';
$route['api/persons/(:num)/relations']          = 'api/persons_relations/$1';
$route['api/persons/(:num)/criminal']           = 'api/persons_criminal/$1';
$route['api/persons/(:num)/affiliations']       = 'api/persons_affiliations/$1';
$route['api/persons/(:num)/trainings']          = 'api/persons_trainings/$1';
$route['api/persons/(:num)/projects']           = 'api/persons_projects/$1';
$route['api/persons/(:num)/category_history']   = 'api/persons_category_history/$1';
$route['api/persons/(:num)/reports']            = 'api/persons_reports/$1';
$route['api/persons/(:num)/name_cnic']          = 'api/persons_name_cnic/$1';

// Personprofile write endpoints (POST) — mirrors dramslive action names
$route['personprofile/get_district']            = 'personprofile/get_district';
$route['personprofile/get_police_station']      = 'personprofile/get_police_station';
$route['personprofile/get_sect']                = 'personprofile/get_sect';
$route['personprofile/update_basic_info']       = 'personprofile/update_basic_info';
$route['personprofile/update_detail_info']      = 'personprofile/update_detail_info';
$route['personprofile/update_education']        = 'personprofile/update_education';
$route['personprofile/delete_education']        = 'personprofile/delete_education';
$route['personprofile/update_identity']         = 'personprofile/update_identity';
$route['personprofile/delete_identity']         = 'personprofile/delete_identity';
$route['personprofile/update_banks']            = 'personprofile/update_banks';
$route['personprofile/delete_bank']             = 'personprofile/delete_bank';
$route['personprofile/update_personassets']     = 'personprofile/update_personassets';
$route['personprofile/delete_asset']            = 'personprofile/delete_asset';
$route['personprofile/update_mobiles']          = 'personprofile/update_mobiles';
$route['personprofile/update_relations']        = 'personprofile/update_relations';
$route['personprofile/update_criminalr']        = 'personprofile/update_criminalr';
$route['personprofile/delete_criminalrecord']   = 'personprofile/delete_criminalrecord';
$route['personprofile/update_affiliations']     = 'personprofile/update_affiliations';
$route['personprofile/delete_affiliations']     = 'personprofile/delete_affiliations';
$route['personprofile/update_trainings']        = 'personprofile/update_trainings';
$route['personprofile/delete_training']         = 'personprofile/delete_training';
$route['personprofile/update_personincomesource'] = 'personprofile/update_personincomesource';
$route['personprofile/deletesource']            = 'personprofile/deletesource';
$route['personprofile/update_personreports']    = 'personprofile/update_personreports';
$route['personprofile/deletereport']            = 'personprofile/deletereport';
$route['personprofile/upload_doc']              = 'personprofile/upload_doc';
