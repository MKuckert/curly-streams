<?php

/**
 * Curly_Version
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 19.11.2009
 */
class Curly_Version {
	
	const VERSION='2.0';
	
	/**
	 * @return string
	 */
	static public function getPath() {
		return dirname(__FILE__);
	}
	
}
