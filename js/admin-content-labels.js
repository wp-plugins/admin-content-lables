/**
 * Admin Content Labels plugin JavaScript
 *
 * @since 1.0.0
 */
(function( $, undefined ) {
	'use strict';

	/**
	 * Toggle screen-reader-text class on the admin-content-label-input field.
	 *
	 * @since 1.0.0
	 */
	function inputLabel() {
		$( '#admin-content-label-input' ).each( function() {
			var input  = $( this ),
				prompt = $( '#admin-content-label-enter' );

			// If the input is blank on page load then show helper text.
			if ( '' === this.value ) {
				prompt.removeClass( 'screen-reader-text' );
			}
			// Hide the helper text when the input label is clicked.
			prompt.click( function() {
				$( this ).addClass( 'screen-reader-text' );
				input.focus();
			} );
			// Hide the helper text when the input is clicked.
			input.focus( function() {
				prompt.addClass( 'screen-reader-text' );
			});
			// When input has lost focus and it's empty, show helper text.
			input.blur( function() {
				if ( '' === this.value ) {
					prompt.removeClass( 'screen-reader-text' );
				}
			});
		});
	}

	$( document ).ready( inputLabel );
})( jQuery );
