<?php

/**
 * Curly_Content_Item
 * 
 * Represents a content item
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Content
 * @since 29.01.2010
 */
class Curly_Content_Item {
	
	/**
	 * @var Curly_Content_RepositoryInterface The repository this item is
	 *  associated with.
	 */
	private $_repository=NULL;
	
	/**
	 * @var string The id of this item
	 */
	private $_id=NULL;
	
	/**
	 * @var Curly_MimeType The mimetype of this item
	 */
	private $_mimeType=NULL;
	
	/**
	 * @var DateTime The creation date of this item
	 */
	private $_created=NULL;
	
	/**
	 * @var DateTime The timestamp of the last item update
	 */
	private $_updated=NULL;
	
	/**
	 * @var Curly_Content_AttributeList List of item attributes
	 */
	private $_attributes=NULL;
	
	/**
	 * Returns the repository this item is associated with.
	 * 
	 * @return Curly_Content_RepositoryInterface
	 */
	public function getRepository() {
		return $this->_repository;
	}
	
	/**
	 * Assiciates this item with the given repository instance.
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item
	 * @param Curly_Content_RepositoryInterface
	 */
	public function associateTo(Curly_Content_RepositoryInterface $repository) {
		if($this->_repository!==NULL) {
			if($this->_repository===$repository) {
				return;
			}
			throw new Curly_Content_Exception('This item is already associated with another repository');
		}
		
		$this->_repository=$repository;
		return $this;
	}
	
	/**
	 * Returns the id of this item
	 * 
	 * @return string
	 */
	public function getID() {
		return $this->_id;
	}
	
	/**
	 * Sets the id of this item
	 * 
	 * @return Curly_Content_Item
	 * @param string
	 */
	public function setID($id) {
		$this->_id=(string)$id;
		return $this;
	}
	
	/**
	 * Returns the mimetype of this item
	 * 
	 * @return Curly_MimeType
	 */
	public function getMimeType() {
		return $this->_mimeType;
	}
	
	/**
	 * Sets the mimetype of this item
	 * 
	 * @return Curly_Content_Item
	 * @param Curly_MimeType|string
	 */
	public function setMimetype($mimeType) {
		if(!($mimeType instanceof Curly_MimeType)) {
			$mimeType=Curly_MimeType::fromString($mimeType);
		}
		
		$this->_mimeType=$mimeType;
		return $this;
	}
	
	/**
	 * Returns the creation date of this item
	 * 
	 * @return DateTime
	 */
	public function getCreated() {
		return $this->_created;
	}
	
	/**
	 * Sets the creation date of this item
	 * 
	 * @return Curly_Content_Item
	 * @param DateTime
	 */
	public function setCreated($created) {
		$this->_created=$created;
		return $this;
	}
	
	/**
	 * Returns the timestamp of the last item update
	 * 
	 * @return DateTime
	 */
	public function getUpdated() {
		return $this->_updated;
	}
	
	/**
	 * Sets the timestamp of the last item update
	 * 
	 * @return Curly_Content_Item
	 * @param DateTime
	 */
	public function setUpdated(DateTime $updated) {
		$this->_updated=$updated;
		return $this;
	}
	
	/**
	 * Returns the list of all item attributes
	 * 
	 * @return Curly_Content_AttributeList
	 */
	public function getAttributes() {
		return $this->_attributes;
	}
	
	/**
	 * Returns the attribute value for the given name
	 * 
	 * @return string or null
	 * @param string Name of the attribute
	 */
	public function getAttribute($name) {
		return $this->_attributes[$name];
	}
	
	/**
	 * Adds an attribute to this item
	 * 
	 * @return Curly_Content_Item
	 * @param string Name of the attribute
	 * @param string The attribute value
	 */
	public function setAttribute($name, $value) {
		$this->_attributes[$name]=$value;
		return $this;
	}
	
	/**
	 * Returns a stream object for reading the content of this item.
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Stream_Input
	 */
	public function getContentStream() {
		if(!$this->_repository) {
			throw new Curly_Content_Exception('This item is not associated to any repository instance');
		}
		
		return $this->_repository->getContentStreamByItemID($this->getID());
	}
	
	/**
	 * Sets the content of this item from the given content stream.
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item
	 * @param Curly_Stream_Input
	 */
	public function readContentStream(Curly_Stream_Input $stream) {
		if(!$this->_repository) {
			throw new Curly_Content_Exception('This item is not associated to any repository instance');
		}
		
		$this->_repository->setContentStreamByItemID($this->getID(), $stream);
		return $this;
	}
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Content_RepositoryInterface The repository this item should
	 *  be referenced with
	 */
	public function __construct(Curly_Content_RepositoryInterface $repository=NULL) {
		$this->_repository=$repository;
		$this->_attributes=new Curly_Content_AttributeList();
	}
	
	/**
	 * Stores this item to the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item
	 */
	public function store() {
		if(!$this->_repository) {
			throw new Curly_Content_Exception('This item is not associated to any repository instance');
		}
		
		if($this->_id!==NULL) {
			$this->_repository->set($this->_id, $this);
		}
		else {
			$this->_repository->add($this);
		}
		
		return $this;
	}
	
	/**
	 * Removes this item from the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item
	 */
	public function remove() {
		if(!$this->_repository) {
			throw new Curly_Content_Exception('This item is not associated to any repository instance');
		}
		
		if($this->_id!==NULL) {
			throw new Curly_Content_Exception('This item has no id. It seems like this item is not stored in any repository');
		}
		
		$this->_repository->remove($this->_id);
		return $this;
	}
	
}
