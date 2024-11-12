<?php
/**
 * Plugin Name: BigTB Accounting
 * Description: Advanced Project Management Software for businesses. Requires WP Project Manager Pro.
 * Author: BigTB
 * Author URI: https://bigtb.com
 * Version: 1.2.0
 * Text Domain: wedevs-project-manager
 * Domain Path: /languages
 * License: Proprietary
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require __DIR__.'/bootstrap/loaders.php';
require __DIR__.'/libs/configurations.php';

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
    add_action( 'admin_notices',  'pm_php_version_notice'  );
    return;
}

define( 'PM_FILE', __FILE__ );
define( 'PM_BASENAME', plugin_basename(__FILE__) );
define( 'PM_PLUGIN_ASSEST', plugins_url( 'views/assets', __FILE__ ) );

require __DIR__.'/bootstrap/start.php';


function enqueue_syncfusion_styles_script() {
    ?>
    <script type="text/javascript">
        // Function to load Syncfusion styles on page load
        function loadSyncfusionStyles() {
            const stylesheets = [
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Buttons
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Calendars
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Dropdowns
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Inputs
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Navigations
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css', // Syncfusion Popups
                'https://cdn.syncfusion.com/ej2/20.2.36/material.css'  // Syncfusion Schedule (optional)
            ];

            stylesheets.forEach(function(url) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = url;
                document.head.appendChild(link);
            });
        }

        // Add the load event listener to ensure styles load on page load
        window.addEventListener('load', loadSyncfusionStyles);
    </script>
    <?php
}
add_action('wp_footer', 'enqueue_syncfusion_styles_script');
