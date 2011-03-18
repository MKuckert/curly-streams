<?php

/**
 * Curly_Annotations_ParseClassResult
 * 
 * Result object for the Curly_Annotations::parseClass method
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Annotations
 * @since 30.12.2009
 */
class Curly_Annotations_ParseClassResult {
	
	/**
	 * @var array Annotations for all parsed methods.
	 */
	private $_methodAnnotations=array();
	
	/**
	 * @var array Annotations for all parsed properties.
	 */
	private $_propertyAnnotations=array();
	
	/**
	 * @var array Annotations for the constructor.
	 */
	private $_ctrAnnotations=array();
	
	/**
	 * Constructor
	 * 
	 * @param array Annotations for all parsed methods.
	 * @param array Annotations for all parsed properties.
	 * @param array Annotations for the constructor.
	 */
	public function __construct(array $methodAnnotations, array $propertyAnnotations, array $ctrAnnotations) {
		$this->_methodAnnotations=$methodAnnotations;
		$this->_propertyAnnotations=$propertyAnnotations;
		$this->_ctrAnnotations=$ctrAnnotations;
	}
	
	/**
	 * Returns the annotations for all parsed methods.
	 * 
	 * @return array
	 */
	public function getAllMethodAnnotations() {
		return $this->_methodAnnotations;
	}
	
	/**
	 * Returns the annotations for the method with the given name.
	 * 
	 * @return array or NULL
	 * @param string
	 */
	public function getMethodAnnotations($method) {
		if(!isset($this->_methodAnnotations[$method])) {
			return NULL;
		}
		return $this->_methodAnnotations[$method];
	}
	
	/**
	 * Returns the annotations for all parsed properties.
	 * 
	 * @return array
	 */
	public function getAllPropertyAnnotations() {
		return $this->_propertyAnnotations;
	}
	
	/**
	 * Returns the annotations for the property with the given name.
	 * 
	 * @return array or NULL
	 * @param string
	 */
	public function getPropertyAnnotations($property) {
		if(!isset($this->_propertyAnnotations[$property])) {
			return NULL;
		}
		return $this->_propertyAnnotations[$property];
	}
	
	/**
	 * Returns the annotations for the constructor.
	 * 
	 * @return array
	 */
	public function getConstructorAnnotations() {
		return $this->_ctrAnnotations;
	}
	
}
