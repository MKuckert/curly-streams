<?php

/**
 * Curly_DateTime
 * 
 * Utility class around the DateTime class.
 *
 * @author Martin Kuckert
 * @copyright Copyright (c) 2010 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 29.03.2010
 */
class Curly_DateTime {
	
	/**
	 * @var DateTime The wrapped php DateTime instance
	 */
	private $_dt=NULL;
	
	/**
	 * @var string Field used for serialization
	 */
	private $_s;
	
	/**
	 * Constructor
	 * 
	 * @throws Exception
	 * @param string
	 * @param DateTimeZone|string
	 */
	public function __construct($time='now', $timezone='UTC') {
		if(is_string($timezone)) {
			$timezone=new DateTimeZone($timezone);
		}
		
		$this->_dt=new DateTime($time, $timezone);
	}
	
	/**
	 * Returns the timezone instance of this object.
	 * 
	 * @return DateTimeZone
	 */
	public function getTimezone() {
		return $this->_dt->getTimezone();
	}
	
	/**
	 * Returns the name of the timezone of this object.
	 * 
	 * @return string
	 */
	public function getTimezoneName() {
		return $this->_dt->getTimezone()
			->getName();
	}
	
	/**
	 * Sets the timezone of this object.
	 * 
	 * @return Curly_DateTime
	 * @param DateTimeZone|string
	 */
	public function setTimezone($timezone) {
		if(!($timezone instanceof DateTimeZone)) {
			$timezone=new DateTimeZone($timezone);
		}
		$this->_dt->setTimezone($timezone);
		return $this;
	}
	
	/**
	 * Returns the timezone difference of this object in seconds to UTC.
	 * 
	 * @return integer
	 */
	public function getOffset() {
		return $this->_dt->getOffset();
	}
	
	/**
	 * Returns the value of this instance as a unix timestamp.
	 * 
	 * @return integer
	 */
	public function getTimestamp() {
		if(method_exists($this->_dt, 'getTimestamp')) {
			return $this->_dt->getTimestamp();
		}
		else {
			return (int)$this->_dt->format('U');
		}
	}
	
	/**
	 * Sets the value of this instance by a unix timestamp.
	 * 
	 * @return Curly_DateTime
	 * @param integer
	 */
	public function setTimestamp($value) {
		return $this->setValue('@'.$value);
	}
	
	/**
	 * Returns the datetime value in the given format.
	 * 
	 * @return string
	 * @param string
	 */
	public function format($format) {
		return $this->_dt->format($format);
	}
	
	/**
	 * Modifies this timestamp.
	 * 
	 * @return Curly_DateTime
	 * @param string
	 */
	public function modify($modify) {
		$this->_dt->modify($modify);
		return $this;
	}
	
	/**
	 * Sets the date part of this object.
	 * 
	 * @return Curly_DateTime
	 * @param integer Year value
	 * @param integer Month value
	 * @param integer Day value
	 */
	public function setDate($year, $month, $day) {
		$this->_dt->setDate($year, $month, $day);
		return $this;
	}
	
	/**
	 * Sets the ISO date
	 * 
	 * @return Curly_DateTime
	 * @param integer Year value
	 * @param integer Week value
	 * @param integer Day value
	 */
	public function setISODate($year, $week, $day=1) {
		$this->_dt->setISODate($year, $week, $day);
		return $this;
	}
	
	/**
	 * Sets the time
	 * 
	 * @return Curly_DateTime
	 * @param integer Hour value
	 * @param integer Minute value
	 * @param integer Second value
	 */
	public function setTime($hour, $minute, $second=0) {
		$this->_dt->setTime($hour, $minute, $second);
		return $this;
	}
	
	/**
	 * Sets the value of this instance. This accepts the same values
	 * like the constructor
	 * 
	 * @throws Exception
	 * @return Curly_DateTime
	 * @param string
	 * @param DateTimeZone|string
	 */
	public function setValue($value, $timezone=NULL) {
		if($timezone===NULL) {
			$this->_dt->__construct($value);
		}
		else {
			$this->_dt->__construct($value, $timezone);
		}
		
		return $this;
	}
	
	/**
	 * Dispatched if this object is serialized.
	 * 
	 * @return array
	 */
	public function __sleep() {
		$this->_s=$this->_dt->format(DateTime::RFC3339);
		return array('_s');
	}
	
	/**
	 * Dispatched after this object is unserialized.
	 * 
	 * @return void
	 */
	public function __wakeup() {
		$this->setValue($this->_s);
	}
	
}
