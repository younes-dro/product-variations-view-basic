<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	WP_CLI::add_command(
		'woocommerce_clear_all_guests_carts',
		function () {
			global $wpdb;
			
			$session_table = $wpdb->prefix . 'woocommerce_sessions';
			// Delete all entries where the user is not logged in (guest users).
			$deleted = $wpdb->query(
				"
            DELETE FROM $session_table
            WHERE session_value LIKE '%cart%'
        "
			);

			if ( $deleted === false ) {
				WP_CLI::error( 'Failed to clear guest carts.' );
			} elseif ( $deleted === 0 ) {
				WP_CLI::success( 'No guest carts were found.' );
			} else {
				WP_CLI::success( "$deleted guest cart(s) cleared successfully." );
			}
		}
	);
}
