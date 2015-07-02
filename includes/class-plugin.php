<?php
/**
 * Enhanced User Profiles main plugin class.
 *
 * @package     AdminContentLabels
 * @author      Robert Neu
 * @copyright   Copyright (c) 2014, Robert Neu
 * @license     GPL-2.0+
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 * Method to initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function run() {
		// Return early if we're not in the WordPress admin panel.
		if ( ! is_admin() ) {
			return;
		}
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
		// Set filter for plugin's languages directory
		$lang_dir = ADMIN_CONLAB_DIR . 'languages/';
		$lang_dir = apply_filters( 'admin_content_labels_lang_directory', $lang_dir );
		// Load the default language files
		load_plugin_textdomain( 'admin-content-labels', false, $lang_dir );
	}

	/**
	 * Require all plugin files.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function includes() {
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
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		return get_post_meta( $post_id, '_admin_content_label', true );
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
		global $post;
		if ( ! isset( $post->post_title ) ) {
			return false;
		}
		// Normalize label and title fields.
		$label = wp_strip_all_tags( esc_html( $this->get_admin_label( $post_id ) ) );
		$title = wp_strip_all_tags( $post->post_title );
		// Return false if we have no label or the label is equal to the post title.
		if ( empty( $label ) || sanitize_title( $title ) === sanitize_title( $label ) ) {
			return false;
		}
		return true;
	}

}
