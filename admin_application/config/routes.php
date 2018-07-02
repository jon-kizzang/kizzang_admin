<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
//Payment Server Stuff
/*
$route['payments'] = "home/index";
$route['payments/(:num)'] = "home/index/$1";
$route['payments/(:num)/(:any)'] = "home/index/$1/$2";
$route['payments/(:num)/(:any)/(:num)'] = "home/index/$1/$2/$3";
$route['ipn'] = "home/ipn";
$route['tdf'] = "home/tdf";
$route['payments/players'] = "home/players";
$route['payments/player/(:num)'] = "home/player/$1";
$route['payments/edit/(:num)'] = "home/edit/$1";
$route['ajax_pay/(:num)'] = "home/ajax_pay/$1";
$route['ajax_qb/(:num)'] = "home/ajax_qb/$1";
$route['ajax_donate/(:num)'] = "home/ajax_donate/$1";
$route['ajax_documents/(:num)'] = "home/ajax_documents/$1";
$route['ajax_manual_pay/(:num)'] = "home/ajax_manual_pay/$1";
$route['ajax_forfeit/(:num)'] = "home/ajax_forfeit/$1";
$route['payments/report'] = "home/report";
$route['mass_pay'] = "home/mass_pay";
*/

$route['scratch/(:any)'] = 'scratch/$1';
$route['scratch'] = 'scratch';
$route['reset/(:any)'] = 'reset/$1';
$route['reset'] = 'reset';
$route['test/(:any)'] = 'test/$1';
$route['test'] = 'test';
$route['admin_sports/(:num)'] = 'admin_sports/view_sports_schedule/$1';
$route['default_controller'] = "admin";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */