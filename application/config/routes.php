<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'users/users/login';
$route['logout'] = 'users/users/logout';
$route['/directory'] = '404';

/* PROCEDURES */
$route['list/procedures'] = 'documents_list/procedures';
$route['list/procedures/(:any)'] = 'documents_list/procedures/$1';
$route['list/getRecords/(:any)/(:any)/(:any)'] = 'documents_list/getRecords/$1/$2/$3';
$route['list/getRecords/(:any)/(:any)'] = 'documents_list/getRecords/$1/$2';
$route['list/getRecords/(:any)'] = 'documents_list/getRecords/$1';
$route['list/view_records/(:any)'] = 'documents_list/view_record/$1';

/* COMPLIANCES */
$route['list/compliances'] = 'documents_list/compliances';
$route['list/compliances/(:any)'] = 'documents_list/compliances/$1';

/* MATERI TRAINING */
$route['list/materi'] = 'documents_list/materi';
$route['list/materi/(:any)'] = 'documents_list/materi/$1';

/* MATERI GUIDES */
$route['list/guides'] = 'documents_list/guides';
$route['list/guides/view_file_guides/(:any)'] = 'documents_list/view_file_guides/$1';
$route['list/guides/(:any)'] = 'documents_list/guides/$1';

/* MATERI GUIDES */
$route['list/manual'] = 'documents_list/manual';
$route['list/manual/(:any)'] = 'documents_list/find_manual/$1';

/* CROSS REFERENCE */
$route['list/cross'] = 'documents_list/cross';
$route['list/cross/(:any)'] = 'documents_list/$1';

/* List Documents */
$route['list/(:any)/(:any)']    = 'documents_list/$1/$2';
$route['list/(:any)']           = 'documents_list/find/$1';
$route['list']                  = 'dashboard';


/* Manage Documents */
$route['docs/procedures'] = 'manage_documents/procedures/$1';
$route['docs/(:any)/(:any)/(:any)'] = 'manage_documents/$1/$2/$3';
$route['docs/(:any)/(:any)'] = 'manage_documents/$1/$2';
$route['docs/(:any)'] = 'manage_documents/$1';
$route['docs'] = 'manage_documents/index';

/* Monitoring - Forms */
$route['monitoring/forms/review'] = 'monitoring/monitoring/forms_review';
$route['monitoring/forms/approval'] = 'monitoring/monitoring/forms_approval';
$route['monitoring/forms/published'] = 'monitoring/monitoring/forms_published';
$route['monitoring/forms/review/(:num)'] = 'monitoring/monitoring/load_form_review_form/$1';
$route['monitoring/forms/review/save'] = 'monitoring/monitoring/save_review_form';
$route['monitoring/forms/approval/(:num)'] = 'monitoring/monitoring/load_form_approval_form/$1';
$route['monitoring/forms/approval/save'] = 'monitoring/monitoring/save_approval_form';
