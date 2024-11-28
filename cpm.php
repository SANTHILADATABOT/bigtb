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

 const PLUGIN_SLUG_NAME = 'vue-pm-admin';

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

function enqueue_vue_script() {
    wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js', array(), null, true);
    wp_enqueue_script(
        'pm-scheduler',
        plugins_url('views/assets/js/pmglobal.js', __FILE__),
        array('vue'),
        null,
        true
    );
}

add_action('wp_enqueue_scripts', 'enqueue_vue_script');
function my_plugin_enqueue_styles() {
    wp_enqueue_style('my-plugin-tailwind', plugin_dir_url(__FILE__) . 'views/assets/dist/tailwind.css', [], '1.0.0');
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');
