<?php
/**
 * Activation and Deactivation hooks and functions.
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

register_activation_hook( ADMIN_CONLAB_FILE, 'admin_content_labels_install' );
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
function admin_content_labels_install() {
	global $wpdb;

	// Get list of post types
	$types_unsafe = get_post_types( array( 'public' => true, ) );
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
