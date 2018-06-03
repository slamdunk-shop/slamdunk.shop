/* global wowmallMediaTranslation */

( function( $ ) {
	'use strict';

	var WowmallAdminMedia = {

        mediaFrame: null,

		init: function ( event, widget ) {

			var wrapper        = widget.find( '.wowmall-admin-media-wrapper' ),
                addMedia       = widget.find( '.wowmall-admin-media-add' ),
				thumbContainer = widget.find( '.wowmall-admin-media-image' ),
				removeBtn      = thumbContainer.find( '.dashicons-dismiss' ),
				id             = wrapper.find( 'input[type=hidden]' );

			addMedia.on( 'click', function( event ) {

                WowmallAdminMedia.openMedia( widget );
			} );

            thumbContainer.on( 'click', function( event ) {

                if($(event.target)[0] === removeBtn[0]) {
                    thumbContainer.removeAttr( 'style' ).hide();
                    addMedia.show();
                    id.val( '' ).trigger( 'change' );
                } else {
                    WowmallAdminMedia.openMedia( widget );
                }
            } );
		},

		openMedia: function ( widget ) {
            if ( WowmallAdminMedia.mediaFrame ) {
                WowmallAdminMedia.mediaFrame.open();
                return;
            }

            WowmallAdminMedia.mediaFrame = wp.media.frames.downloadable_file = wp.media( {
                title:    wowmallMediaTranslation.mediaFrameTitle,
                multiple: false
            } );

            var wrapper        = widget.find( '.wowmall-admin-media-wrapper' ),
                addMedia       = widget.find( '.wowmall-admin-media-add' ),
                thumbContainer = widget.find( '.wowmall-admin-media-image' ),
                removeBtn      = thumbContainer.find( '.dashicons-dismiss' ),
                id             = wrapper.find( 'input[type=hidden]' );

            WowmallAdminMedia.mediaFrame.on( 'select', function() {

                var attachment = WowmallAdminMedia.mediaFrame.state().get( 'selection' ).first().toJSON();

                thumbContainer.attr( {
                    style: 'background-image: url(' + attachment.sizes.thumbnail.url + ');'
                } );
                addMedia.hide();
                id.val( attachment.id ).trigger( 'change' );
            } );

            WowmallAdminMedia.mediaFrame.open();
        }
	};

	$( '#widgets-right' ).find( '.wowmall-admin-media-wrapper' ).closest( 'div.widget' ).each( function () {

        WowmallAdminMedia.init( 'init', $( this ) );
	} );

	$( document ).on( 'widget-updated widget-added', function( event, widget ) {

		if ( widget.find( '.wowmall-admin-media-wrapper' ) ) {

            WowmallAdminMedia.init( event, widget );
		}
	} );

} )( jQuery );