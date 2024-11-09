<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Access_Project;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
use WeDevs\PM\Core\Permissions\Create_Task;

$router = Router::singleton();
$generic_permissions = ['WeDevs\PM\Core\Permissions\Authentic'];

$router->get( 'global-kanboard', 'WeDevs/PM/Global_Kanboard/Controllers/Global_Kanboard_Controller@index' )
    ->permission($generic_permissions);

// Adds a new board (column) to the global kanboard
$router->post( 'global-kanboard',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@store' )
    ->permission($generic_permissions);

// Changes the order of the boards (columns) on the global kanboard
$router->put( 'global-kanboard',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@board_order' )
    ->permission($generic_permissions);

// Adds a new board (column) to the global kanboard
$router->delete( 'global-kanboard/{board_id}',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@destroy' )
    ->permission($generic_permissions);

// Adds an existing project to the global kanboard
$router->post( 'global-kanboard/{board_id}/project/{project_id}',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@store_searchable_project' )
	->permission($generic_permissions);

// meant to gather all the projects that are searchable to be placed in the global kanboard
$router->get( 'global-kanboard/projects',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@get_projects' )
    ->permission($generic_permissions);

// show() retrieves all of the projects on the boardable table to be displayed on the global kanboard
$router->get( 'global-kanboard/{board_id}/projects',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@show' )
    ->permission($generic_permissions);

// removes the boardable from a particular board - and from the kanban altogether
$router->delete( 'global-kanboard/{board_id}/boardable/{project_id}',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@remove_boardable' )
    ->permission($generic_permissions);

// updates the boardables to be located on a particular board by editing board column
$router->put( 'global-kanboard/boardable',
    'WeDevs\PM\Global_Kanboard\Controllers\Global_Kanboard_Controller@update_boardable' )
    ->permission($generic_permissions);
// Everything edited above this point
