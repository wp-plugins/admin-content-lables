<?php
/**
 * Methods used for filtering and displaying Admin Content Labels.
 *
 * @package     AdminContentLabels
 * @author      Robert Neu
 * @copyright   Copyright (c) 2015, Robert Neu
 * @license     GPL-2.0+
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Admin_Content_Labels_Filters extends Admin_Content_Labels_Data {

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
		add_filter( 'the_title',     array( $this, 'the_admin_label' ) );
		add_filter( 'get_pages',     array( $this, 'admin_label_get_pages' ), 10, 2 );
		add_filter( 'pre_get_posts', array( $this, 'order_by_admin_label' ) );
	}

	/**
	 * Filter the post title to display the admin label if one has been set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $title
	 * @return string modified post title
	 */
	public function the_admin_label( $title ) {
		if ( ! $this->has_label() ) {
			return $title;
		}
		return $this->get_label();
	}

	/**
	 * Filter the post title output within the get_pages function to ensure the
	 * admin label is used in the page attributes metabox and other locations.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $pages
	 * @param  array $r
	 * @return array $pages
	 */
	public function admin_label_get_pages( $pages, $r ) {
		if ( empty( $pages ) ) {
			return $pages;
		}

		foreach ( $pages as $page ) {
			if ( $this->has_label( $page->ID ) ) {
				$page->post_title = $this->get_label( $page->ID );
			}
		}

		return $pages;
	}

	/**
	 * Filter the post ordering by title within the WordPress admin panel so it
	 * takes the custom admin label into account and sorts accordingly.
	 *
	 * This only works because of the hack being used on plugin activation.
	 * Without the function in activate-deactivate.php, this would only display
	 * the posts which have already been updated/saved at least once.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $query
	 * @return array $query
	 */
	public function order_by_admin_label( $query ) {
		// Return early if we're not ordering posts by title.
		if ( false === strpos( $query->get( 'orderby' ), 'title' ) ) {
			return $query;
		}
		// Bail if the current query contains an ACF field.
		if ( false !== strpos( $query->get( 'post_type' ), 'acf-field' ) || 'acf' === $query->get( 'post_type' ) ) {
			return $query;
		}
		$query->set( 'orderby', str_replace( 'title', 'meta_value title', $query->get( 'orderby' ) ) );
		$query->set( 'meta_key', '_admin_content_label' );
		return $query;
	}

}
