<?php

/**
 * Curly_Stream_Memory_Input
 * 
 * Implements an inputstream with data hold in memory.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Memory
 * @since 11.09.2009
 */
class Curly_Stream_Memory_Input extends Curly_Stream_Memory_Seekable implements Curly_Stream_Input {
	
	/**
	 * Constructor
	 * 
	 * @param string Memory data used for this stream
	 */
	public function __construct($data) {
		$this->_data=(string)$data;
		$this->_dataLen=strlen($this->_data);
	}
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		return $this->_pos+1<$this->_dataLen;
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
		
		if($len+$this->_pos>$this->_dataLen) {
			$retval=substr($this->_data, $this->_pos);
			$this->_pos=$this->_dataLen;
			return $retval;
		}
		
		$retval=substr($this->_data, $this->_pos, $len);
		$this->_pos+=strlen($retval);
		return $retval;
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
		
		$this->_pos+=$len;
		if($this->_pos>=$this->_dataLen) {
			$this->_pos=$this->_dataLen;
		}
	}
	
}