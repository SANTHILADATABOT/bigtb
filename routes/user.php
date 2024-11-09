<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;

$router = Router::singleton();

$permission = ['WeDevs\PM\Core\Permissions\Authentic'];

$router->get( 'users', 'WeDevs/PM/User/Controllers/User_Controller@index' )
    ->permission($permission);

$router->post( 'users', 'WeDevs/PM/User/Controllers/User_Controller@store' )
    ->permission($permission);

$router->get( 'users/{id}', 'WeDevs/PM/User/Controllers/User_Controller@show' )
    ->permission($permission);

$router->get( 'users/search', 'WeDevs/PM/User/Controllers/User_Controller@search' )
    ->permission($permission);

$router->put( 'users/{user_id}/roles', 'WeDevs/PM/User/Controllers/User_Controller@update_role' )
    ->permission($permission);

$router->post( 'save_users_map_name', 'WeDevs/PM/User/Controllers/User_Controller@save_users_map_name' )
    ->permission($permission);

$router->get( 'user-all-projects', 'WeDevs/PM/User/Controllers/User_Controller@get_user_all_projects' )
    ->permission($permission);

$router->get( 'current-user', 'WeDevs/PM/User/Controllers/User_Controller@showCurrent' )
    ->permission($permission);
