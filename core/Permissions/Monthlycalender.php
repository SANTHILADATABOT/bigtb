<?php

namespace WeDevs\PM\Core\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WP_REST_Request;

class Monthlycalender extends Abstract_Permission {
    public function check() {

        if ( pm_user_can_access( pm_manager_cap_slug() ) ) {
            return true;
        }
        
        return new \WP_Error( 'monthlycalender', __( "You have no permission to create monthlycalender.", "wedevs-project-manager" ) );
    }
}
