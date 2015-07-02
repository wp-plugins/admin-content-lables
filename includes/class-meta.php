<?php
/**
 * Methods used for adding and saving meta data for Admin Content Labels.
 *
 * @package     AdminContentLabels
 * @author      Robert Neu
 * @copyright   Copyright (c) 2015, Robert Neu
 * @license     GPL-2.0+
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Admin_Content_Labels_Meta extends Admin_Content_Labels_Data {

	/**
	 * The meta key for admin content labels.
	 *
	 * @since 1.0.2
	 * @var   string
	 */
	protected $key = '_admin_content_label';

	/**
	 * A script prefix to load minified assets on production sites.
	 *
	 * @since 1.2.0
	 * @var   string
	 */
	protected $suffix;

	public function __construct() {
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * Get the class running!
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Hook into WordPress.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'admin_enqueue_scripts',       array( $this, 'load_admin_scripts' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'admin_label_form' ) );
		add_action( 'save_post',                   array( $this, 'save_label_meta' ), 10, 2 );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load_admin_scripts( $hook ) {
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			wp_enqueue_script(
				'admin-content-labels',
				ADMIN_CONLAB_URL . "js/admin-content-labels{$this->suffix}.js",
				array( 'jquery' ),
				ADMIN_CONLAB_VERSION,
				true
			);
		}
	}

	/**
	 * Display an input field to allow users to add a content admin label.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_label_form() {
		$post_type_object = get_post_type_object( get_post_type() );
		// Bail if the current user doesn't have permission to edit the current post.
		if ( ! current_user_can( $post_type_object->cap->edit_post, get_the_ID() ) ) {
			return;
		}

		$label = $this->has_label() ? $this->get_label() : '';
		require ADMIN_CONLAB_DIR . 'templates/label-form.php';
	}

	/**
	 * Helper function to determine if an automated task which should prevent
	 * saving meta box data is running.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function stop_label_save() {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
			defined( 'DOING_AJAX' ) && DOING_AJAX ||
			defined( 'DOING_CRON' ) && DOING_CRON;
	}

	/**
	 * Callback function for saving our testimonial details meta box data.
	 * Handles data validation and sanitization for our content label.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function save_label_meta( $post_id, $post ) {
		// Bail if something is in progress.
		if ( $this->stop_label_save() ) {
			return;
		}

		$no  = 'admin_content_labels_nonce';
		$act = plugin_basename( ADMIN_CONLAB_FILE );

		//	Bail if we can't verify the nonce.
		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		// Bail if the current user doesn't have permission to edit the current post.
		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) {
			return;
		}

		$old   = get_post_meta( $post_id, $this->key, true );
		$new   = isset( $_POST[ $this->key ] ) ? trim( $_POST[ $this->key ] ) : '';
		$value = $post->post_title;

		if ( ! empty( $new ) ) {
			$value = wp_strip_all_tags( $new );
			$value = sanitize_post_field( $this->key, $value, $post_id, 'db' );
		}

		// Update our post meta if our new value is different from the old.
		if ( $new !== $old || empty( $old ) ) {
			update_post_meta( $post_id, $this->key, $value );
		}
	}

}
