<?php

/**
 * Curly_Stream_Base64_Encode_Input
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 11.09.2009
 */
class Curly_Stream_Base64_Encode_Input extends Curly_Stream_Buffered_Input {
	
	/**
	 * Reads data out of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param integer Number of elements to read
	 */
	public function read($len) {
		$read=parent::read($len);
		
		if($this->available()) {
			$overlap=strlen($read)%3;
			if($overlap>0) {
				$this->_buffer=substr($read, -$overlap).$this->_buffer;
				$this->_curSize+=$overlap;
				$read=substr($read, 0, -$overlap);
			}
		}
		
		return base64_encode($read);
	}
	
}