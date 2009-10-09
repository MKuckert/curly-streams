<?php

/**
 * Curly_Stream_Append_Input
 * 
 * Combines different input streams to one appended stream.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Append
 * @since 13.09.2009
 */
class Curly_Stream_Append_Input implements Curly_Stream_Input {
	
	/**
	 * @var array List of all input streams.
	 */
	private $_streams=array();
	
	/**
	 * @var integer Length of the stream list.
	 */
	private $_streamsCount=0;
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Input First stream to use.
	 */
	public function __construct(Curly_Stream_Input $stream) {
		$this->append($stream);
	}
	
	/**
	 * Appends the given input stream to this stream.
	 * 
	 * @return Curly_Stream_Append_Input
	 * @param Curly_Stream_Input
	 */
	public function append(Curly_Stream_Input $stream) {
		$this->_streams[]=$stream;
		$this->_streamsCount++;
		return $this;
	}
	
	/**
	 * Removes the first input stream from this stream.
	 * 
	 * @return void
	 */
	protected function shift() {
		array_shift($this->_streams);
		$this->_streamsCount--;
	}
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		// All streams were read
		if($this->_streamsCount===0) {
			return false;
		}
		
		// The current stream has more data
		if(reset($this->_streams)->available()) {
			return true;
		}
		
		// Check the other streams, if they have data
		foreach($this->_streams as $stream) {
			if($stream->available()) {
				return true;
			}
		}
		
		return false;
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
		
		$read='';
		$readLen=0;
		
		// Read from every stream, until we have enough data
		foreach($this->_streams as $stream) {
			$read.=$stream->read($len-$readLen);
			
			// Enough data read
			$readLen=strlen($read);
			if($readLen==$len) {
				return $read;
			}
			
			$this->shift();
		}
		
		// So, not enough data found, but return what we´ve got.
		return $read;
	}
	
	/**
	 * Skips data of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Number of elements to skip
	 */
	public function skip($len) {
		// Do a read operation instead, because there is no way to find out how
		// many elements were really skipped
		$this->read($len);
	}
	
}