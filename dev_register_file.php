<?php 
class ThemeFiles {

	private $defaults = array(
		'type'		=> false,
		'handle'	=> false,
		'src'		=> false,
		'deps'		=> array(),
		'vers'		=> false,
		'media'		=> 'all',
		'in_footer'	=> false
	);
	
	private $files = array();
	
	const MIN		= 'min';
	const STYLE		= 'style';
	const SCRIPT	= 'script';
	
	function __construct( $input, $handle, $src, $settings = array() ) {
		
		// Make arguments supplied are valid
		if( !is_string( $input ) && !is_array( $input ) ) {
			exit( '$type is an invalid argument' );
		}
		if( is_string( $input ) ) {
			if( !is_string( $handle ) ) {
				exit( '$handle is an invalid argument' );
			}
			if( !is_string( $src ) ) {
				exit( '$src is an invalid argument' );
			}
			if( !is_array( $settings ) ) {
				exit( '$settings is an invalid argument' );
			}
		}
		
		// If input argument is a string make passed arguments into a valid associative array
		if( is_sting( $input ) ) {
			$file = $this->defaults;
			$file['type'] = $type;
			$file['handle'] = $handle;
			$file['src'] = $src;
			$file = array_merge( $file, $settings );
			array_push( $this->files, $file);
		} else {
			foreach( $input as $in ) {
				array_push( $this->files, array_merge( $this->defaults, $in ) );
			}
		}
		
		// If script SCRIPT_DEBUG enabled try to set development version of source file
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			foreach( $this->files as &$file ) {
				$file['src'] = $this->get_dev( $file['src'] );
			}
			unset( $file );
		}
		
		$this->register_files();
	}
	
	// Perform Wordpress file register on files 
	private function register_files() {
		if( $this->type === self::STYLE ) {
			wp_register_style( $this->handle, $this->src, $this->settings['deps'], $this->settings['vers'], $this->settings['media'] );
		} else if ( $this->type === self::SCRIPT ) {
			wp_register_script( $this->handle, $this->src, $this->settings['deps'], $this->settings['vers'], $this->settings['in_footer'] );
		}
	}
	
	// Run file enqueue function based on type 
	public function enqueue( $handle = false, $safe = false ) {
		if( $this->type === self::STYLE ) {
			$this->enqueue_style( $safe );
		} else if ( $this->type === self::SCRIPT ) {
			$this->enqueue_script( $safe );
		}
	}
	
	// Perform Wordpress enqueue style
	private function enqueue_style( $safe ) {
		if( $safe && !$this->url_exists( $this->src ) ) {
			return false;
		}
		wp_enqueue_style( $this->handle );
		return true;
	}
	
	// Perform Wordpress enqueue script
	private function enqueue_script( $safe ) {
		if( $safe && !$this->url_exists( $this->src ) ) {
			return false;
		}
		wp_enqueue_script( $this->handle );
		return true;
	}
	
	// Enqueue and perform Wordpress localize script
	public function localize( $object_name, $l10n, $safe = false ) {
		
		// Make arguments supplied are valid
		if( !is_string( $object_name ) ) {
			exit( '$object_name is an invalid argument' );
		}
		if( !is_array( $l10n ) ) {
			exit( '$l10n is an invalid argument' );
		}
		if( !is_bool( $safe ) ) {
			exit( '$safe is an invalid argument' );
		}
		
		if( $this->type == self::SCRIPT ) {
			if( $this->enqueue_script( $safe ) ) {
				wp_localize_script( $this->handle, $object_name, $l10n );
			}
		}
	}
	
	// Check to see if URL exists
	public function url_exists( $src ) {
		
		// Make arguments supplied are valid
		if( !is_string( $src ) ) {
			exit( '$src is an invalid argument' );
		}
		
		// Isolate path to file from URL
		$url_parts = parse_url( $src );
		$path = $url_parts['path'];
		
		// Check if file exists from reassembled path
		if( file_exists( $_SERVER['DOCUMENT_ROOT'] . $path ) ) {
			return true;
		} else {
			return false;
		}
		
	}
	
	// Return development version of source if exists
	public function get_dev( $src ) {
	
		// Make arguments supplied are valid
		if( !is_string( $src ) ) {
			exit( '$src is an invalid argument' );
		}
		
		// Isolate path to file component from URL
		$url_parts = parse_url( $src );
		$path = $url_parts['path'];
		
		// Split the path into parts
		$path_parts = pathinfo( $path );
		
		// Remove suffix from file name
		$pattern = '/\.' . self::MIN . '$/i';
		$file_name = preg_replace( $pattern, '', $path_parts['filename'] );
		
		// Reassemble path
		$dev_url = $url_parts['scheme'] . '://' . $url_parts['host'] . $path_parts['dirname'] . '/' . $file_name . '.' . $path_parts['extension'];
		
		// If developer version of source file exists set it as the source url
		if( $this->url_exists( $dev_url ) ) {
			return $dev_url;
		} else {
			return $src;
		}
		
	}
}
?>