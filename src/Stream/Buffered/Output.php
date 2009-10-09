<?php

/**
 * Curly_Stream_Buffered_Output
 * 
 * Bufferes data to an outputstream internal, so lesser write operations are
 * required. Each write operation writes at least {@link BUFFERSIZE} bytes at
 * once into the datastream.
 * 
 * Note: Never modify the underlying outputstream directly. The resulting
 * behaviour is undefined.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Buffered
 * @since 11.09.2009
 */
class Curly_Stream_Buffered_Output implements Curly_Stream_Output {
	
	/**
	 * @desc Size of the internal data buffer.
	 */
	const BUFFERSIZE=2048;
	
	/**
	 * @var Curly_Stream_Output Buffered output stream.
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
	 * @param Curly_Stream_Output The output stream used for buffering
	 */
	public function __construct(Curly_Stream_Output $stream) {
		$this->_stream=$stream;
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		// Write the remaining data to the buffer
		if($this->_bufLen>0) {
			$this->_stream->write($this->_buffer);
		}
	}
	
	/**
	 * Returns the capsuled output stream.
	 * 
	 * @return Curly_Stream_Output
	 */
	public function getCapsuledStream() {
		return $this->_stream;
	}
	
	/**
	 * Schreibt alle intern gepufferten Daten in den Ausgabestrom.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		$this->_stream->write($this->_buffer);
		$this->_buffer='';
		$this->_bufLen=0;
	}
	
	/**
	 * Schreibt die übergebenen Elemente in diesen Ausgabestrom.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		$dataLen=strlen($data);
		
		// All data goes into the buffer
		if($dataLen+$this->_bufLen<self::BUFFERSIZE) {
			$this->_buffer.=$data;
			$this->_bufLen+=$dataLen;
			return;
		}
		
		// Write all data into the stream
		$this->_stream->write($this->_buffer.$data);
		
		$this->_buffer='';
		$this->_bufLen=0;
	}
	
}