<?php
// If in debug mode register uncompressed css and js files if available
function dev_register_file( $type, $handle, $min_src, $deps = array(), $ver = false, $arg = false ) {
	// Set default values
	$suffix = 'min';
	$style_type = 'style';
	$script_type = 'script';
	$src = $min_src;
	
	// Make sure required arguments are set
	if( $type !== $style_type && $type !== $script_type ) { return; }
	if( !is_string( $min_src ) ) { return; }
	
	// If not set set argument based on type variable
	if( !isset( $arg ) ) {
		if( $type === $style_type ) {
			$arg = 'all';
		} else {
			$arg = false;
		}
	}
	
	if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		
		// Isolate path to file component from URL
		$components = parse_url( $min_src );
		$path = $components['path'];
		
		// Split the path into parts
		$parts = pathinfo( $path );
		
		// Remove suffix from file name
		$pattern = '/\.' . $suffix . '$/i';
		$file_name = preg_replace( $pattern, '', $parts['filename'] );
		
		// Reassemble path
		$path = $parts['dirname'] . '/' . $file_name . '.' . $parts['extension'];
		
		// If uncompressed css or js file exists set it as the source url
		$target_path = $_SERVER['DOCUMENT_ROOT'] . $path;
		if( file_exists( $target_path ) ) {
			$src = $components['scheme'] . '://' . $components['host'] . $path;
		}
	
	}
	
	// Perform Wordpress file register 
	if( $type === $style_type ) {
		wp_register_style( $handle, $src, $deps, $ver, $arg );
	} else {
		wp_register_script( $handle, $src, $deps, $ver, $arg );
	}
}
?>