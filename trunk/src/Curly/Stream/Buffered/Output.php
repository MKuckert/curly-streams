<?php

/**
 * Curly_Stream_Buffered_Output
 * 
 * Bufferes data to an outputstream internal, so lesser write operations are
 * required. Each write operation trys to write at least BUFFERSIZE bytes at
 * once into the datastream.
 * 
 * Note: Never modify the underlying outputstream directly. The resulting
 * behaviour is undefined.
 * 
 * Note: The buffersize should be twice as high as the longest write operation
 * length. E.g. if the call write(data with strlen(100)) is the longest write
 * operation in your code, use a buffersize value greater or equal to 200 to
 * expect the best performance gain.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Buffered
 * @since 11.09.2009
 */
class Curly_Stream_Buffered_Output implements Curly_Stream_Output {
	
	/**
	 * @desc Default size of the internal data buffer.
	 */
	const DEFAULT_BUFFERSIZE=2048;
	
	/**
	 * @var Curly_Stream_Output Buffered output stream.
	 */
	private $_stream;
	
	/**
	 * @var integer Maximal size of the internal buffer.
	 */
	protected $_maxSize=0;
	
	/**
	 * @var string Internal buffer.
	 */
	protected $_buffer='';
	
	/**
	 * @var integer The current size of the internal buffer.
	 */
	protected $_curSize=0;
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Output The output stream used for buffering
	 * @param integer Size of the internal buffer
	 */
	public function __construct(Curly_Stream_Output $stream, $buffersize=self::DEFAULT_BUFFERSIZE) {
		if($buffersize<=0) {
			throw new Curly_Stream_Exception('The buffersize has to be a positive non-zero value');
		}
		$this->_maxSize=(int)$buffersize;
		$this->_stream=$stream;
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		// Write the remaining data to the buffer
		if($this->_curSize>0) {
			$this->_stream->write($this->_buffer);
		}
	}
	
	/**
	 * Returns the capsuled output stream.
	 * 
	 * @return Curly_Stream_Output
	 */
	public function getStream() {
		return $this->_stream;
	}
	
	/**
	 * Writes all internally buffered data into the output stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		$this->_stream->write($this->_buffer);
		$this->_buffer='';
		$this->_curSize=0;
	}
	
	/**
	 * Writes the given data into the output stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		$dataLen=strlen($data);
		
		// All data goes into the buffer
		if($dataLen+$this->_curSize<$this->_maxSize) {
			$this->_buffer.=$data;
			$this->_curSize+=$dataLen;
			return;
		}
		
		// Write all data into the stream
		$this->_stream->write($this->_buffer.$data);
		
		$this->_buffer='';
		$this->_curSize=0;
	}
	
}