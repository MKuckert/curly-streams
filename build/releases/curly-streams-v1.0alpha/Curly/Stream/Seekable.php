<?php

/**
 * Curly_Stream_Seekable
 * 
 * Extends a datastream by a seek operation, so a certain position can directly
 * being reached.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 11.09.2009
 */
interface Curly_Stream_Seekable {
	
	/**#@+
	 * @desc Possible position values for a seek operation.
	 */
	const ORIGIN_BEGIN=1;
	const ORIGIN_CURRENT=2;
	const ORIGIN_END=3;
	/**#@-*/
	
	/**
	 * Seeks to a specified position in a datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Offset from the given position value
	 * @param integer Relative position value. One of the ORIGIN_* values.
	 */
	public function seek($offset, $origin=self::ORIGIN_CURRENT);
	
	/**
	 * Returns the current offset to the beginning of the datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return integer
	 */
	public function tell();
	
}