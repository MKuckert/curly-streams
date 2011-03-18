<?php

/**
 * Curly_Stream_File_Output
 * 
 * Implements an outputstream to write data to a file.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.File
 * @since 12.09.2009
 */
class Curly_Stream_File_Output extends Curly_Stream_File_Seekable implements Curly_Stream_Output {
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param string Path to the file
	 * @param mixed Opening modes. Default is CREATE|OPEN
	 */
	public function __construct($filepath, $mode=NULL) {
		if($mode===NULL) {
			$mode=Curly_Stream_File::CREATE|Curly_Stream_File::OPEN;
		}
		
		$filepath=(string)$filepath;
		
		if(is_numeric($mode)) {
			$mode=$this->determineMode($filepath, (int)$mode);
		}
		else {
			$mode=trim($mode, 'b');
			$validModes=array('r+', 'w'. 'w+', 'a', 'a+', 'x', 'x+');
			if(!in_array($mode, $validModes)) {
				throw new Curly_Stream_Exception('Invalid mode '.$mode.' given');
			}
		}
		
		$this->_handle=fopen($filepath, $mode.'b');
		if(!is_resource($this->_handle)) {
			throw new Curly_Stream_Exception('Failed to open an outputstream to the file '.$filepath);
		}
	} // end of ctr
	
	/**
	 * Determines the mode for the fopen call, based on the different mode
	 * constants.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return string
	 * @param string Path to the file
	 * @param integer
	 */
	protected function determineMode($filepath, $mode) {
		if(($mode & Curly_Stream_File::OPEN)===Curly_Stream_File::OPEN) {
			// File does not exist
			if(($mode & Curly_Stream_File::CREATE)!==Curly_Stream_File::CREATE and !file_exists($filepath)) {
				throw new Curly_Stream_Exception('The file '.$filepath.' does not exist and can not been opened');
			}
			
			if(($mode & Curly_Stream_File::TRUNCATE)===Curly_Stream_File::TRUNCATE) {
				return 'w';
			}
			else {
				return 'a';
			}
		}
		else if(($mode & Curly_Stream_File::CREATE)===Curly_Stream_File::CREATE) {
			return 'x';
		}
		else {
			throw new Curly_Stream_Exception('Invalid open mode given. At least the open or create mode has to be specified');
		}
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