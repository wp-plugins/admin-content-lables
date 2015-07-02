<?php
/**
 * Plugin Name:  Admin Content Labels
 * Plugin URI:   http://www.wpsitecare.com/admin-content-labels/
 * Description:  Categorize your content within wp-admin using admin-specific labels.
 * Version:      1.2.1
 * Author:       Robert Neu
 * Author URI:   https://github.com/robneu
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  admin-content-labels
 * Domain Path:  /languages
 */

defined( 'ABSPATH' ) || exit;

if ( is_admin() ) {
	define( 'ADMIN_CONLAB_FILE', __FILE__ );
	define( 'ADMIN_CONLAB_VERSION', '1.2.1' );

	if ( ! defined( 'ADMIN_CONLAB_DIR' ) ) {
		define( 'ADMIN_CONLAB_DIR', plugin_dir_path( __FILE__ ) );
	}
	if ( ! defined( 'ADMIN_CONLAB_URL' ) ) {
		define( 'ADMIN_CONLAB_URL', plugin_dir_url( __FILE__ ) );
	}

	// Load the main plugin class.
	require_once ADMIN_CONLAB_DIR . 'class-plugin.php';

	/**
	 * Allow themes and plugins to access Admin_Content_Labels methods and properties.
	 *
	 * Because we aren't using a singleton pattern for our main plugin class, we
	 * need to make sure it's only instantiated once in our helper function.
	 * If you need to access methods inside the plugin classes, use this function.
	 *
	 * Example:
	 *
	 * <?php admin_content_lables()->data->get_label(); ?>
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
	add_action( 'plugins_loaded', array( admin_content_lables(), 'run' ) );

	register_activation_hook( __FILE__, array( admin_content_lables(), 'install' ) );
}
