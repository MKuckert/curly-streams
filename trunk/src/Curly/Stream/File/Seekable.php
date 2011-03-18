<?php

/**
 * Curly_Stream_File_Input
 * 
 * Implements an inputstream with data read from a filehandle.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.File
 * @since 12.09.2009
 */
abstract class Curly_Stream_File_Seekable implements Curly_Stream_Seekable {
	
	/**
	 * @var resource Internal filehandle
	 */
	protected $_handle;
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		if(is_resource($this->_handle)) {
			fclose($this->_handle);
		}
	}
	
	/**
	 * Seeks to a specified position in a datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Offset from the given position value
	 * @param integer Relative position value. One of the ORIGIN_* values.
	 */
	public function seek($offset, $origin=self::ORIGIN_CURRENT) {
		static $originMapping=array(
			self::ORIGIN_BEGIN		=> SEEK_SET,
			self::ORIGIN_CURRENT	=> SEEK_CUR,
			self::ORIGIN_END		=> SEEK_END
		);
		
		if(!isset($originMapping[$origin])) {
			throw new Curly_Stream_Exception('Invalid origin value given');
		}
		
		if(fseek($this->_handle, $offset, $originMapping[$origin])!==0) {
			throw new Curly_Stream_Exception('Failed to seek to the specified position');
		}
	}
	
	/**
	 * Returns the current offset to the beginning of the datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return integer
	 */
	public function tell() {
		$pos=ftell($this->_handle);
		if($pos===false) {
			throw new Curly_Stream_Exception('An error occured while determining the current stream offset');
		}
		return $pos;
	}
	
}