<?php 
class ThemeFile {
	private $type = false;
	private $handle = false;
	private $src = false;
	private $defaults = array(
		'deps'		=> array(),
		'vers'		=> false,
		'media'		=> 'all',
		'in_footer'	=> false
	);
	
	private $settings = array();
	
	const MIN		= 'min';
	const STYLE		= 'style';
	const SCRIPT	= 'script';
	
	function __construct( $type, $handle, $src, $settings = array() ) {
		
		// Make arguments supplied are valid
		if( !is_string( $type ) ) {
			exit( '$type is an invalid argument' );
		}
		if( !is_string( $handle ) ) {
			exit( '$handle is an invalid argument' );
		}
		if( !is_string( $src ) ) {
			exit( '$src is an invalid argument' );
		}
		if( !is_array( $settings ) ) {
			exit( '$settings is an invalid argument' );
		}
		
		// Merge user supplied arguments with defaults
		$this->type = $type;
		$this->handle = $handle;
		$this->src = $src;
		$this->settings = array_merge( $this->defaults, $settings );
		
		// If script SCRIPT_DEBUG enabled look for development version of source file
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$this->src = $this->dev_src( $src );
		}
		
		$this->register();
	}
	
	// Perform Wordpress file register 
	private function register() {
		if( $this->type === self::STYLE ) {
			wp_register_style( $this->handle, $this->src, $this->settings['deps'], $this->settings['vers'], $this->settings['media'] );
		} else if ( $this->type === self::SCRIPT ) {
			wp_register_script( $this->handle, $this->src, $this->settings['deps'], $this->settings['vers'], $this->settings['in_footer'] );
		}
	}
	
	// Run file enqueue function based on type 
	public function enqueue( $safe = false ) {
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
	public function dev_src( $src ) {
	
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