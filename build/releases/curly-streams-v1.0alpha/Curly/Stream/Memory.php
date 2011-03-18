<?php

/**
 * Curly_Stream_Memory
 * 
 * Implements an stream with data hold in memory.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Memory
 * @since 12.09.2009
 */
class Curly_Stream_Memory extends Curly_Stream_Memory_Seekable implements Curly_Stream {
	
	/**
	 * Constructor
	 * 
	 * @param string Memory data used for this stream
	 */
	public function __construct($data='') {
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
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a read operation. Only positiv values area valid.');
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
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a skip operation. Only positiv values area valid.');
		}
		
		$this->_pos+=$len;
		if($this->_pos>=$this->_dataLen) {
			$this->_pos=$this->_dataLen;
		}
	}
	
	/**
	 * Returns the internal in memory data of this stream.
	 * 
	 * @return string
	 */
	public function getBuffer() {
		return $this->_data;
	}
	
	/**
	 * Writes any buffered data into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		// Simply nothing to do here
	}
	
	/**
	 * Writes the given elements into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		$dataLen=strlen($data);
		
		// The pointer is at the end of the internal memory, so just append the data
		if($this->_pos+1==$this->_dataLen) {
			$this->_data.=$data;
			$this->_dataLen+=$dataLen;
			$this->_pos+=$dataLen;
			return;
		}
		
		$this->_data=
			substr($this->_data, 0, $this->_pos).
			$data.
			substr($this->_data, $this->_pos+$dataLen);
		
		$this->_pos+=$dataLen;
		$this->_dataLen=strlen($this->_data);
	}
	
}