jQuery(document).ready(function(){
	jQuery('ul.sorting a').click(function(e) {
		jQuery(this).css('outline','none');
		jQuery('ul.sorting .current').removeClass('current');
		jQuery(this).parent().addClass('current');
		
		var filterVal = jQuery(this).text().toLowerCase().replace(' ','-');
				
		if( filterVal == 'all' ) {
			jQuery('ul.portfolios li.hidden').fadeIn('fast').removeClass('hidden').addClass('visible');
		} else {
			
			jQuery('ul.portfolios li').each(function() {
				if( ! jQuery(this).hasClass( filterVal ) ) {
					jQuery(this).hide().addClass('hidden').removeClass('visible');
				} else {
					jQuery(this).show().removeClass('hidden').addClass('visible');
				}
			});
		}

		jQuery('ul.portfolios').hide().fadeIn('fast');

		return false;
	});

	jQuery('ul.sorting a').click(function() {
		jQuery('ul.portfolios').find('li').removeClass('last').removeClass('first');
	});

	jQuery('ul.sorting a').click(function() {
		jQuery('ul.portfolios li.visible').each(function(i) {
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