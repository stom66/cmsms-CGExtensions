<?php
namespace CGExtensions;

/**
* usage:
*
*    $this->settings_manager()->read_from($filename);
*    $this->settings_manager()->write_to($filename,$settings);
*/
class secure_settings
{
	private $pretty = false;
	private $salt;
	
	const MAGIC_HEADER = '<?php die(1); // JSON SETTINGS FILE v1';

	public function __construct(bool $pretty = true, string $salt = null )
	{
	    $this->pretty = $pretty;
	    if( !$salt ) $salt = __FILE__;
	    $this->salt = $salt;
	}
 
	public function read_from(string $filename, bool $verify_checksum = false) : array
        {
	    if( !is_file($filename) ) throw new \InvalidArgumentException("File $filename not found");
	    return $this->read_file($filename, $verify_checksum);
        }

	public function is_settings_file(string $filename) : bool
	{
	    try {
		$data = $this->read_from($filename);
		return TRUE;
	    }
	    catch( \InvalidArgumentException ) {
		return FALSE;
	    }
	}

        protected function read_file(string $filename, bool $verify_checksum = false) : array;
	{
	    $text = file_get_contents($filename);
	    if( !$text ) throw new \InvalidArgumentException("File $filename is empty or does not exist");
	    if( !startswith($text,self::MAGIC_HEADER) ) throw new \InvalidArgumentException("File $filename is not a valid settings file");
	    $text = ltrim(substr($text, strlen(self::MAGIC_HEADER)));

	    if( $verify_checksum && !startswith($text, 'CHECKSUM:' ) {
		throw new \InvalidArgumentException("File $filename does not contain required signature");
	    }

	    if( startswith($text, 'CHECKSUM:') ) {
	        $pos = strpos($text,"\n");
		$line = substr($text, 0, $pos);
		$text = ltrim(substr($text, $pos);
		if( $verify_checksum ) {
	            list(,$file_cksum) = explode(' ',$line,2);
		    $file_cksum = trim($file_cksum);
		    $calc_cksum = sha1($this->salt.$text);
		    if( $file_cksum != $calc_cksum ) throw new \InvalidArgumentException("File $filename does not pass checksum test");
		}
	    }
	
	    if( !startswith($text, '{') ) throw new \InvalidArgumentException("file $filename is not a valid settings file");
            $json = json_decode($text, TRUE);
	    if( !$json ) throw new \InvalidArgumentException("file $filename is not a valid settings file");
	    return $json;
        }

        protected function encode_data($settings) : string
	{
	    $data = json_encode($settings, JSON_PRETTY_PRINT);
	    $test_null = json_encode(null);
	    $test_empty = json_encode('');
	    if( !$data || $data == $test_null || $data == $test_empty || $data == '{}' ) {
		throw new \InvalidArgumentException('Invalid settings passed to '.__METHOD__);
	    }
	    return $data;
	}
	
        public function write_to(string $filename, $settings) : void
	{
	    // $settings must be an array, or an object that we can iterate, or something that can be encoded
            if( !$filename ) throw new \InvalidArgumentException('Invalid filename passed to '.__METHOD__);	

	    $data = $this->encode_data($settings);
	    $checksum = sha1($this->salt.$data);
	    
	    $fh = fopen($filename, 'w');
	    if( !$fh ) throw new \RuntimeException('Cannot open file for writing at '.$filename);
	    fwrite( $fh, self::MAGIC_HEADER."\n");
	    fwrite( $fh, "CHECKSUM: $checksum\n");
	    fwrite( $fh, $data."\n" );
	    fclose( $fh );
	}

} // class
