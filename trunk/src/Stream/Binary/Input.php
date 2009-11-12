<?php

/**
 * Curly_Stream_Binary_Input
 * 
 * Reads the data of a stream in a binary way.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Binary
 * @since 11.10.2009
 */
class Curly_Stream_Binary_Input extends Curly_Stream_Capsule_Input {
	
	/**#@+
	 * @desc Possible values for the endian setting of this class.
	 */
	const ENDIAN_LITTLE=true;
	const ENDIAN_BIG=false;
	/**#@-*/
	
	/**
	 * @var boolean True if the little endian byte order should been used.
	 */
	private $_littleEndian=self::ENDIAN_LITTLE;
	
	/**
	 * Checks whether this reader uses little endian byte order.
	 * 
	 * @return boolean
	 */
	public function getUseLittleEndian() {
		return $this->_littleEndian;
	}
	
	/**
	 * Sets the flag to use little endian byte order or removes it.
	 * 
	 * @return Curly_Stream_Binary_Reader
	 * @param boolean
	 */
	public function setUseLittleEndian($flag=true) {
		$this->_littleEndian=(bool)$flag;
	}
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Input
	 * @param boolean Flag to use little endian byte order
	 */
	public function __construct(Curly_Stream_Input $stream, $littleEndian=self::ENDIAN_LITTLE) {
		parent::__construct($stream);
		$this->setUseLittleEndian($littleEndian);
	}
	
	/**
	 * Reads a single byte from the underlying stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return integer The byte read from the stream or NULL on error
	 */
	public function readByte() {
		$buffer=$this->read(1);
		if(!isset($buffer[0])) {
			return NULL;
		}
		else {
			$tmp=unpack('C', $buffer);
			return reset($tmp);
		}
	}
	
	/**
	 * Reads a 16 bit unsigned short from the underlying stream.
	 *
	 * @throws Curly_Stream_Exception
	 * @return integer or NULL on error
	 */
	public function readShort() {
		$buffer=$this->read(2);
		if(!isset($buffer[1])) {
			return NULL;
		}
		
		$ret=0;
		if($this->getUseLittleEndian()) {
			$ret=unpack('v', $buffer);
		}
		else {
			$ret=unpack('n', $buffer);
		}
		return reset($ret);
	}
	
	/**
	 * Reads a 32 bit unsigned integer from the underlying stream.
	 *
	 * @throws Curly_Stream_Exception
	 * @return integer or NULL on error
	 */
	public function readInteger() {
		$buffer=$this->read(4);
		if(!isset($buffer[3])) {
			return NULL;
		}
		
		$ret=0;
		if($this->getUseLittleEndian()) {
			$ret=unpack('V', $buffer);
		}
		else {
			$ret=unpack('N', $buffer);
		}
		return reset($ret);
	}
	
}