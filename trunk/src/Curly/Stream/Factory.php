<?php

/**
 * Curly_Stream_Factory
 * 
 * Factory class to create stream instances.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 12.11.2009
 */
class Curly_Stream_Factory {
	
	const FLAG_INPUT='Input';
	const FLAG_OUTPUT='Output';
	const FLAG_IO='';
	
//	/**
//	 * @var Curly_Stream_Factory Global instance to save resources for plenty of usages.
//	 */
//	static private $_globalInstance=NULL;
//	
//	/**
//	 * Returns the global instance of this class.
//	 * 
//	 * @return Curly_Stream_Factory
//	 */
//	static public function getGlobalInstance() {
//		if(self::$_globalInstance===NULL) {
//			self::$_globalInstance=new self();
//		}
//		return self::$_globalInstance;
//	}
//	
//	/**
//	 * Sets the global instance of this class.
//	 * 
//	 * @return void
//	 * @param Curly_Stream_Factory
//	 */
//	static public function setGlobalInstance(Curly_Stream_Factory $factory) {
//		self::$_globalInstance=$factory;
//	}
	
	/**
	 * @var Curly_Stream_Factory_Allocator Instance used to create class instances.
	 */
	private $_allocator=NULL;
	
	/**
	 * @var array List of namespaces to search in for stream classes
	 */
	private $_loadNamespaces=array('Curly_Stream');
	
	/**
	 * Returns the instance used to create class instances.
	 * 
	 * @return Curly_Stream_Factory_Allocator
	 */
	public function getAllocator() {
		if($this->_allocator===NULL) {
			$this->_allocator=new Curly_Stream_Factory_Allocator();
		}
		return $this->_allocator;
	}
	
	/**
	 * Sets the instance used to create class instances.
	 * 
	 * @return Curly_Stream_Factory
	 * @param Curly_Stream_Factory_Allocator
	 */
	public function setAllocator(Curly_Stream_Factory_Allocator $allocator) {
		$this->_allocator=$allocator;
		return $this;
	}
	
	/**
	 * Returns the list of namespaces to search in for stream classes.
	 * 
	 * @return array
	 */
	public function getLoadNamespaces() {
		return $this->_loadNamespaces;
	}
	
	/**
	 * Adds an entry to the list of namespaces to search in for stream classes.
	 * 
	 * @return Curly_Stream_Factory
	 * @param string
	 */
	public function addLoadNamespace($ns) {
		$this->_loadNamespaces[]=(string)$ns;
		return $this;
	}
	
	/**
	 * Removes an entry from the list of stream class namespaces.
	 * 
	 * @return Curly_Stream_Factory
	 * @param string
	 */
	public function removeLoadNamespace($ns) {
		$index=array_search($ns, $this->_loadNamespaces);
		if($index!==false) {
			unset($this->_loadNamespaces[$index]);
		}
		return $this;
	}
	
	/**
	 * Creates an stream of the given type.
	 * 
	 * @return Curly_Stream or NULL
	 * @param string Type of the stream
	 * @param mixed Constructor arguments for the stream instance
	 * @param string Input/Output type of stream
	 */
	public function createStream($type, $options=array(), $ioFlag=self::FLAG_IO) {
		if(!is_array($options)) {
			$options=array($options);
		}
		
		$type=ucfirst(strtolower($type));
		$postfix=$ioFlag=='' ? '' : '_'.ucfirst(strtolower($ioFlag));
		foreach($this->getLoadNamespaces() as $ns) {
			$class=$ns.'_'.$type.$postfix;
			
			if(class_exists($class)) {
				return $this->createInstance($class, $options);
			}
		}
		
		return NULL;
	}
	
	/**
	 * Creates an input stream of the given type.
	 * 
	 * @return Curly_Stream_Input or NULL
	 * @param string Type of the stream
	 * @param mixed Constructor arguments for the stream instance
	 */
	public function createInputStream($type, $options=array()) {
		return $this->createStream($type, $options, self::FLAG_INPUT);
	}
	
	/**
	 * Creates an output stream of the given type.
	 * 
	 * @return Curly_Stream_Output or NULL
	 * @param string Type of the stream
	 * @param mixed Constructor arguments for the stream instance
	 */
	public function createOutputStream($type, $options=array()) {
		return $this->createStream($type, $options, self::FLAG_OUTPUT);
	}
	
	/**
	 * Creates an instance of the given class, passing the given arguments as
	 * constructor arguments.
	 * 
	 * @return object
	 * @param string Classname
	 * @param array Constructor arguments
	 */
	protected function createInstance($class, array $arguments) {
		return $this->getAllocator()
			->createInstance($class, $arguments);
	}
	
}
