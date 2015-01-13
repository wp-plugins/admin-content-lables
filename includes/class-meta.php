<?php
/**
 * Methods used for adding and saving meta data for Admin Content Labels.
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

class Admin_Content_Labels_Meta {

	/**
	 * An empty placeholder for referencing the main plugin class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	protected $plugin;

	/**
	 * The meta key for admin content labels.
	 *
	 * @since 1.0.2
	 * @var   string
	 */
	protected $key = '_admin_content_label';

	/**
	 * Get the class running!
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->plugin = admin_content_lables();
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
				ADMIN_CONLAB_URL . 'js/admin-content-labels.js',
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

		$label = $this->plugin->has_admin_label() ? $this->plugin->get_admin_label() : '';
		?>
		<div id="admin-content-label-wrap" class="misc-pub-section admin-content-label" style="position:relative;">
			<?php wp_nonce_field( plugin_basename( ADMIN_CONLAB_FILE ), 'admin_content_labels_nonce' ); ?>
			<label id="admin-content-label-enter" class="screen-reader-text" for="<?php echo $this->key; ?>" style="position:absolute; color: #777; padding: 5px; cursor: text;">
				<?php _e( 'Enter admin label here', 'admin-content-labels' ); ?>
			</label>
			<input id="admin-content-label-input" class="widefat" name="<?php echo $this->key; ?>" value="<?php echo esc_attr( $label ); ?>" type="text">
		</div>
		<?php
	}

	/**
	 * Helper function to determine if an automated task which should prevent
	 * saving meta box data is running.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function stop_label_save() {
		$stops = array(
			defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE,
			defined( 'DOING_AJAX' ) && DOING_AJAX,
			defined( 'DOING_CRON' ) && DOING_CRON,
		);
		return in_array( true, $stops );
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

		$nonce = isset( $_POST['admin_content_labels_nonce'] ) ? $_POST['admin_content_labels_nonce'] : '';

		//	Bail if we can't verify the nonce.
		if ( ! wp_verify_nonce( $nonce, plugin_basename( ADMIN_CONLAB_FILE ) ) ) {
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
