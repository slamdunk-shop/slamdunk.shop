<?php
	// Load Redux extensions
	if ( class_exists( 'Redux' ) && file_exists( dirname( __FILE__ ) . '/redux-extensions/extensions-init.php' ) ) {
		require_once dirname( __FILE__ ) . '/redux-extensions/extensions-init.php';
	}

	require_once dirname( __FILE__ ) . '/redux.fallback.php';

    // Load the theme/plugin options
    if ( file_exists( dirname( __FILE__ ) . '/options-init.php' ) ) {
        require_once dirname( __FILE__ ) . '/options-init.php';
    }
