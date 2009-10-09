<?php

/**
 * Curly_Stream_File
 * 
 * Implements an stream to read and write data to a file.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.File
 * @since 12.09.2009
 */
class Curly_Stream_File extends Curly_Stream_File_Seekable implements Curly_Stream {
	
	/**
	 * @desc Truncates the underlying file before opening the stream.
	 */
	const TRUNCATE=1;
	
	/**
	 * @desc Creates the file, if it doesn´t exist yet.
	 */
	const CREATE=2;
	
	/**
	 * @desc Opens an existing file.
	 */
	const OPEN=4;
	
	/**
	 * @desc Opens an existing file after truncating it or creates a new file.
	 * Combination of TRUNCATE, OPEN and CREATE just for convenience.
	 */
	const CLEAN=7;
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param string Path to the file
	 * @param integer Opening modes. Default is CREATE|OPEN
	 */
	public function __construct($filepath, $mode=NULL) {
		if($mode===NULL) {
			$mode=Curly_Stream_File::CREATE|Curly_Stream_File::OPEN;
		}
		
		$filepath=(string)$filepath;
		if(($mode & Curly_Stream_File::OPEN)===Curly_Stream_File::OPEN) {
			// File does not exist
			if(($mode & Curly_Stream_File::CREATE)!==Curly_Stream_File::CREATE and !file_exists($filepath)) {
				throw new Curly_Stream_Exception('The file '.$filepath.' does not exist and can not been opened');
			}
			
			if(($mode & Curly_Stream_File::TRUNCATE)===Curly_Stream_File::TRUNCATE) {
				$mode='w+';
			}
			else {
				$mode='a+';
			}
		}
		else if(($mode & Curly_Stream_File::CREATE)===Curly_Stream_File::CREATE) {
			$mode='x+';
		}
		else {
			throw new Curly_Stream_Exception('Invalid open mode given. At least the open or create mode has to be specified');
		}
		
		$this->_handle=fopen($filepath, $mode.'b');
		if(!is_resource($this->_handle)) {
			throw new Curly_Stream_Exception('Failed to open an stream to the file '.$filepath);
		}
	}
	
	/**
	 * Checks if more data is available in this stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return boolean
	 */
	public function available() {
		return !feof($this->_handle);
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
		else if($len===0) {
			return '';
		}
		
		$read=fread($this->_handle, $len);
		if($read===false) {
			throw new Curly_Stream_Exception('An error occured while reading data from the input stream');
		}
		
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
		if($len<0) {
			throw new Curly_Stream_Exception('The given length '.$len.' is invalid for a skip operation. Only positiv values area valid.');
		}
		
		$this->read($len);
	}
	
	/**
	 * Writes any buffered data into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		fflush($this->_handle);
	}
	
	/**
	 * Writes the given elements into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 * @param string
	 */
	public function write($data) {
		$res=fwrite($this->_handle, $data);
		if(!$res) {
			throw new Curly_Stream_Exception('An error occured while writing data into the output stream');
		}
		else if($res!==strlen($data)) {
			throw new Curly_Stream_Exception('Not all data was written to the output stream. Just '.$res.' bytes were written');
		}
	}
	
}