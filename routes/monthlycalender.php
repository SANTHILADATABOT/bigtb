<?php

use WeDevs\PM\Core\Router\Router;

$router = Router::singleton();

 $router->post('monthlycalender/store', 'WeDevs\PM\Monthlycalender\Controllers\Calenders_Controller@store');
 $router->post('monthlycalender/update', 'WeDevs\PM\Monthlycalender\Controllers\Calenders_Controller@update');
$router->get( 'monthlycalender/index', 'WeDevs\PM\Monthlycalender\Controllers\Calenders_Controller@index' );
$router->post( 'monthlycalender/delete', 'WeDevs/PM/Monthlycalender/Controllers/Calenders_Controller@destroy' );

