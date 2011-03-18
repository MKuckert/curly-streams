<?php

/**
 * Curly_Stream_Capsule_Output
 * 
 * Base implementation for a class that capsules another stream and writes data
 * into that one.
 * This implementation simply encapsulates a Curly_Stream_Output instance.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Capsule
 * @since 15.02.2010
 */
class Curly_Stream_Capsule_Output implements Curly_Stream_Output {
	
	/**
	 * @var Curly_Stream_Output The underlying stream for this writer.
	 */
	protected $_stream=NULL;
	
	/**
	 * Returns the underlying stream for this writer.
	 * 
	 * @return Curly_Stream_Output
	 */
	public function getStream() {
		return $this->_stream;
	}
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param Curly_Stream_Output
	 */
	public function __construct(Curly_Stream_Output $stream) {
		$this->_stream=$stream;
	}
	
	/**
	 * Writes any buffered data into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		$this->_stream->flush();
	}
	
	/**
	 * Writes the given elements into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		$this->_stream->write($data);
	}
	
}