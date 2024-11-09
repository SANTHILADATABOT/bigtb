<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Access_Project;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
use WeDevs\PM\Core\Permissions\Create_Task;

$router = Router::singleton();
$generic_permissions = ['WeDevs\PM\Core\Permissions\Authentic'];

$router->get( 'materials/orders', 'WeDevs/PM/Materials/Controllers/MaterialOrderController@show' )
    ->permission($generic_permissions);

$router->get( 'materials/vendors', 'WeDevs/PM/Materials/Controllers/MaterialVendorController@show' )
    ->permission($generic_permissions);

$router->post( 'materials/orders', 'WeDevs/PM/Materials/Controllers/MaterialOrderController@store' )
    ->permission($generic_permissions);

$router->post( 'materials/vendors', 'WeDevs/PM/Materials/Controllers/MaterialVendorController@store' )
    ->permission($generic_permissions);

$router->delete( 'materials/orders/{id}', 'WeDevs/PM/Materials/Controllers/MaterialOrderController@delete' )
    ->permission($generic_permissions);

$router->delete( 'materials/vendors/{id}', 'WeDevs/PM/Materials/Controllers/MaterialVendorController@delete' )
    ->permission($generic_permissions);
