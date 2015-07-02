<?php
/**
 * Helper methods for working with admin content labels data.
 *
 * @package     AdminContentLabels
 * @author      Robert Neu
 * @copyright   Copyright (c) 2015, Robert Neu
 * @license     GPL-2.0+
 * @since       1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Admin_Content_Labels_Data {

	/**
	 * Check to see if a given post has an admin label set.
	 *
	 * @since  1.2.0
	 * @access public
	 * @global $post
	 * @return bool
	 */
	public function has_label( $post_id = '' ) {
		global $post;
		if ( ! isset( $post->post_title ) ) {
			return false;
		}
		// Normalize label and title fields.
		$label = wp_strip_all_tags( esc_html( $this->get_label( $post_id ) ) );
		$title = wp_strip_all_tags( $post->post_title );
		// Return false if we have no label or the label is equal to the post title.
		if ( empty( $label ) || sanitize_title( $title ) === sanitize_title( $label ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Get the admin label of a given post.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string
	 */
	public function get_label( $post_id = '' ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		return get_post_meta( $post_id, '_admin_content_label', true );
	}

	/**
	 * Display the admin label.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string $title
	 * @return string modified post title
	 */
	public function the_label( $post_id = '' ) {
		echo $this->get_label( $post_id );
	}

}
