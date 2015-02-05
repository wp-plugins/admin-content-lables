<?php
/**
 * Plugin Name:  Admin Content Labels
 * Plugin URI:   http://www.wpsitecare.com/admin-content-labels/
 * Description:  Categorize your content within wp-admin using admin-specific labels.
 * Version:      1.1.2
 * Author:       Robert Neu
 * Author URI:   https://github.com/robneu
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  admin-content-labels
 * Domain Path:  /languages
 * Git URI:      https://github.com/wpsitecare/admin-content-labels
 * GitHub Plugin URI: https://github.com/wpsitecare/admin-content-labels
 * GitHub Branch: master
 */

// Define the root plugin file.
if ( ! defined( 'ADMIN_CONLAB_FILE' ) ) {
	define( 'ADMIN_CONLAB_FILE', __FILE__ );
}
// Define the plugin version.
if ( ! defined( 'ADMIN_CONLAB_VERSION' ) ) {
	define( 'ADMIN_CONLAB_VERSION', '1.1.2' );
}
// Define the plugin folder path.
if ( ! defined( 'ADMIN_CONLAB_DIR' ) ) {
	define( 'ADMIN_CONLAB_DIR', plugin_dir_path( ADMIN_CONLAB_FILE ) );
}
// Define the plguin Folder URL.
if ( ! defined( 'ADMIN_CONLAB_URL' ) ) {
	define( 'ADMIN_CONLAB_URL', plugin_dir_url( ADMIN_CONLAB_FILE ) );
}

// Load the main plugin class.
require_once( ADMIN_CONLAB_DIR . 'includes/class-plugin.php' );

// Load activation and deactivation functionality.
require_once( ADMIN_CONLAB_DIR . 'includes/activate-deactivate.php' );

add_action( 'plugins_loaded', array( admin_content_lables(), 'run' ) );
/**
 * Allow themes and plugins to access Admin_Content_Labels methods and properties.
 *
 * Because we aren't using a singleton pattern for our main plugin class, we
 * need to make sure it's only instantiated once in our helper function.
 * If you need to access methods inside the plugin classes, use this function.
 *
 * Example:
 *
 * <?php admin_content_lables()->get_admin_label(); ?>
 *
 * @since  1.0.0
 * @access public
 * @uses   Admin_Content_Labels
 * @return object Admin_Content_Labels A single instance of the main plugin class.
 */
function admin_content_lables() {
	static $plugin;
	if ( null === $plugin ) {
		$plugin = new Admin_Content_Labels;
	}
	return $plugin;
}
