<?php

/**
 * Curly_Stream_Memory_Seekable
 * 
 * Baseimplementation of the Seekable-Interface. Shared by the memory streams.
 *  
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Memory
 * @since 11.09.2009
 */
abstract class Curly_Stream_Memory_Seekable implements Curly_Stream_Seekable {
	
	/**
	 * @var string Internal memory data for this stream.
	 */
	protected $_data='';
	
	/**
	 * @var integer Length of the internal memory data for this stream.
	 */
	protected $_dataLen=0;
	
	/**
	 * @var integer The current position in the stream.
	 */
	protected $_pos=0;
	
	/**
	 * Seeks to a specified position in a datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Offset from the given position value
	 * @param integer Relative position value. One of the ORIGIN_* values.
	 */
	public function seek($offset, $origin=self::ORIGIN_CURRENT) {
		$offset=(int)$offset;
		switch($origin) {
			case self::ORIGIN_BEGIN:
				if($offset>$this->_dataLen) {
					throw new Curly_Stream_Exception('The offset value '.$offset.' is too big to seek to, '.($this->_dataLen).' is the biggest possible value for the begin seek origin.');
				}
				else if($offset<0) {
					throw new Curly_Stream_Exception('Invalid offset value '.$offset.' given. Only positive offset values are valid for the begin seek origin.');
				}
				$this->_pos=$offset;
				return;
			case self::ORIGIN_CURRENT:
				$newPos=$this->_pos+$offset;
				if($newPos>$this->_dataLen) {
					throw new Curly_Stream_Exception('The offset value '.$offset.' is too big to seek to, '.($this->_dataLen-$this->_pos).' is the biggest possible value for the current seek origin.');
				}
				else if($newPos<0) {
					throw new Curly_Stream_Exception('The offset value '.$offset.' is too small to seek to, '.(-$this->_pos).' is the smallest possible value for the current seek origin.');
				}
				$this->_pos=$newPos;
				return;
			case self::ORIGIN_END:
				if($offset>0) {
					throw new Curly_Stream_Exception('The offset value can only be negative for the end seek origin.');
				}
				else if(-$offset>$this->_dataLen) {
					throw new Curly_Stream_Exception('The offset value '.$offset.' is too small to seek to, '.(-$this->_dataLen).' is the smallest possible value for the end seek origin.');
				}
				$this->_pos=$this->_dataLen+$offset;
				return;
			default:
				throw new Curly_Stream_Exception('Invalid seek origin value '.$origin.' given');
		}
	}
	
	/**
	 * Returns the current offset to the beginning of the datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return integer
	 */
	public function tell() {
		return $this->_pos;
	}
	
}
