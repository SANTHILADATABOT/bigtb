<?php

use WeDevs\PM\Core\Router\Router;

$router = Router::singleton();

$router->get( 'all-invoices', 'WeDevs/PM/Invoice/Controllers/Invoice_Controller@show_all' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
