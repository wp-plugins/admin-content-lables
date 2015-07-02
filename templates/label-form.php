<?php
/**
 * Template part for displaying the admin label form.
 *
 * @package   AdminContentLabels
 * @copyright Copyright (c) 2015, Robert Neu
 * @license   GPL-2.0+
 * @since     1.2.0
 */
?>
<div id="admin-content-label-wrap" class="misc-pub-section admin-content-label" style="position:relative;">
	<label id="admin-content-label-enter" class="screen-reader-text" for="<?php echo $this->key; ?>" style="position:absolute; color: #777; padding: 5px; cursor: text;">
		<?php esc_attr_e( 'Enter admin label here', 'admin-content-labels' ); ?>
	</label>
	<input id="admin-content-label-input" class="widefat" name="<?php echo $this->key; ?>" value="<?php echo esc_attr( $label ); ?>" type="text">
</div>
<?php wp_nonce_field( plugin_basename( ADMIN_CONLAB_FILE ), 'admin_content_labels_nonce' ); ?>
