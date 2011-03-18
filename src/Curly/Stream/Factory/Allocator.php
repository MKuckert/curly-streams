<?php

/**
 * Curly_Stream_Factory_Allocator
 * 
 * Partially adopted from the CI_Allocator_Base class from the curly-ioc project.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Factory
 * @since 12.11.2009
 * @link http://code.google.com/p/curly-ioc/
 */
class Curly_Stream_Factory_Allocator {
	
	/**
	 * Creates the concrete object.
	 * 
	 * @return object
	 * @param string Classname
	 * @param array Creation arguments
	 */
	public function createInstance($class, array $args) {
		// The ReflectionClass is not quite fast, so we'll use a simple
		// constructor call for most of the time
		switch(count($args)) {
			case 0:
				return new $class();
			case 1:
				return new $class($args[0]);
			case 2:
				return new $class($args[0], $args[1]);
			case 3:
				return new $class($args[0], $args[1], $args[2]);
			case 4:
				return new $class($args[0], $args[1], $args[2], $args[3]);
			default:
				$ref=new ReflectionClass($class);
				return $ref->newInstanceArgs($args);
		}
	}
	
}
