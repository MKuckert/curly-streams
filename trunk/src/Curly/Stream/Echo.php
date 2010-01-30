<?php

/**
 * Curly_Stream_Echo
 * 
 * Implements a stream directly printing every data to the standard output.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 09.10.2009
 */
class Curly_Stream_Echo implements Curly_Stream_Output {
	
	/**
	 * Writes any buffered data into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		// Nothing to do here
	}
	
	/**
	 * Writes the given elements into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		echo $data;
	}
	
}