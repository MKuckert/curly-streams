<?php

/**
 * Curly_Content_AttributeList
 * 
 * A list of item attributes.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Content
 * @since 29.01.2010
 */
class Curly_Content_AttributeList extends ArrayObject {
	
	const ADDED=1;
	const CHANGED=2;
	const REMOVED=3;
	
	/**
	 * @var array List of changes made to this list.
	 */
	private $_changeLog=array();
	
	/**
	 * Returns the list of changes made to this list.
	 * 
	 * @return array
	 */
	public function getChangeLog() {
		return $this->_changeLog;
	}
	
	/**
	 * Clears the list of changes
	 * 
	 * @return void
	 */
	public function clearChangeLog() {
		$this->_changeLog=array();
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public function offsetSet($index, $newval) {
		if($this->offsetExists($index)) {
			$this->_changeLog[$index]=self::CHANGED;
		}
		else {
			$this->_changeLog[$index]=self::ADDED;
		}
		
		return parent::offsetSet($index, $newval);
	}
	
	public function offsetUnset($index) {
		$this->_changeLog[$index]=self::REMOVED;
		return parent::offsetUnset($index);
	}
	
	public function append($value) {
		$this->_changeLog[$this->count()]=self::ADDED;
		return parent::append($value);
	}
	
	public function exchangeArray($input) {
		foreach($this->_changeLog as &$value) {
			$value=self::REMOVED;
		}
		foreach($this as $key=>$value) {
			$this->_changeLog[$key]=self::REMOVED;
		}
		
		foreach($input as $key=>$value) {
			if(isset($this->_changeLog[$key])) {
				$this->_changeLog[$key]=self::UPDATED;
			}
			else {
				$this->_changeLog[$key]=self::ADDED;
			}
		}
		
		return parent::exchangeArray($input);
	}
	
}
