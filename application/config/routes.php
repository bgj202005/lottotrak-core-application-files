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
$route['default_controller'] = 'page';
$route['translate_uri_dashes'] = FALSE;

// Prize controller routes
$route['admin/prize'] = 'admin/prize/index';
$route['admin/prize/(:num)'] = 'admin/prize/index/$1';  // Prize history with lottery ID
$route['admin/prize/(:any)'] = 'admin/prize/$1';

// Predictions controller routes  
$route['admin/predictions'] = 'admin/predictions/index';
$route['admin/predictions/(:num)'] = 'admin/predictions/index/$1';  // Predictions with lottery ID
$route['admin/predictions/futures/(:num)'] = 'admin/predictions/futures/$1';  // Futures with lottery ID
$route['admin/predictions/combination/(:num)'] = 'admin/predictions/combination/$1';  // Combination with lottery ID
$route['admin/predictions/combination_save/(:num)'] = 'admin/predictions/combination_save/$1';

// Statistics controller routes
$route['admin/statistics'] = 'admin/statistics/index';
$route['admin/statistics/(:num)'] = 'admin/statistics/index/$1';  // Statistics with lottery ID

// History controller routes
$route['admin/history'] = 'admin/history/index';
$route['admin/history/(:num)'] = 'admin/history/index/$1';  // History with lottery ID

// Lotteries controller routes
$route['admin/lotteries'] = 'admin/lotteries/index';
$route['admin/lotteries/(:num)'] = 'admin/lotteries/index/$1';  // Lotteries with lottery ID

// Dashboard controller routes
$route['admin/dashboard'] = 'admin/dashboard/index';
$route['admin/dashboard/(:any)'] = 'admin/dashboard/$1';

// Add route for combination save
// Article route
$route['article/(:num)/(:any)'] = 'article/index/$1/$2';
// 404 to page controller (if not found controller routing) 
$route['404_override'] = 'page';