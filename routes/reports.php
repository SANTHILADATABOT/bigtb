<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Access_Project;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
use WeDevs\PM\Core\Permissions\Create_Task;

$router = Router::singleton();
$generic_permissions = ['WeDevs\PM\Core\Permissions\Authentic'];

$router->get(
    'db-reports',
    'WeDevs/PM/Reports/Controllers/ReportsController@getReports'
)->permission($generic_permissions);

$router->get(
    'db-reports/employees',
    'WeDevs/PM/Reports/Controllers/ReportsController@getEmployees'
)->permission($generic_permissions);

$router->get(
    'db-reports/records',
    'WeDevs/PM/Reports/Controllers/ReportsController@getRecords'
)->permission($generic_permissions);

$router->get(
    'db-reports/budget',
    'WeDevs/PM/Reports/Controllers/ReportsController@showBudget'
)->permission($generic_permissions);

$router->get(
    'db-reports/inventory-quantity-by-location',
    'WeDevs/PM/Reports/Controllers/ReportsController@showInventoryByLocation'
)->permission($generic_permissions);

$router->get(
    'db-reports/wac-value-report',
    'WeDevs/PM/Reports/Controllers/ReportsController@showWacValues'
)->permission($generic_permissions);

$router->get(
    'db-reports/job-cost-totals',
    'WeDevs/PM/Reports/Controllers/ReportsController@showJobCostTotals'
)->permission($generic_permissions);

$router->get(
    'db-reports/job-cost-by-cost-code',
    'WeDevs/PM/Reports/Controllers/ReportsController@showJobCostByCostCode'
)->permission($generic_permissions);

$router->get(
    'db-reports/job-cost-journal',
    'WeDevs/PM/Reports/Controllers/ReportsController@showJobCostJournal'
)->permission($generic_permissions);

$router->get(
    'db-reports/job-labor-journal-by-phase',
    'WeDevs/PM/Reports/Controllers/ReportsController@showJobLaborJournal'
)->permission($generic_permissions);

$router->get(
    'db-reports/purchase-orders',
    'WeDevs/PM/Reports/Controllers/ReportsController@showPurchaseOrders'
)->permission($generic_permissions);

$router->get(
    'db-reports/receivable-invoice-retention',
    'WeDevs/PM/Reports/Controllers/ReportsController@showReceivableInvoice'
)->permission($generic_permissions);

$router->get(
    'db-reports/part-record',
    'WeDevs/PM/Reports/Controllers/ReportsController@showPartsRecord'
)->permission($generic_permissions);

$router->get(
    'db-reports/wip-schedule',
    'WeDevs/PM/Reports/Controllers/ReportsController@showWipSchedule'
)->permission($generic_permissions);

$router->get(
    'db-reports/status-report',
    'WeDevs/PM/Reports/Controllers/ReportsController@showStatusReport'
)->permission($generic_permissions);

$router->get(
    'db-reports/parts-on-hand',
    'WeDevs/PM/Reports/Controllers/ReportsController@showPartsOnHand'
)->permission($generic_permissions);

$router->get(
    'db-reports/parts-on-hand-two',
    'WeDevs/PM/Reports/Controllers/ReportsController@showPartsOnHandTwo'
)->permission($generic_permissions);
