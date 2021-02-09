<?php
// This file should be set to be auto_append on the server or
// included at the top of every entry point files

define( '__MY_IP_ADDRESS', 'x.x.x.x' ); // Set to false for all calls
define( '__FILE_LIST_JSON', '/home/project/__file_list.json' );
define( '__PER_ENTRY_POINT', true );

// this function will get registered to be called at the end of script execution at the bottom of this file
function __get_file_lists__() {
	if (
		! array_key_exists( 'REMOTE_ADDR', $_SERVER ) ||
		( __MY_IP_ADDRESS && $_SERVER['REMOTE_ADDR'] == __MY_IP_ADDRESS )
	) {
		$data = false;
		if ( file_exists( __FILE_LIST_JSON ) && is_writable( __FILE_LIST_JSON ) ) {
			$data = file_get_contents( __FILE_LIST_JSON );
			$data = json_decode( $data, true );
			if ( ! is_array( $data ) ) {
				$data = [ 'entry' => [], 'files' => [] ];
			} else {
				if ( ! array_key_exists( 'entry', $data ) ) {
					$data['entry'] = [];
				}
				if ( ! array_key_exists( 'files', $data ) ) {
					$data['files'] = [];
				}
			}

			// Get the entry point file. The IF block is for if it is called
			// via command line, or via cron
			$from   = 'web';
			$script = $_SERVER['SCRIPT_FILENAME'];
			if ( array_key_exists( 'PWD', $_SERVER ) ) {
				$from   = 'cli';
				$script = $_SERVER['PWD'] . DIRECTORY_SEPARATOR . $script;
			}

			// Don't run if we are calling the script to view the data
			if ( substr( $script, - 16 ) != '/__view_list.php' ) {

				// Add the entry point file to the list
				if ( ! array_key_exists( $script, $data['entry'] ) ) {
					if ( __PER_ENTRY_POINT ) {
						// Set up to list "from"s (CLI or WEB, can be both so use an array)
						$data['entry'][ $script ] = [
							'from'  => [],
							'files' => []
						];
					} else {
						// Just add the name to the list
						$data['entry'][] = $script;
					}
				}
				if ( __PER_ENTRY_POINT ) {
					// add "from" in case different from existing
					if ( ! in_array( $from, $data['entry'][ $script ]['from'] ) ) {
						$data['entry'][ $script ]['from'][] = $from;
					}
				}

				// Add the files used to the global file list
				$files = get_included_files();
				foreach ( $files as $file ) {
					if ( ! in_array( $file, $data['files'] ) ) {
						$data['files'][] = $file;
					}
					if ( __PER_ENTRY_POINT && ! in_array( $file, $data['entry'][ $script ]['files'] ) ) {
						$data['entry'][ $script ]['files'][] = $file;
					}
				}

				// All done, sort them and save the data
				sort( $data['files'] );
				if ( __PER_ENTRY_POINT ) {
					if ( ! in_array( $from, $data['entry'][ $script ]['from'] ) ) {
						$data['entry'][ $script ]['from'][] = $from;
					}
					sort( $data['entry'][ $script ]['files'] );
				}
				file_put_contents( __FILE_LIST_JSON, json_encode( $data ) );

			}

		}

	}
}

register_shutdown_function( '__get_file_lists__' );
