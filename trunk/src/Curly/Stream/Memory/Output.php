<?php

/**
 * Curly_Stream_Memory_Output
 * 
 * Implements an outputstream which holds his data in memory.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Memory
 * @since 11.09.2009
 */
class Curly_Stream_Memory_Output extends Curly_Stream_Memory_Seekable implements Curly_Stream_Output {
	
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