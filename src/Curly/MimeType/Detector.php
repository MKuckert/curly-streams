<?php

/**
 * Curly_MimeType_Detector
 * 
 * An instance of this class is capable to determine the mimetype to
 * some part of content
 * 
 * Partially taken from the zend framework class Zend_Validate_File_MimeType.
 * So some parts are copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.MimeType
 * @since 30.12.2009
 */
class Curly_MimeType_Detector {
	
	/**
	 * @var string Path to the magic file if the finfo extension is used.
	 */
	protected $_magicfile=NULL;
	
	/**
	 * @var array If no $_ENV['MAGIC'] is set, try and autodiscover it based on common locations
	 */
	protected $_magicFiles=array(
		'/usr/share/misc/magic',
		'/usr/share/misc/magic.mime',
		'/usr/share/misc/magic.mgc',
		'/usr/share/mime/magic',
		'/usr/share/mime/magic.mime',
		'/usr/share/mime/magic.mgc',
		'/usr/share/file/magic',
		'/usr/share/file/magic.mime',
		'/usr/share/file/magic.mgc',
	);
	
	/**
	 * @var finfo A previously created finfo instance
	 */
	protected $_finfo=NULL;
	
	/**
	 * Returns the path to the magic file if the finfo extension is used.
	 * 
	 * @return string
	 */
	public function getMagicFile() {
		if($this->_magicfile===NULL and empty($_ENV['MAGIC'])) {
			foreach($this->_magicFiles as $file) {
				if(file_exists($file)) {
					$this->setMagicFile($file);
					break;
				}
			}
		}
		
		return $this->_magicfile;
	}
	
	/**
	 * Sets the magicfile to use.
	 * 
	 * If null, the MAGIC constant from php is used
	 * 
	 * @return Curly_MimeType_Detector
	 * @param string
	 */
	public function setMagicFile($file) {
		if(empty($file)) {
			$this->_magicfile=NULL;
		}
		else if(!is_readable($file)) {
			throw new Curly_MimeType('The given magicfile can not be read');
		}
		else {
			$this->_magicfile=(string)$file;
		}
		
		return $this;
	}
	
	/**
	 * Trys to detect the mimetype of the given stream
	 * 
	 * @return Curly_MimeType
	 * @param Curly_Stream_Input
	 */
	public function detectMimeType(Curly_Stream_Input $stream) {
		$mimefile=$this->getMagicFile();
		$type=NULL;
		$uri=Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->registerOnce($stream);
		
		if(class_exists('finfo', false)) {
			if(!$this->_finfo) {
				$const=defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
				if(!empty($mimefile)) {
					$this->_finfo=new finfo($const, $mimefile);
				}
				else {
					$this->_finfo=new finfo($const);
				}
			}
			
			if($this->_finfo) {
				// TODO: The finfo extensions seems to not support stream wrappers...
				$type=$this->_finfo->buffer(file_get_contents($uri));
			}
		}
		
		if(empty($type)) {
			if(function_exists('mime_content_type')) {
				// TODO: The finfo extensions seems to not support stream wrappers...
				$tmpFile=tempnam('/tmp', 'mime-detect');
				while($stream->available()) {
					file_put_contents($tmpFile, $stream->read(1024), FILE_APPEND);
				}
				
				$type=mime_content_type($tmpFile);
			}
		}
		
		if(empty($type)) {
			throw new Curly_MimeType_Exception('Failed to determine the mimetype of the given stream');
		}
		
		return Curly_MimeType::fromString($type);
	}
	
}
