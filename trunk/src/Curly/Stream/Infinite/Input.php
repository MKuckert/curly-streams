<?php

/**
 * Curly_Stream_Infinite_Input
 * 
 * A stream reading the data of an underlying stream in an infinite loop.
 * The underlying stream has to implement the {@link Curly_Stream_Seekable} interface.
 * 
 * This stream returns always true for each {@method available}-call, except
 * the underlying stream returns false after seeking back to the first position
 * of this stream.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Infinite
 * @since 12.11.2009
 */
class Curly_Stream_Infinite_Input extends Curly_Stream_Capsule_Input {
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param Curly_Stream_Input
	 */
	public function __construct(Curly_Stream_Input $stream) {
		if(!($stream instanceof Curly_Stream_Seekable)) {
			throw new Curly_Stream_Exception('The underlying input stream has to implement the seekable interface');
		}
		parent::__construct($stream);
	}
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		if(!$this->_stream->available()) {
			$this->_stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
			return $this->_stream->available();
		}
		else {
			return true;
		}
	}
	
	/**
	 * Reads data out of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param integer Number of elements to read
	 */
	public function read($len) {
		$data='';
		$dataLen=0;
		$seeked=false;
		
		do {
			$readData=$this->_stream->read($len-$dataLen);
			
			if($readData=='') {
				// Don't seek twice to avoid a real infinite loop
				if($seeked) {
					break;
				}
				
				$this->_stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
				$seeked=true;
				continue;
			}
			
			$seeked=false;
			
			$data.=$readData;
			$dataLen=strlen($data);
		}
		while($dataLen<$len);
		
		return $data;
	}
	
	/**
	 * Skips data of this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param integer Number of elements to skip
	 */
	public function skip($len) {
		$this->read($len);
	}
	
}