<?php

/**
 * Curly_MimeType
 * 
 * Represents an immutable mimetype instance
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.MimeType
 * @since 30.12.2009
 */
class Curly_MimeType {
	
	/**
	 * @var string The media part of this mimetype
	 */
	private $_mediaType;
	
	/**
	 * @var string The sub type part of this mimetype
	 */
	private $_subType;
	
	/**
	 * @var array Parameters of this mimetype
	 */
	private $_parameters=NULL;
	
	/**
	 * Returns the media part of this mimetype
	 * 
	 * @return string
	 */
	public function getMediaType() {
		return $this->_mediaType;
	}
	
	/**
	 * Returns the sub type part of this mimetype
	 * 
	 * @return string
	 */
	public function getSubType() {
		return $this->_subType;
	}
	
	/**
	 * Returns the parameters of this mimetype
	 * 
	 * @return array
	 */
	public function getParameters() {
		return $this->_parameters;
	}
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_MimeType_Exception
	 * @param string The media part of this mimetype
	 * @param string The sub type part of this mimetype
	 * @param array Parameters of this mimetype
	 */
	public function __construct($mediaType, $subType, array $parameters=array()) {
		if(!preg_match('/^[a-z0-9]+(-?[a-z0-9])*$/i', $mediaType)) {
			throw new Curly_MimeType_Exception('Invalid media type definition given');
		}
		if(!preg_match('/^[a-z0-9]+([\-\.+]?[a-z0-9])*$/i', $mediaType)) {
			throw new Curly_MimeType_Exception('Invalid sub type definition given');
		}
		
		$this->_mediaType=strtolower($mediaType);
		$this->_subType=strtolower($subType);
		
		foreach($parameters as $key=>$param) {
			if(!is_string($key)) {
				throw new Curly_MimeType_Exception('Invalid parameter key '.$key.' given. Only strings are allowed');
			}
			$parameters[$key]=(string)$param;
		}
		$this->_parameters=$parameters;
	}
	
	/**
	 * Parses the given parameter into a mimetype instance.
	 * 
	 * @throws Curly_MimeType_Exception
	 * @return Curly_MimeType
	 * @param string
	 */
	static public function fromString($string) {
		$parts=explode('/', $string, 2);
		if(count($parts)<2) {
			throw new Curly_MimeType_Exception('The given parameter is no valid mimetype');
		}
		
		list($mediatype, $subtype)=$parts;
		
		// Split parameters from sub part
		$subtype=explode(';', $subtype);
		
		$params=array_splice($subtype, 1);
		$subtype=$subtype[0];
		
		foreach($params as $key=>$value) {
			$split=explode('=', $value, 2);
			if(count($split)<2) {
				throw new Curly_MimeType_Exception('Invalid mimetype parameter '.$value.' given');
			}
			
			unset($params[$key]);
			$params[trim($split[0])]=trim($split[1]);
		}
		
		return new self($mediatype, $subtype, $params);
	}
	
	/**#@+
	 * Returns a string representation of this instance
	 * 
	 * @return string
	 */
	public function toString() {
		$type=$this->getMediaType().'/'.$this->getSubType();
		foreach($this->getParameters() as $param=>$value) {
			// TODO: Improve this for whitespaces etc.
			$type.='; '.$param.'='.$value;
		}
		
		return $type;
	}
	public function __toString() {
		return $this->toString();
	}
	/**#@-*/
	
	/**
	 * Determines if this instance is the same as the given instance.
	 * 
	 * @return boolean
	 * @param Curly_MimeType
	 */
	public function equals(Curly_MimeType $that) {
		$p1=$this->getParameters();
		$p2=$that->getParameters();
		if(!(
			$this->getMediaType()===$that->getMediaType() and
			$this->getSubType()===$that->getSubType() and
			count($p1)===count($p2)
		)) {
			return false;
		}
		
		foreach($p1 as $key=>$value) {
			if(!isset($p2[$key]) or $p2[$key]!==$value) {
				return false;
			}
		}
		
		return true;
	}
	
}
