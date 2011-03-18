<?php

/**
 * Curly_Annotations
 * 
 * A wrapper class around the Annotation namespace.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Annotations
 * @since 30.12.2009
 */
class Curly_Annotations {
	
	/**
	 * @var Curly_Annotations_Parser
	 */
	private $_annotationParser=NULL;

	/**
	 * Returns the annotation parser of this instance.
	 * 
	 * @return Curly_Annotations_Parser
	 */
	public function getAnnotationParser() {
		if($this->_annotationParser===NULL) {
			$this->_annotationParser=new Curly_Annotations_Parser();
		}
		return $this->_annotationParser;
	}

	/**
	 * Sets the annotation parser for this instance.
	 * 
	 * @return Curly_Annotations
	 * @param Curly_Annotations_Parser
	 */
	public function setAnnotationParser(Curly_Annotations_Parser $parser) {
		$this->_annotationParser=$parser;
		return $this;
	}
	
	/**
	 * Extracts the annotations for the given doc comment.
	 * 
	 * @return array
	 * @param string
	 */
	public function parseDocComment($comment) {
		return $this->getAnnotationParser()->parse($comment);
	}
	
	/**
	 * Extracts the annotations for the given reflection instance.
	 * 
	 * @throws Curly_Annotations_Exception
	 * @return array
	 * @param ReflectionClass|ReflectionFunctionAbstract|ReflectionProperty
	 */
	public function parseReflection($reflectionInstance) {
		if(!method_exists($reflectionInstance, 'getDocComment')) {
			throw new Curly_Annotations_Exception('Invalid argument given. Expected a valid Reflection object with a getDocComment method');
		}
		
		$annotations=$this->parseDocComment($reflectionInstance->getDocComment());
		
		if($reflectionInstance instanceof ReflectionFunctionAbstract) {
			$annotations['requiredParams']=$reflectionInstance->getNumberOfRequiredParameters();
		}
		
		return $annotations;
	}
	
	/**
	 * Parses the annotations of all publicly accessable properties and methods
	 * of the given ReflectionClass instance or the class with the given name.
	 * 
	 * @throws Curly_Annotations_Exception
	 * @return Curly_Annotations_ParseClassResult
	 * @param ReflectionClass|string
	 */
	public function parseClass($class) {
		if(!($class instanceof ReflectionClass)) {
			$class=(string)$class;
			if(!class_exists($class)) {
				throw new Curly_Annotations_Exception('A class with the name '.$class.' does not exist');
			}
			$class=new ReflectionClass($class);
		}
		
		list($methods, $properties, $ctr)=$this->_parseClass($class);
		return new Curly_Annotations_ParseClassResult($methods, $properties, $ctr);
	}
	
	/**
	 * Main implementation of the parseClass method
	 * 
	 * @return array
	 * @param ReflectionClass
	 * @param array&
	 * @param array&
	 * @param array&
	 */
	protected function _parseClass(ReflectionClass $class, array &$methods=array(), array &$properties=array(), array &$ctr=array()) {
		foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			if(isset($methods[$method->name])) {
				continue;
			}
			
			$methods[$method->name]=$this->parseReflection($method);
		}
		
		foreach($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
			if(isset($properties[$property->name])) {
				continue;
			}
			
			$properties[$property->name]=$this->parseReflection($property);
		}
		
		$classCtr=$class->getConstructor();
		if($classCtr!==NULL and count($ctr)===0) {
			$ctr=$this->parseReflection($classCtr);
		}
		
		return array($methods, $properties, $ctr);
	}
	
	/**
	 * Traverses the complete class hierarchy of the given ReflectionClass
	 * instance or of the class with the given name up and merges the
	 * annotations for all publicly accessable properties and methods.
	 * 
	 * Overwriting of methods with the same name is performed as expected.
	 * For example:
	 * <code>
	 * class A {
	 *  public function x() {}
	 * }
	 * class B extends A {
	 *  public function x() {}
	 * }
	 * </code>
	 * 
	 * The result of this method call will contain the annotations for the
	 * method B::x.
	 * 
	 * @throws Curly_Annotations_Exception
	 * @return Curly_Annotations_ParseClassResult
	 * @param ReflectionClass|string
	 */
	public function parseClassHierarchy($class) {
		if(!($class instanceof ReflectionClass)) {
			$class=(string)$class;
			if(!class_exists($class)) {
				throw new Curly_Annotations_Exception('A class with the name '.$class.' does not exist');
			}
			$class=new ReflectionClass($class);
		}
		
		$methods=array();
		$properties=array();
		$ctr=array();
		
		do {
			$this->_parseClass($class, $methods, $properties, $ctr);
			$class=$class->getParentClass();
		} while($class);
		
		return new Curly_Annotations_ParseClassResult($methods, $properties, $ctr);
	}
	
}
