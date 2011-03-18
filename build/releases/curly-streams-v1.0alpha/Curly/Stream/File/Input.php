<?php

/**
 * Curly_Stream_File_Input
 * 
 * Implements an inputstream with data read from a filehandle.
 * 
 * Note: The seek operation of the underlying function may not properly set the
 * end-of-file flag. So it may occur that you seek to the end of the stream
 * (e.g. seek(0, ORIGIN_END)), but the available method returns true. A read
 * operation of any count of bytes correctly sets the eof flag, so available
 * returns false after that. You have to keep track of that your own.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.File
 * @since 12.09.2009
 */
class Curly_Stream_File_Input extends Curly_Stream_File_Seekable implements Curly_Stream_Input {
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param string Path to the file
	 */
	public function __construct($filepath) {
		$filepath=(string)$filepath;
		if(!file_exists($filepath)) {
			throw new Curly_Stream_Exception('The file '.$filepath.' does not exist');
		}
		else if(!is_readable($filepath)) {
			throw new Curly_Stream_Exception('The file '.$filepath.' is not readable');
		}
		
		$this->_handle=fopen($filepath, 'rb');
		if(!is_resource($this->_handle)) {
			throw new Curly_Stream_Exception('Failed to open an inputstream to the file '.$filepath);
		}
	} // end of ctr
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		return !feof($this->_handle);
	}
	
	/**
	 * Reads data out of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param integer Number of elements to read
	 */
	public function read($len) {
		if($len<0) {
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a read operation. Only positive values area valid.');
		}
		else if($len===0) {
			return '';
		}
		
		$read=fread($this->_handle, $len);
		if($read===false) {
			throw new Curly_Stream_Exception('An error occured while reading data from the input stream');
		}
		
		return $read;
	}
	
	/**
	 * Skips data of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Number of elements to skip
	 */
	public function skip($len) {
		if($len<0) {
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a skip operation. Only positive values area valid.');
		}
		
		$this->read($len);
	}
	
}