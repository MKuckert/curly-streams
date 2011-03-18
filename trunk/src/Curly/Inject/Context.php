<?php

/**
 * Curly_Inject_Context
 * 
 * Dependency injection context container
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Inject
 * @since 28.01.2010
 */
class Curly_Inject_Context {
	
	/**
	 * @var array List of primitive data types
	 */
	static private $_primitiveTypes=array(
		'string', 'int', 'integer', 'float', 'double', 'bool', 'boolean', 'array', 'resource'
	);
	
	/**
	 * @var array Already registered instances.
	 */
	private $_registeredInstances=array();
	
	/**
	 * @var Curly_Annotations
	 */
	private $_annotationParser=NULL;
	
	/**
	 * Returns the annotation parser.
	 * 
	 * @return Curly_Annotations
	 */
	public function getAnnotationParser() {
		if($this->_annotationParser===NULL) {
			$this->_annotationParser=new Curly_Annotations();
		}
		return $this->_annotationParser;
	}
	
	/**
	 * Sets the annotation parser.
	 * 
	 * @return Curly_Inject_Context
	 * @param Curly_Annotations
	 */
	public function setAnnotationParser(Curly_Annotations $parser) {
		$this->_annotationParser=$parser;
		return $this;
	}
	
	/**
	 * Creates an instance for the given class name.
	 * 
	 * @throws Curly_Inject_Exception
	 * @return object
	 * @param string
	 * @param array Additional constructor parameters. The index has to match the
	 *  position in the method signature.
	 */
	public function createInstance($className, array $additionalParams=array()) {
		$annotations=$this->getAnnotationParser()
			->parseClassHierarchy($className);
		
		$instance=$this->_createInstance($className, $annotations->getConstructorAnnotations(), $additionalParams);
		$this->_injectMethods($instance, $annotations->getAllMethodAnnotations());
		return $instance;
	}
	
	/**
	 * Implementation of the constructor injection.
	 * 
	 * @throws Curly_Inject_Exception
	 * @return object
	 * @param string
	 * @param array constructor annotations
	 * @param array Additional constructor parameters
	 */
	protected function _createInstance($className, array $ctrAnnotations, array $additionalParams) {
		$values=array();
		
		if(isset($ctrAnnotations['param'])) {
			foreach($ctrAnnotations['param'] as $index=>$param) {
				if(in_array(strtolower($param), self::$_primitiveTypes)) {
					// Found a primitive type: Lets try the given additional params
					if(isset($additionalParams[$index])) {
						$values[]=$additionalParams[$index];
					}
					// We have already enough parameters, so we leave the rest unset
					else if($index>=$ctrAnnotations['requiredParams']) {
						break;
					}
					else {
						throw new Curly_Inject_Exception('Missing constructor parameter '.$index.' of type '.$param);
					}
				}
				// Class parameter
				else {
					$values[]=$this->getInstance($param);
				}
			}
		}
		
		// Create an instance
		$ref=new ReflectionClass($className);
		if(count($values)>0) {
			$instance=$ref->newInstanceArgs($values);
		}
		else {
			$instance=$ref->newInstance();
		}
		
		return $instance;
	}
	
	/**
	 * Implementation of the setter injection.
	 * 
	 * @throws Curly_Inject_Exception
	 * @return void
	 * @param object The context object
	 * @param array method annotations
	 */
	protected function _injectMethods($object, array $annotations) {
		foreach($annotations as $method=>$annotation) {
			$useParam=isset($annotation['param']);
			$useInject=isset($annotation['inject']);
			
			// No params given, no setter method or more than 1 parameter required
			if(
				!($useParam or $useInject) or
				strcasecmp(substr($method, 0, 3), 'set')!==0 or
				$annotation['requiredParams']>1
			) {
				continue;
			}
			
			// Fetch param
			if($useParam) {
				$param=$annotation['param'][0];
				
				// primitive type
				if(in_array(strtolower($param), self::$_primitiveTypes)) {
					continue;
				}
			}
			else {
				$param=$annotation['inject'][0];
			}
			
			$value=$this->getInstance($param);
			$object->$method($value);
		}
	}
	
	/**
	 * Returns the previously registered instance with the given name.
	 * 
	 * If a class name is given, this method trys to create an instance of that class.
	 * 
	 * @throws Curly_Inject_Exception
	 * @return object or NULL
	 * @param string
	 */
	public function getInstance($instanceID) {
		if(isset($this->_registeredInstances[$instanceID])) {
			return $this->_registeredInstances[$instanceID];
		}
		else if(class_exists($instanceID)) {
			return $this->createInstance($instanceID);
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Registers an object instance at this class.
	 * 
	 * @throws InvalidArgumentException
	 * @return Curly_Inject_Context
	 * @param string or object. The instance or class name or the instance directly
	 * @param object or boolean. The instance if $instanceOrClass is a string or true
	 *  if you want to register the given object with all it's implementing interfaces.
	 */
	public function registerInstance($instanceOrClass, $instance=NULL) {
		if(is_object($instanceOrClass)) {
			if($instance===true) {
				foreach(class_implements($instanceOrClass) as $interface) {
					$this->registerInstance($instance, $instanceOrClass);
				}
			}
			
			$instance=$instanceOrClass;
			$instanceOrClass=get_class($instanceOrClass);
		}
		if(!is_object($instance)) {
			throw new InvalidArgumentException('Invalid instance argument given');
		}
		
		$this->_registeredInstances[$instanceOrClass]=$instance;
	}
	
}
