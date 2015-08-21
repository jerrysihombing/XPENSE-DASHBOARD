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
|	http://codeigniter.com/user_guide/general/routing.html
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

$route['default_controller'] = 'DboardController';
$route['translate_uri_dashes'] = FALSE;
$route['404_override'] = '';

# Dboard
$route['dboard'] = 'DboardController';
$route['dboard/(:num)/(:num)'] = 'DboardController/index/$1/$2';
$route['dboard/(:num)/(:num)/(:num)'] = 'DboardController/index/$1/$2/$3';
$route['dboard/(:num)/(:num)/cluster/(:any)'] = 'DboardController/index/$1/$2/cluster/$3';
$route['dboard/(:num)/(:num)/regional/(:any)'] = 'DboardController/index/$1/$2/regional/$3';
$route['dboard/ytd/(:num)/(:num)/(:num)'] = 'DboardController/ytd/$1/$2/$3';
$route['dboard/getdata/(:num)/(:num)/(:num)'] = 'DboardController/getData/$1/$2/$3';
$route['dboard/getdatacluster/(:num)/(:num)/(:any)'] = 'DboardController/getDataCluster/$1/$2/$3';
$route['dboard/getdataregional/(:num)/(:num)/(:any)'] = 'DboardController/getDataRegional/$1/$2/$3';
$route['dboard/getdataytd/(:num)'] = 'DboardController/getDataYtd/$1';
$route['dboard/getdataytdv2/(:num)/(:num)'] = 'DboardController/getDataYtdV2/$1/$2';
$route['dboard/savecharts'] = 'DboardController/saveCharts';

# Compose
$route['compose'] = 'ComposeController';
$route['compose/send'] = 'ComposeController/send';
$route['compose/(:num)/(:num)/(:num)/(:any)'] = 'ComposeController/index/$1/$2/$3/$4';
$route['compose/(:num)/(:num)/cluster/(:any)/(:any)'] = 'ComposeController/index/$1/$2/cluster/$3/$4';
$route['compose/(:num)/(:num)/regional/(:any)/(:any)'] = 'ComposeController/index/$1/$2/regional/$3/$4';

# Admin
$route['admin'] = 'AdminController/user';
$route['admin/user'] = 'AdminController/user';
$route['admin/user/(:any)'] = 'AdminController/user/$1';
$route['admin/user/(:any)/(:num)'] = 'AdminController/user/$1/$2';
$route['admin/role'] = 'AdminController/role';
$route['admin/role/(:any)'] = 'AdminController/role/$1';
$route['admin/role/(:any)/(:num)'] = 'AdminController/role/$1/$2';
$route['admin/contact'] = 'AdminController/contact';
$route['admin/contact/(:any)'] = 'AdminController/contact/$1';
$route['admin/contact/(:any)/(:num)'] = 'AdminController/contact/$1/$2';
$route['admin/expense'] = 'AdminController/expense';
$route['admin/expense/(:any)'] = 'AdminController/expense/$1';
$route['admin/expense/(:any)/(:num)'] = 'AdminController/expense/$1/$2';
$route['admin/stores/(:num)'] = 'AdminController/stores/$1';
$route['admin/menus/(:num)'] = 'AdminController/menus/$1';
$route['admin/accounts/(:num)'] = 'AdminController/accounts/$1';

# Login
$route['login'] = 'LoginController';
$route['login/auth'] = 'LoginController/auth';

# Change password
$route['chpass'] = 'ChpassController';
$route['chpass/commit'] = 'ChpassController/commit';

# Logout
$route['logout'] = 'LogoutController';

# Unauthorized
$route['unauthorized'] = 'UnauthorizedController';