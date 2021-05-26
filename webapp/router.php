<?php
/**
 * Created by PhpStorm.
 * User: smendes
 * Date: 02-05-2016
 * Time: 11:18
 */
use ArmoredCore\Facades\Router;

/****************************************************************************
 *  URLEncoder/HTTPRouter Routing Rules
 *  Use convention: controllerName/methodActionName
 ****************************************************************************/

Router::get('/',			'HomeController/index');
Router::get('home/',		'HomeController/index');
Router::get('home/index',	'HomeController/index');
Router::get('home/start',	'HomeController/start');


/*Router::get('user/index',	'UserController/index');
Router::get('user/create',	'UserController/create');
Router::get('user/store',	'UserController/store');
*/
Router::resource('user', 'UserController');

Router::get('test/index',  'TestController/index');











/************** End of URLEncoder Routing Rules ************************************/