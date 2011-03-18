<?php

/**
 * Curly_Stream_Binary_Output
 * 
 * Writes the data in a binary way into a stream.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Binary
 * @since 15.02.2010
 */
class Curly_Stream_Binary_Output extends Curly_Stream_Capsule_Output {
	
	/**
	 * @var boolean True if the little endian byte order should been used.
	 */
	private $_littleEndian=Curly_Stream::ENDIAN_LITTLE;
	
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
	 * @param Curly_Stream_Output
	 * @param boolean Flag to use little endian byte order
	 */
	public function __construct(Curly_Stream_Output $stream, $littleEndian=Curly_Stream::ENDIAN_LITTLE) {
		parent::__construct($stream);
		$this->setUseLittleEndian($littleEndian);
	}
	
	/**
	 * Writes a byte into the stream
	 *
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer
	 */
	public function writeByte($value) {
		$value=(int)$value;
		if($value<0 or $value>0xFF) {
			throw new Curly_Stream_Exception('The given value is no valid byte (0-255)');
		}
		
		$this->write(pack('C', $value));
	}
	
	/**
	 * Writes a 16 bit unsigned short from the underlying stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer
	 */
	public function writeShort($value) {
		$value=(int)$value;
		
		if($this->getUseLittleEndian()) {
			$this->write(pack('v', $value));
		}
		else {
			$this->write(pack('n', $value));
		}
	}
	
	/**
	 * Writes an integer into the data stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer
	 */
	public function writeInteger($value) {
		$value=(int)$value;
		
		if($this->getUseLittleEndian()) {
			$this->write(pack('V', $value));
		}
		else {
			$this->write(pack('N', $value));
		}
	}
	
}
