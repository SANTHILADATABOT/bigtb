<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Access_Project;
use WeDevs\PM\Core\Permissions\Create_Task;
use WeDevs\PM\Core\Permissions\Administrator;

$router    = Router::singleton();
$authentic = 'WeDevs\PM\Core\Permissions\Authentic';
$accPermission = 'WeDevs\PM\Core\Permissions\Access_Project';
$editPermission = 'WeDevs\PM\Core\Permissions\Edit_Task';

// Get tasks for specified project
$router->get( 'projects/{project_id}/tasks', 'WeDevs/PM/Task/Controllers/Task_Controller@index' )
    ->permission([$accPermission]);

// Get tasks depending on request params - could be none?
$router->get( 'tasks', 'WeDevs/PM/Task/Helper/Task@get_tasks' )
    ->permission( [ $authentic ] );

$router->get( 'advanced/tasks', 'WeDevs/PM/Task/Helper/Task@get_tasks' )
    ->permission( [ $authentic ] );

$router->get( 'advanced/taskscsv', 'WeDevs/PM/Task/Helper/Task@get_taskscsv' )
    ->permission( [ $authentic ] );

$router->post( 'projects/{project_id}/tasks', 'WeDevs/PM/Task/Controllers/Task_Controller@store' )
    ->permission(['WeDevs\PM\Core\Permissions\Create_Task'])
    ->validator( 'WeDevs\PM\Task\Validators\Create_Task' )
    ->sanitizer( 'WeDevs\PM\Task\Sanitizers\Task_Sanitizer' );

$router->post( 'projects/{project_id}/tasks/sorting', 'WeDevs/PM/Task/Controllers/Task_Controller@task_sorting' )
    ->permission( [ $authentic ] );

$router->get( 'projects/{project_id}/tasks/{task_id}', 'WeDevs/PM/Task/Controllers/Task_Controller@show' )
    ->permission([$accPermission]);

$router->post( 'projects/{project_id}/tasks/{task_id}/update', 'WeDevs/PM/Task/Controllers/Task_Controller@update' )
    ->permission([$editPermission])
    ->validator( 'WeDevs\PM\Task\Validators\Create_Task' )
    ->sanitizer( 'WeDevs\PM\Task\Sanitizers\Task_Sanitizer' );

$router->post( 'projects/{project_id}/tasks/{task_id}/change-status', 'WeDevs/PM/Task/Controllers/Task_Controller@change_status' )
    ->permission(['WeDevs\PM\Core\Permissions\Complete_Task']);

$router->post( 'projects/{project_id}/tasks/{task_id}/delete', 'WeDevs/PM/Task/Controllers/Task_Controller@destroy' )
    ->permission([$editPermission]);

$router->put( 'projects/{project_id}/tasks/{task_id}/attach-users', 'WeDevs/PM/Task/Controllers/Task_Controller@attach_users' )
    ->permission([$editPermission]);

$router->put( 'projects/{project_id}/tasks/{task_id}/detach-users', 'WeDevs/PM/Task/Controllers/Task_Controller@detach_users' )
    ->permission([$editPermission]);

$router->put( 'projects/{project_id}/tasks/{task_id}/boards', 'WeDevs/PM/Task/Controllers/Task_Controller@attach_to_board' )
    ->permission([$editPermission]);

$router->delete( 'projects/{project_id}/tasks/{task_id}/boards', 'WeDevs/PM/Task/Controllers/Task_Controller@detach_from_board' )
    ->permission([$editPermission]);

$router->put( 'projects/{project_id}/tasks/reorder', 'WeDevs/PM/Task/Controllers/Task_Controller@reorder' )
    ->permission(['WeDevs\PM\Core\Permissions\Project_Manage_Capability']);

$router->post( 'projects/{project_id}/tasks/privacy/{task_id}', 'WeDevs/PM/Task/Controllers/Task_Controller@privacy' )
    ->permission([$editPermission]);

$router->post( 'projects/{project_id}/tasks/filter', 'WeDevs/PM/Task/Controllers/Task_Controller@filter' )
    ->permission([$accPermission]);

$router->post( 'projects/{project_id}/tasks/{task_id}/activity', 'WeDevs/PM/Task/Controllers/Task_Controller@activities' )
    ->permission([$accPermission]);

$router->post( 'tasks/{task_id}/duplicate', 'WeDevs/PM/Task/Controllers/Task_Controller@duplicate' )
    ->permission([$editPermission]);

$router->get( 'projects/{project_id}/task-lists/{list_id}/more/tasks', 'WeDevs/PM/Task/Controllers/Task_Controller@load_more_tasks' )
    ->permission([$accPermission]);





