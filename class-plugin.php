<?php
/**
 * Enhanced User Profiles main plugin class.
 *
 * @package     AdminContentLabels
 * @author      Robert Neu
 * @copyright   Copyright (c) 2015, Robert Neu
 * @license     GPL-2.0+
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
class Admin_Content_Labels {

	/**
	 * An empty placeholder for referencing the filters class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	public $filters;

	/**
	 * An empty placeholder for referencing the meta class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	public $meta;

	/**
	 * An empty placeholder for referencing the data class.
	 *
	 * @since 1.2.0
	 * @var   object
	 */
	public $data;

	/**
	 * Method to initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function run() {
		$this->load_textdomain();
		$this->includes();
		$this->instantiate();
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'admin-content-labels',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Require all plugin files.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function includes() {
		require_once ADMIN_CONLAB_DIR . 'includes/class-data.php';
		require_once ADMIN_CONLAB_DIR . 'includes/class-filters.php';
		require_once ADMIN_CONLAB_DIR . 'includes/class-meta.php';
	}

	/**
	 * Load all required files and get all of our classes running.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function instantiate() {
		$this->data    = new Admin_Content_Labels_Data;
		$this->filters = new Admin_Content_Labels_Filters;
		$this->meta    = new Admin_Content_Labels_Meta;

		$this->filters->run();
		$this->meta->run();
	}

	/**
	 * Get the admin label of a given post.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function get_admin_label( $post_id = '' ) {
		return $this->data->get_label( $post_id );
	}

	/**
	 * Check to see if a given post has an admin label set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global $post
	 * @return bool
	 */
	public function has_admin_label( $post_id = '' ) {
		return $this->data->has_label( $post_id );
	}

	/**
	 * Install
	 *
	 * Runs on plugin activation to set a default admin content label for all
	 * existing posts using the post title.
	 *
	 * Props to Shawn Hooper (https://twitter.com/ShawnHooper) for writing the SQL
	 * which powers this function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global $wpdb
	 * @return void
	 */
	public function install() {
		global $wpdb;

		// Get list of post types
		$types_unsafe = get_post_types( array( 'public' => true ) );
		unset( $types_unsafe['attachment'] );

		// Make sure post type names are safe to use as an SQL value.
		$types = array();
		foreach ( $types_unsafe as $type ) {
			array_push( $types, esc_sql( $type ) );
		}
		// Convert escaped post types to a string for use in our SQL statement.
		$types = implode( "','", $types );

		// Prepare an SQL statement to efficiently set the default labels.
		// Only set this on posts that don't already have an admin label.
		$sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) ";
			$sql .= "SELECT ID, '_admin_content_label', post_title ";
			$sql .= "FROM {$wpdb->posts} p ";
			$sql .= "LEFT JOIN {$wpdb->postmeta} pm ";
			$sql .= "ON p.ID = pm.post_id AND meta_key = '_admin_content_label' ";
			$sql .= "WHERE pm.meta_key IS NULL AND p.post_type IN ('{$types}')";

		// Set default content labels.
		$wpdb->query( $sql );
	}

}
