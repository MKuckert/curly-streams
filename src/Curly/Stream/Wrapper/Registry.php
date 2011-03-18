<?php

/**
 * Curly_Stream_Wrapper_Registry
 * 
 * Registry for any stream object or class, callable through a php stream wrapper.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Wrapper
 * @since 08.11.2009
 */
class Curly_Stream_Wrapper_Registry {
	
	/**
	 * @desc Name of the stream wrapper class
	 */
	const WRAPPERCLASS='Curly_Stream_Wrapper';
	
	// STATIC:
	
	/**
	 * @var Curly_Stream_Wrapper_Registry The global registry instance.
	 */
	static private $_globalInstance=NULL;
	
	/**
	 * Returns the global registry instance.
	 * 
	 * @return Curly_Stream_Wrapper_Registry
	 */
	static public function getGlobalInstance() {
		if(self::$_globalInstance===NULL) {
			self::setGlobalInstance(new self());
		}
		return self::$_globalInstance;
	}
	
	/**
	 * Sets the global registry instance.
	 * 
	 * @return void
	 * @param Curly_Stream_Wrapper_Registry
	 */
	static public function setGlobalInstance(Curly_Stream_Wrapper_Registry $value) {
		self::$_globalInstance=$value;
	}
	
	/**
	 * @var integer ID for the registerOnce method
	 */
	static private $id=0;
	
	// INSTANCE:
	
	/**
	 * @var array Associativ array of all registered streams.
	 */
	private $_registry=array();
	
	/**
	 * @var array List of streams registered for just a single usage.
	 */
	private $_onceRegistry=array();
	
	/**
	 * Adds a stream to this registry.
	 * 
	 * @throws Curly_Stream_Wrapper_Exception
	 * @return Curly_Stream_Wrapper_Registry
	 * @param string Name of the protocol used to use this stream
	 * @param Curly_Stream_Input|Curly_Stream_Output|string Instance or class
	 */
	public function register($name, $stream) {
		if(is_object($stream)) {
			if(!(
				$stream instanceof Curly_Stream_Input
				or $stream instanceof Curly_Stream_Output
			)) {
				throw new Curly_Stream_Wrapper_Exception('Invalid stream instance given. InputStream or OutputStream expected');
			}
		}
		else {
			$stream=(string)$stream;
			if(!class_exists($stream)) {
				throw new Curly_Stream_Wrapper_Exception('The class '.$stream.' does not exist');
			}
			
			$impl=class_implements($stream);
			if(!in_array('Curly_Stream_Input', $impl) and !in_array('Curly_Stream_Output', $impl)) {
				throw new Curly_Stream_Wrapper_Exception('The given class '.$stream.' does neither implement the InputStream nor the OutputStream interface');
			}
		}
		
		// Restore the protocol before assigning it again to prevent a warning
		if(in_array($name, stream_get_wrappers())) {
			stream_wrapper_unregister($name);
		}
		
		$this->_registry[strtolower($name)]=$stream;
		stream_wrapper_register($name, self::WRAPPERCLASS);
		
		return $this;
	}
	
	/**
	 * Registers the given stream temporarly at this registry, so it may be used for one call.
	 * 
	 * @return string The uri to use this stream instance
	 * @param Curly_Stream_Input|Curly_Stream_Output
	 */
	public function registerOnce($stream) {
		if(!(
			$stream instanceof Curly_Stream_Input
			or $stream instanceof Curly_Stream_Output
		)) {
			throw new Curly_Stream_Wrapper_Exception('Invalid stream instance given. InputStream or OutputStream expected');
		}
		
		$protocol='stream-proto-'.self::$id++;
		
		$this->register($protocol, $stream);
		$this->_onceRegistry[]=$protocol;
		
		return $protocol.'://';
	}
	
	/**
	 * Removes a stream from this registry.
	 * 
	 * @throws Curly_Stream_Wrapper_Exception
	 * @return boolean true if an item was successfully removed from the registry.
	 * @param string|Curly_Stream_Input|Curly_Stream_Output Name of the protocol or the stream instance
	 */
	public function unregister($nameOrStream) {
		if(is_object($nameOrStream)) {
			$index=array_search($nameOrStream, $this->_registry, true);
			if($index===false) {
				return false;
			}
		}
		else {
			$nameOrStream=strtolower($nameOrStream);
			if(!isset($this->_registry[$nameOrStream])) {
				return false;
			}
			else {
				$index=$nameOrStream;
			}
		}
		
		unset($this->_registry[$index]);
		stream_wrapper_unregister($index);
		return true;
	}
	
	/**
	 * Checks whether a stream instance is registered for the given protocol name.
	 * 
	 * @return boolean
	 * @param string
	 */
	public function isRegisteredName($name) {
		return isset($this->_registry[strtolower($name)]);
	}
	
	/**
	 * Returns the stream instance for the given protocol name.
	 * 
	 * @return Curly_Stream_Input|Curly_Stream_Output|NULL
	 * @param string
	 * @param boolean Flag to check the once property of the registry
	 */
	public function getByName($name, $checkOnceProp=false) {
		$name=strtolower($name);
		if($checkOnceProp and in_array($name, $this->_onceRegistry)) {
			$stream=$this->_registry[$name];
			unset($this->_registry[$name]);
			return $stream;
		}
		
		return $this->_registry[$name];
	}
	
	/**
	 * Returns all registered instances and protocol names as an associative array.
	 * 
	 * @return array
	 */
	public function getAllRegistered() {
		return $this->_registry;
	}
	
}