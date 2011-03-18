<?php

/**
 * Curly_Stream_Buffered_Input
 * 
 * Bufferes data of an inputstream internal, so lesser read operations are
 * required. Each read operation trys to read at least BUFFERSIZE bytes at
 * once out of the datastream.
 * 
 * Note: Never modify the underlying inputstream directly. The resulting
 * behaviour is undefined.
 * 
 * Note: The buffersize should be twice as high as the longest read operation
 * length. E.g. if the call read(100) is the longest read operation in your
 * code, use a buffersize value greater or equal to 200 to expect the best
 * performance gain.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Buffered
 * @since 11.09.2009
 */
class Curly_Stream_Buffered_Input extends Curly_Stream_Capsule_Input {
	
	/**
	 * @desc Default size of the internal data buffer.
	 */
	const DEFAULT_BUFFERSIZE=2048;
	
	/**
	 * @var integer Maximal size of the internal buffer.
	 */
	private $_maxSize=0;
	
	/**
	 * @var string Internal buffer.
	 */
	private $_buffer='';
	
	/**
	 * @var integer The current size of the internal buffer.
	 */
	private $_curSize=0;
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param Curly_Stream_Input The input stream used for buffering
	 * @param integer Size of the internal buffer
	 */
	public function __construct(Curly_Stream_Input $stream, $buffersize=self::DEFAULT_BUFFERSIZE) {
		if($buffersize<=0) {
			throw new Curly_Stream_Exception('The buffersize has to be a positive non-zero value');
		}
		$this->_maxSize=(int)$buffersize;
		parent::__construct($stream);
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
		if($this->_curSize==$len) {
			$this->_curSize=0;
			$retval=$this->_buffer;
			$this->_buffer='';
			return $retval;
		}
		// Not enough data in buffer
		else if($this->_curSize<$len) {
			// Fill the buffer
			$this->_buffer.=$this->_stream->read($this->_maxSize);
			$this->_curSize=strlen($this->_buffer);
			
			// More data than buffersize requested
			if($len>$this->_curSize) {
				$retval=
					$this->_buffer
					.$this->_stream->read($len-$this->_curSize);
				$this->_buffer='';
				$this->_curSize=0;
				return $retval;
			}
		}
		
		$retval=substr($this->_buffer, 0, $len);
		$this->_buffer=substr($this->_buffer, $len);
		$this->_curSize-=$len;
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
		if($this->_curSize==$len) {
			$this->_buffer='';
			$this->_curSize=0;
		}
		// Not enough data in buffer
		else if($this->_curSize<$len) {
			$bufLen=$this->_maxSize+$this->_curSize;
			// More data than buffersize requested
			if($len>$bufLen) {
				$this->_stream->skip($len-$bufLen);
				$this->_buffer='';
				$this->_curSize=0;
				return;
			}
			
			$this->_stream->skip($len-$this->_curSize);
			$this->_buffer=$this->_stream->read($this->_maxSize);
			$this->_curSize=strlen($this->_buffer);
		}
		// Some data in the buffer
		else {
			$this->_buffer=substr($this->_buffer, $len);
			$this->_curSize-=$len;
		}
	}
	
}