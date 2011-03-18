<?php

/**
 * Curly_Content_RepositoryInterface
 * 
 * Interface for a repository adapter
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Content
 * @since 29.01.2010
 */
interface Curly_Content_RepositoryInterface {
	
	/**
	 * Returns the content item to the given id
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item
	 * @param string Item id
	 */
	public function get($id);
	
	/**
	 * Sets the content item for the given id
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string Item id
	 * @param Curly_Content_Item
	 */
	public function set($id, Curly_Content_Item $item);
	
	/**
	 * Adds the given item instance to the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param Curly_Content_Item
	 */
	public function add(Curly_Content_Item $item);
	
	/**
	 * Removes the content item with the given id from the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string Item id
	 */
	public function remove($id);
	
	/**
	 * Queries the repository for a list of items matching the given
	 * query object.
	 * 
	 * @return Curly_Content_ItemList
	 * @param Curly_Content_Query
	 */
	public function find(Curly_Content_Query $query);
	
	/**
	 * Returns the content stream for the item with the given id.
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Stream_Input
	 * @param string The item id
	 */
	public function getContentStreamByItemID($id);
	
	/**
	 * Reads the content for the item with the given id from the given content stream
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string The item id
	 * @param Curly_Stream_Input
	 */
	public function setContentStreamByItemID($id, Curly_Stream_Input $stream);
	
}