<?php

/**
 * Curly_Stream_Buffered_Input
 * 
 * Bufferes data of an inputstream internal, so lesser read operations are
 * required. Each read operation reads at least {@link BUFFERSIZE} bytes at
 * once out of the datastream.
 * 
 * Note: Never modify the underlying inputstream directly. The resulting
 * behaviour is undefined.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Buffered
 * @since 11.09.2009
 */
class Curly_Stream_Buffered_Input implements Curly_Stream_Input {
	
	/**
	 * @desc Size of the internal data buffer.
	 */
	const BUFFERSIZE=2048;
	
	/**
	 * @var Curly_Stream_Input Buffered input stream.
	 */
	private $_stream;
	
	/**
	 * @var string Internal buffer.
	 */
	private $_buffer='';
	
	/**
	 * @var integer The current size of the internal buffer.
	 */
	private $_bufLen=0;
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Input The input stream used for buffering
	 */
	public function __construct(Curly_Stream_Input $stream) {
		$this->_stream=$stream;
	}
	
	/**
	 * Returns the capsuled input stream.
	 * 
	 * @return Curly_Stream_Input
	 */
	public function getCapsuledStream() {
		return $this->_stream;
	}
	
	/**
	 * Checks if any data is available for reading.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		return $this->_buffer!=='' or $this->_stream->available();
	}
	
	/**
	 * Reads some elements from this datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param integer
	 */
	public function read($len) {
		if($len<0) {
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a read operation. Only positive values area valid.');
		}
		
		// Exactly the right amount of data available
		if($this->_bufLen==$len) {
			$this->_bufLen=0;
			$retval=$this->_buffer;
			$this->_buffer='';
			return $retval;
		}
		// Not enough data in buffer
		else if($this->_bufLen<$len) {
			$bufLen=self::BUFFERSIZE+$this->_bufLen;
			// More data than buffersize requested
			if($len>$bufLen) {
				$retval=
					$this->_buffer
					.$this->_stream->read($len-$bufLen);
				$this->_buffer='';
				$this->_bufLen=0;
				return $retval;
			}
			
			// Fill the buffer up with one read operation
			$this->_buffer.=$this->_stream->read(self::BUFFERSIZE);
			$this->_bufLen=strlen($this->_buffer);
		}
		
		$retval=substr($this->_buffer, 0, $len);
		$this->_buffer=substr($this->_buffer, $len);
		$this->_bufLen-=$len;
		return $retval;
	}
	
	/**
	 * Skips some elements from this datastream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer
	 */
	public function skip($len) {
		if($len<0) {
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a skip operation. Only positive values area valid.');
		}
		
		// Exactly the right amount of data available
		if($this->_bufLen==$len) {
			$this->_buffer='';
			$this->_bufLen=0;
		}
		// Not enough data in buffer
		else if($this->_bufLen<$len) {
			$bufLen=self::BUFFERSIZE+$this->_bufLen;
			// More data than buffersize requested
			if($len>$bufLen) {
				$this->_stream->skip($len-$bufLen);
				$this->_buffer='';
				$this->_bufLen=0;
				return;
			}
			
			$this->_stream->skip($len-$this->_bufLen);
			$this->_buffer=$this->_stream->read(self::BUFFERSIZE);
			$this->_bufLen=strlen($this->_buffer);
		}
		// Some data in the buffer
		else {
			$this->_buffer=substr($this->_buffer, $len);
			$this->_bufLen-=$len;
		}
	}
	
}