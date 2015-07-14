<?php

/**
 * Check if the array of files, or given file, exists in the data folder.
 *
 * @since 0.3.0
 *
 * @param string | array $files The string or array of file names to check if
 *        they exist in the data folder.
 * @param string         $filter Acts as a logical operator to check if 'all' or 'some'
 *                files exists.
 * @return bool Returns Boolean value depending on how many files are found
 *        depending on the $filter. 'all' will return TRUE if all given files
 *        exist. 'some' will return TRUE if at least one files exists.
 */
function geoipsl_data_files_exist( $files, $filter = 'some' ) {

	$file_count = 0;

	// if string is passed, we can work with comma-separated file names
	if ( is_string( $files ) ) {
		$files = explode( ',', $files );
		// otherwise return false if we do not have an array of file names
	} else if ( ! is_array( $files ) ) {
		return false;
	}

	foreach ( $files as $file ) {
		if ( file_exists( geoipsl_get_file_path( $file, 'data' ) ) ) {
			$file_count++;
		}
	}

	if ( ! in_array( $filter, array( 'some', 'all' ) ) ) {
		$filter = 'some'; }

	if ( $file_count > 0 && 'some' == $filter ) {
		return true; }

	if ( $file_count == count( $files ) && 'all' == $filter ) {
		return true; }

	return false;
}

/**
 * Check the last time the data file is last modified.
 *
 * @since 0.3.0
 *
 * @param string $file The file name to check relative to the data/ folder.
 * @return string Date modified or "Never." if file does not exist.
 */
function geoipsl_last_uploaded( $file ) {
	return ( file_exists( geoipsl_get_file_path( $file ) ) ) ? date( 'd M Y ( D )', filemtime( geoipsl_get_file_path( $file ) ) ) :  __( 'Never.', 'geoipsl' );
}

/**
 * Prefix a file name with the full path to our plugins data/ folder.
 *
 * @since 0.1.0
 *
 * @param string $geolocation_id The string to be prefixed.
 * @throws InvalidArgumentException
 * @return bool|string Boolean FALSE if our plugin dir cannot be found
 */
function geoipsl_get_file_path( $dest_file_name, $dir = 'data' ) {
	if ( ! is_string( $dest_file_name ) ) {
		throw new InvalidArgumentException( 'get_file_path expects
       $dest_file_name to be string, '
		. gettype( $dest_file_name ) . ' given.' );
	}

	if ( ! is_string( $dir ) ) {
		throw new InvalidArgumentException( 'get_file_path expects
       $dest_file_name to be string, '
		. gettype( $dest_file_name ) . ' given.' );
	}

	if ( strpbrk( trim( $dest_file_name, '/' ), '\\/?%*:|"<>' ) !== false ) {
		throw new InvalidArgumentException( 'get_file_path expects
       $dest_file_name to be a valid file name, '
		. $dest_file_name . ' given.' );
	}

	if ( strpbrk( trim( $dir, '/' ), '\?%*:|"<>' ) !== false ) {
		throw new InvalidArgumentException( 'get_file_path expects $dir to be a
       valid directory name, ' . $dir . ' given.' );
	}

	return sprintf( '%s/%s/%s', rtrim( GEOIPSL_PLUGIN_DIR, '/' ),
	trim( $dir, '/' ), trim( $dest_file_name, '/' ) );
}

/**
 * Unzip given file an put it in the data/ directory.
 *
 * @since 0.3.0
 *
 * @param string $dest_file_name The destination file name (not file path),
 *        relative to the data/ directory.
 * @param string $source_file_name The source file name.
 * @return string $destination_file_name
 */
function geoipsl_unzip_file( $dest_file_name, $source_file_name ) {

	// resolve destination file, return FALSE if unable to
	$destination_file = geoipsl_get_file_path( $dest_file_name );
	if ( ! $destination_file || ! $source_file_name ) {
		return false;
	}

	// we need this to use the download_url function geoipsl_of Worldpress
	require_once( ABSPATH.'/wp-admin/includes/file.php' );

	$file_ext = pathinfo( $source_file_name );
	$file_ext = $file_ext['extension'];

	if ( 'gz' == $file_ext ) {
		// open a gzip file for reading
		$input_file     = gzopen( $source_file_name, 'r' );
	} else {
		// open the source file for reading
		$input_file     = fopen( $source_file_name, 'r' );
	}

	// create the target file then open it for writing
	$output_file    = fopen( $destination_file, 'w' );

	if ( 'gz' == $file_ext ) {
		// read the contens of source file and copy it to destination file
		while ( ( $line = gzread( $input_file, 4096 ) ) != false ) {
			fwrite( $output_file , $line, strlen( $line ) );
		}
	} else {
		// read the contens of source file and copy it to destination file
		while ( ( $line = fread( $input_file, 4096 ) ) != false ) {
			fwrite( $output_file , $line, strlen( $line ) );
		}
	}

	if ( 'gz' == $file_ext ) {
		// close connections to temporary source file and target file
		gzclose( $input_file );
	} else {
		// close connections to temporary source file and target file
		fclose( $input_file );
	}

	fclose( $output_file );

	// if we're unable to move anything to the new destination file
	if ( ! $destination_file || ! file_exists( $destination_file ) ) {
		update_option( geoipsl( 'download_error' ), 'Destination file not found.' );
		return 2;
	}

	// delete the temporary file
	unlink( $source_file_name );

	// surely, success so return path to new file
	return $destination_file;
}
