/* global redux_change, wp */

(function ( $ ) {
	"use strict";
	$.redux = $.redux || {};
	$( document ).ready( function () {
		$.redux.wbc_importer();
	} );
	var in_process        = false,
		all_iterations    = 1,
		current_iteration = 0,
		msg;
	$.redux.wbc_importer  = function () {

		$( '.wrap-importer.theme.not-imported, #wbc-importer-reimport' ).off( 'click' ).on( 'click', function ( e ) {
			e.preventDefault();

			if ( in_process ) {
				return;
			}

			var parent = jQuery( this );

			var reimport = false;

			var message = 'Import Demo Content?';

			if ( 'wbc-importer-reimport' === e.target.id ) {
				reimport = true;
				message  = 'Re-Import Content?';

				if ( !jQuery( this ).hasClass( 'rendered' ) ) {
					parent = jQuery( this ).parents( '.wrap-importer' );
				}
			}

			if ( parent.hasClass( 'imported' ) && false === reimport ) return;

			var imported_skin = parent.closest( '.themes' ).find( '.wrap-importer.theme.active' ).find( '.theme-name' ).text(),
				parent_skin   = parent.find( '.theme-name' ).text();

			if ( imported_skin.match( /Home/gi ) && !parent_skin.match( /Home/gi ) ) {
				alert( 'Not allowed to import skins over default skin. You need to clean up the database before importing this skin. You can use the plugin "WordPress Reset" for it.' );
				return;
			}

			var r = confirm( message );

			if ( false === r ) return;

			if ( true === reimport ) {
				parent.removeClass( 'active imported' ).addClass( 'not-imported' );
			}

			parent.find( '.spinner' ).css( 'display', 'inline-block' );

			parent.removeClass( 'active imported' );

			parent.find( '.importer-button' ).hide();

			var data = jQuery( this ).data();

			in_process = true;

			data.action         = "redux_wbc_importer";
			data.demo_import_id = parent.attr( "data-demo-id" );
			data.nonce          = parent.attr( "data-nonce" );
			data.type           = 'import-demo-content';
			data.wbc_import     = (true === reimport) ? 're-importing' : ' ';
			parent.find( '.wbc_image' ).css( 'opacity', '0.5' );
			parent.find( '.theme-name' ).prepend( '<progress max="100" value="0"/>' );

			import_font( data, parent, reimport );

			return false;
		} );
	};

	function import_font( ajax_data, parent, reimport ) {
		if( 'undefined' === typeof uavc ) {
			ajax_import( ajax_data, parent, reimport );
		} else {
			$.ajax( {
				type:     "POST",
				url:      ajaxurl,
				data:     {
					action:   'smile_ajax_add_zipped_font',
					security: uavc.add_zipped_font,
					values:   {
						id: ''
					}
				},
				error:    function () {
					msg = "Couldn't add the font because the server didn’t respond.";
					console.log( msg );
				},
				success:  function ( response ) {
					if ( response.match( /smile_font_added/ ) ) {
						msg = "Font icon added successfully!";
					}
					else {
						msg = "Couldn't add the font. The script returned the following error: " + response;
					}
					console.log( msg );
				},
				complete: function () {
					ajax_import( ajax_data, parent, reimport );
				}
			} );
		}
	}

	function ajax_import( data, parent, reimport ) {
		jQuery.ajax( {
			type:    'POST',
			cache:   false,
			url:     ajaxurl,
			data:    data,
			error:   function ( response ) {
				//Catch unknown error
				if ( response === null ) {
					response         = {};
					response.success = false;
					response.error   = 'Unknown error occured.';
				}
				console.log( response );
				if ( response.length > 0 ) {
					var regex = /{{wowmall_all_progress=(\d*)}}/gm;
					var found = regex.exec( response );
					if ( null !== found && 'undefined' !== typeof found[1] ) {
						all_iterations = found[1];
					}
				}
				current_iteration++;
				var percent = current_iteration * 100 / all_iterations;
				percent     = 100 > percent ? percent : 100;
				parent.find( 'progress' ).val( percent );
				data.wbc_import = 're-importing';
				ajax_import( data, parent, reimport );

			},
			success: function ( response ) {

				//Catch unknown error
				if ( response === null ) {
					response         = {};
					response.success = false;
					response.error   = 'Unknown error occured.';
				}
				console.log( response );
				if ( response.length > 0 ) {
					var regex = /{{wowmall_all_progress=(\d*)}}/gm;
					var found = regex.exec( response );
					if ( null !== found && 'undefined' !== typeof found[1] ) {
						all_iterations = found[1];
					}
				}
				if ( response.length > 0 && response.match( /wowmall_import_posts_part/gi ) ) {
					current_iteration++;
					var percent = current_iteration * 100 / all_iterations;
					percent     = 100 > percent ? percent : 100;
					parent.find( 'progress' ).val( percent );
					data.wbc_import = 're-importing';
					ajax_import( data, parent, reimport );
				}
				else {
					parent.find( '.wbc_image' ).css( 'opacity', '1' );
					parent.find( '.spinner' ).css( 'display', 'none' );
					parent.find( 'progress' ).remove();
					current_iteration = 0;
					in_process        = false;
					if ( response.length > 0 && response.match( /Have fun!/gi ) ) {
						if ( false === reimport ) {
							parent.addClass( 'rendered' ).find( '.wbc-importer-buttons .importer-button' ).removeClass( 'import-demo-data' );
							var reImportButton = '<div id="wbc-importer-reimport" class="wbc-importer-buttons button-primary import-demo-data importer-button">Re-Import</div>';
							parent.find( '.theme-actions .wbc-importer-buttons' ).append( reImportButton );
						}
						parent.find( '.importer-button:not(#wbc-importer-reimport)' ).removeClass( 'button-primary' ).addClass( 'button' ).text( 'Imported' ).show();
						parent.find( '.importer-button' ).attr( 'style', '' );
						parent.addClass( 'imported active' ).removeClass( 'not-imported' );
					}
					else {
						parent.find( '.import-demo-data' ).show();
						if ( true === reimport ) {
							parent.find( '.importer-button:not(#wbc-importer-reimport)' ).removeClass( 'button-primary' ).addClass( 'button' ).text( 'Imported' ).show();
							parent.find( '.importer-button' ).attr( 'style', '' );
							parent.addClass( 'imported active' ).removeClass( 'not-imported' );
						}
						alert( 'There was an error importing demo content: \n\n' + response.replace( /(<([^>]+)>)/gi, "" ) );
					}
				}
			}
		} );
	}
})( jQuery );