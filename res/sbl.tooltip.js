/**
 * qTip Javascript handler for the sbl extension
 */

/*global jQuery, mediaWiki */
/*global confirm */

( function ( $, mw ) {

	'use strict';

	$( function ( $ ) {

		/**
		 * qTip tooltip instance
		 *
		 * @since 1.2
		 */
		$( '.sbl-breadcrumb-children' ).each(function () {

			$( this ).prev().addClass( 'sbl-breadcrumb-has-childern' )

			// The current instance is hidden therefore look for the direct
			// precedents and only act on a href link
			$( this ).prev().find( 'a' ).qtip( {
				content: {
					text  : $( this ).data( 'children' )
				},
				position: {
					viewport: $( window ),
					at: 'bottom center',
					my: 'top center'
				},
				hide    : {
					fixed: true,
					delay: 300
				},
				style   : {
					classes: 'qtip-shadow qtip-bootstrap',
					def    : false
				}
			} );
		} );

	} );
}( jQuery, mediaWiki ) );
