jQuery(document).ready(function($){
	jQuery('ul.sorting a').click(function(e) {
		e.preventDefault();
		jQuery(this).css('outline','none');
		jQuery('ul.sorting .current').removeClass('current');
		jQuery(this).parent().addClass('current');

		// filterVal = slug of active link
		var filterVal = jQuery(this).text().toLowerCase().replace(' ','-');

		// filterVal default = 'all'
		if( filterVal == 'all' ) {
			jQuery('ul.projects li.hidden').show().removeClass('hidden').addClass('visible');
		} else {

			// Show / hide based on active link
			jQuery('ul.projects li').each(function() {

				if( ! jQuery(this).hasClass( filterVal ) ) {
					// If the li doesn't have a class = filterVal, hide it
					jQuery(this).hide().addClass('hidden').removeClass('visible');
				} else {
					// If it does, show it
					jQuery(this).show().removeClass('hidden').addClass('visible');
				}
			});
		}

		//jQuery('ul.projects').hide().fadeIn(300);

		return false;
	});

	jQuery('ul.sorting a').click(function() {
		jQuery('ul.projects').find('li').removeClass('last').removeClass('first');
	});

	jQuery('ul.sorting a').click(function() {
		jQuery('ul.projects li.visible').each(function(i) {
			i=( i+1 );
			x=( i+2 );
			if ( i%3==0 ) {
				jQuery(this).removeClass('first').addClass('last');
			}
			if ( x%3==0 ) {
				jQuery(this).removeClass('last').addClass('first');
			}
		});
	});
});