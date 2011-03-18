<?php

/**
 * Curly_Content_Query
 * 
 * An instance of this class may be used to query a repository object
 * for some specific content items.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Content
 * @since 30.01.2010
 */
class Curly_Content_Query {
	const EQUAL='=';
	const LOWER_THAN='<';
	const LOWER_THAN_EQUAL='<=';
	const GREATER_THAN='>';
	const GERATER_THAN_EQUAL='>=';
	const UNEQUAL='!=';
	const SORT_ASC=SORT_ASC;
	const SORT_DESC=SORT_DESC;
	
	/**
	 * @return Curly_Content_Query
	 * @param string
	 * @param string
	 * @param string
	 */
	public function where($attributeName, $attributeValue, $operation=self::EQUALS) {
		// TODO
	}
	
	/**
	 * @return Curly_Content_Query
	 * @param string
	 * @param string
	 */
	public function sortBy($attributeName, $direction=self::SORT_ASC) {
		// TODO
	}
	
	/**
	 * @return Curly_Content_Query
	 * @param integer
	 * @param integer
	 */
	public function limit($limit, $offset=0) {
		// TODO
	}
	
}
