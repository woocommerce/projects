jQuery(document).ready(function($){
	// Hide all containers except the first
	jQuery( 'div.settings-container' ).each( function() {
		if ( jQuery( this ).attr( 'id' ) == 'pages-settings-container' ) {
			jQuery( this ).show();
			jQuery( this ).prev( 'h3' ).show();
		} else {
			jQuery( this ).hide();
			jQuery( this ).prev( 'h3' ).hide();
		} // End If Statement
	});
	jQuery( 'h2.nav-tab-wrapper' ).on( 'click', 'a.nav-tab', function( event ) {
		// Get id of settings to show
		var containerId = jQuery( this ).html().toLowerCase() + '-settings-container';
		jQuery( 'div.settings-container' ).each( function() {
			if ( jQuery( this ).attr( 'id' ) == containerId ) {
				jQuery( this ).show();
				jQuery( this ).prev( 'h3' ).show();
			} else {
				jQuery( this ).hide();
				jQuery( this ).prev( 'h3' ).hide();
			} // End If Statement
		});
		// Set active menu tab
		jQuery( 'a.nav-tab-active' ).removeClass( 'nav-tab-active' );
		jQuery( this ).addClass( 'nav-tab-active' );
	});
});