(function ( $ ) {
	'use strict';
	$( '.swiper-container[data-id]' ).each( function ( n ) {
		var swiper  = [],
		    id      = $( this ).data( 'id' );
		swiper[ n ] = new Swiper( $( this ), {
			grabCursor                  : false,
			preloadImages               : false,
			lazyLoading                 : true,
			autoHeight                  : true,
			lazyLoadingInPrevNext       : true,
			lazyLoadingOnTransitionStart: true,
			onLazyImageReady            : function ( swiper ) {
				swiper.update()
			},
			nextButton                  : $( '.swiper-button-next[data-id=' + id + ']' ),
			prevButton                  : $( '.swiper-button-prev[data-id=' + id + ']' )
		} );
	} );
})( jQuery );