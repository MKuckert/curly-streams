<?php

/**
 * Curly_Stream_Wrapper
 * 
 * Proxy class to use any stream object as a php stream wrapper.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 08.11.2009
 */
class Curly_Stream_Wrapper {
	
	/**
	 * @var resource
	 */
	public $context=NULL;
	
	/**
	 * @var Curly_Stream_Input|Curly_Stream_Output
	 */
	private $_instance=NULL;
	
	/**
	 * @var boolean Flag if this stream can be used for read operations.
	 */
	private $_readable=false;
	
	/**
	 * @var boolean Flag if this stream can be used for write operations.
	 */
	private $_writable=false;
	
	/**
	 * @var boolean Flag if errors should be reported.
	 */
	private $_reportErrors=false;
	
	/**
	 * Triggers an error if the {@link _reportErrors}-Flag is set and returns
	 * false.
	 * 
	 * @return boolean
	 * @param string
	 */
	protected function triggerError($msg) {
		if($this->_reportErrors) {
			trigger_error($msg);
		}
		return false;
	}
	
	/**
	 * Opens this wrapper
	 * 
	 * @return boolean true if the stream was successfully opened.
	 * @param string The path to use
	 * @param string The opening mode for this stream
	 * @param integer More options as a bit field. Following values are
	 *  possible:
	 * 	 - STREAM_USE_PATH: If $path is relative and the include_path
	 * 						configuration should be used. If the stream was
	 * 						opened correctly the 4th parameter should be set to
	 * 						the fully resolved path.
	 *   - STREAM_REPORT_ERRORS: If this flag is set every error should be
	 * 						triggered using {@link trigger_error}.
	 * @param string 
	 */
	public function stream_open($path, $mode, $options/*, $opened_path*/) {
		$this->_reportErrors=(($options & STREAM_REPORT_ERRORS)===STREAM_REPORT_ERRORS);
		
		// Determine protocol
		$pos=strpos($path, ':');
		if($pos===false) {
			return $this->triggerError('Invalid path given. No protocol was found');
		}
		
		$proto=substr($path, 0, $pos);
		
		// Extract parameter and remove trailing slashes
		$params=substr($path, $pos+3);
		
		// Resolve registry
		$registry=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		if(!$registry->isRegisteredName($proto)) {
			return $this->triggerError('No stream instance or class was found for the given protocol '.$proto);
		}
		
		// Resolve stream
		$stream=$registry->getByName($proto, true);
		if(is_string($stream)) {
			try {
				$stream=new $stream($params, $mode);
			}
			catch(Curly_Stream_Exception $ex) {
				return $this->triggerError('Error while opening stream: '.$ex->getMessage());
			}
		}
		
		$this->_instance=$stream;
		if($stream instanceof Curly_Stream_Input) {
			$this->_readable=true;
		}
		if($stream instanceof Curly_Stream_Output) {
			$this->_writable=true;
		}
		
		return true;
	}
	
	/**
	 * Closes this stream wrapper.
	 * 
	 * @return void
	 */
	public function stream_close() {
		$this->_instance=NULL;
	}
	
	/**
	 * Ensures this stream is readable.
	 * 
	 * @return boolean
	 */
	public function ensureReadable() {
		if($this->_instance===NULL) {
			return $this->triggerError('This stream has been closed and is not readable anymore');
		}
		if(!$this->_readable) {
			return $this->triggerError('This stream is not readable');
		}
		return true;
	}
	
	/**
	 * Ensures this stream is writable.
	 * 
	 * @return boolean
	 */
	public function ensureWritable() {
		if($this->_instance===NULL) {
			return $this->triggerError('This stream has been closed and is not writable anymore');
		}
		if(!$this->_writable) {
			return $this->triggerError('This stream is not writable');
		}
		return true;
	}
	
	/**
	 * Returns $count or less bytes out of the stream.
	 * 
	 * @return string
	 * @param integer
	 */
	public function stream_read($count) {
		if(!$this->ensureReadable()) {
			return '';
		}
		
		try {
			$read=$this->_instance->read($count);
		}
		catch(Curly_Stream_Exception $ex) {
			return $this->triggerError('Error while reading from stream: '.$ex->getMessage());
		}
		
		return $read;
	}
	
	/**
	 * Writes the given data into the stream and returns the count of written
	 * bytes.
	 * 
	 * @return integer Count of written bytes.
	 * @param string Data to write
	 */
	public function stream_write($data) {
		if(!$this->ensureWritable()) {
			return 0;
		}
		
		try {
			$this->_instance->write($data);
		}
		catch(Curly_Stream_Exception $ex) {
			return (int)$this->triggerError('Error while writing to stream: '.$ex->getMessage());
		}
		
		return strlen($data);
	}
	
	/**
	 * Returns a boolean value if the stream contains no more data.
	 * 
	 * @return boolean
	 */
	public function stream_eof() {
		if(!$this->ensureReadable()) {
			return false;
		}
		
		return !$this->_instance->available();
	}
	
	/**
	 * Returns the current read or write position if possible.
	 * 
	 * @return integer
	 */
	public function stream_tell() {
		if($this->_instance===NULL or !($this->_instance instanceof Curly_Stream_Seekable)) {
			return (int)$this->triggerError('This stream is not seekable');
		}
		
		return $this->_instance->tell();
	}
	
	/**
	 * Changes the current read or write position to the given value.
	 * 
	 * @return boolean true on success
	 * @param integer Offset value
	 * @param integer Reference position
	 */
	public function stream_seek($offset, $whence) {
		if($this->_instance===NULL or !($this->_instance instanceof Curly_Stream_Seekable)) {
			return (int)$this->triggerError('This stream is not seekable');
		}
		
		static $mapping=array(
			SEEK_SET => Curly_Stream_Seekable::ORIGIN_BEGIN,
			SEEK_CUR => Curly_Stream_Seekable::ORIGIN_CURRENT,
			SEEK_END => Curly_Stream_Seekable::ORIGIN_END
		);
		
		if(!isset($mapping[$whence])) {
			return false;
		}
		
		try {
			$this->_instance->seek($offset, $mapping[$whence]);
		}
		catch(Curly_Stream_Exception $ex) {
			return (int)$this->triggerError('Error while seeking to the specified position: '.$ex->getMessage());
		}
		
		return true;
	}
	
	/**
	 * Clears all data buffers.
	 * 
	 * @return boolean true on success
	 */
	public function stream_flush() {
		if(!$this->ensureWritable()) {
			return false;
		}
		
		try {
			$this->_instance->flush();
		}
		catch(Curly_Stream_Exception $ex) {
			return $this->triggerError('Error while flushing the stream: '.$ex->getMessage());
		}
		
		return true;
	}
	
	/**
	 * Returns statistics to this stream.
	 * 
	 * @return array
	 */
	public function stream_stat() {
		$stats=array(
			'dev' => -1,
			'ino' => -1,
			'mode' => -1,
			'nlink' => -1,
			'uid' => -1,
			'gid' => -1,
			'rdev' => -1,
			'size' => -1,
			'atime' => -1,
			'mtime' => -1,
			'ctime' => -1,
			'blksize' => -1,
			'blocks' => -1
		);
		return array_merge(array_values($stats), $stats);
	}
	
}