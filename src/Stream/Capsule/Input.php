<?php

/**
 * Curly_Stream_Capsule_Input
 * 
 * Base implementation for a class that capsules another stream and reads data
 * of that one.
 * This implementation simply encapsulates a Curly_Stream_Input instance.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Capsule
 * @since 11.10.2009
 */
class Curly_Stream_Capsule_Input implements Curly_Stream_Input {
	
	/**
	 * @var Curly_Stream_Input The source stream for this reader.
	 */
	protected $_stream=NULL;
	
	/**
	 * Returns the source stream for this reader.
	 * 
	 * @return Curly_Stream_Input
	 */
	public function getStream() {
		return $this->_stream;
	}
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Input
	 */
	public function __construct(Curly_Stream_Input $stream) {
		$this->_stream=$stream;
	}
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		return $this->_stream->available();
	}
	
	/**
	 * Reads data out of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param integer Number of elements to read
	 */
	public function read($len) {
		return $this->_stream->read($len);
	}
	
	/**
	 * Skips data of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Number of elements to skip
	 */
	public function skip($len) {
		$this->_stream->skip($len);
	}
	
}