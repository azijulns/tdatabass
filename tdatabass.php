<?php

/**

 * Plugin Name: Tdatabass
 *  Version: 1.0.0
 * Description: Tdatabass
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author:      Azijul H.
 * Author URI:  https://ash-digitalsolutions.com/
 * Text Domain: services
 * Domain Path: /languages
 *
 */
defined('ABSPATH') || die();

define('TDB_PLUGIN_VERSION', '1.0.0');
define('TDB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TDB_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('TDB_PLUGIN_ASSETS', trailingslashit(TDB_PLUGIN_DIR_URL  . 'assets'));

if (!class_exists('TDB_PLUGIN_PLUGIN')) :

    final class TDB_MAIN {
        private static $instance;

        private function __construct() {
            add_action('plugins_loaded', [$this, 'init_plugin']);
        }

        public function init_plugin() {

            // new Tdatabass\Includes\Functions();
        }

        public static function instance() {
            if (!isset(self::$instance) && !(self::$instance instanceof TDB_MAIN)) {
                self::$instance = new TDB_MAIN();
                self::$instance->includes();
            }

            return self::$instance;
        }

        private function includes() {
            require_once TDB_PLUGIN_DIR . 'includes/services_base.php';
            require_once TDB_PLUGIN_DIR . 'includes/functions.php';
        }
    }

endif;

function tdb_init_plugin() {
    return TDB_MAIN::instance();
}

tdb_init_plugin();
