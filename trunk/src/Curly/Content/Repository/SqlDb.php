<?php

/**
 * Curly_Content_Repository_SqlDb
 * 
 * A repository adapter storing item data into a sql database
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Content.Repository
 * @since 30.01.2010
 */
class Curly_Content_Repository_SqlDb implements Curly_Content_RepositoryInterface {
	
	/**
	 * @var Zend_Db_Adapter_Abstract Database adapter instance to use
	 */
	private $_db=NULL;
	
	/**
	 * @var Curly_MimeType_Detector The instance to detect the mimetype automatically
	 */
	private $_mimetypeDetector=NULL;
	
	/**
	 * @var string Name of the content table
	 */
	private $_contentTable='content';
	
	/**
	 * @var string Name of the attribute table
	 */
	private $_attributeTable='attributes';
	
	/**
	 * @var DateTimeZone
	 */
	private $GMT=NULL;
	
	/**
	 * Returns the database adapter instance to use for this object
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDatabaseAdapter() {
		return $this->_db;
	}
	
	/**
	 * Sets the database adapter instance to use for this object
	 * 
	 * @return Curly_Content_Repository_SqlDb
	 * @param Zend_Db_Adapter_Abstract
	 */
	public function setDatabaseAdapter(Zend_Db_Adapter_Abstract $adapter) {
		$this->_db=$adapter;
		return $this;
	}
	
	/**
	 * Returns the instance to detect the mimetype automatically
	 * 
	 * @return Curly_MimeType_Detector
	 */
	public function getMimetypeDetector() {
		return $this->_mimetypeDetector;
	}
	
	/**
	 * Sets the mimetype detector instance
	 * 
	 * @return Curly_Content_Repository_SqlDb
	 * @param Curly_MimeType_Detector
	 */
	public function setMimetypeDetector($detector) {
		$this->_mimetypeDetector=$detector;
		return $this;
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->GMT=new DateTimeZone('GMT');
	}
	
	/**
	 * Returns the content item to the given id
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_Item or NULL
	 * @param string Item id
	 */
	public function get($id) {
		try {
			$items=$this->mapStatement(
				$this->getDatabaseAdapter()
					->select()
						->from(array('c'=>$this->_contentTable), array('id', 'mimetype', 'created', 'updated'))
						->joinLeft(array('a'=>$this->_attributeTable), 'a.content_id=c.id', array('name', 'value'))
						->where('c.id=?', $id)
					->query(Zend_Db::FETCH_OBJ)
			);
		}
		catch(Zend_Db_Exception $ex) {
			throw new Curly_Content_Exception('Failed to select the dataset for the given id: '.$ex->getMessage());
		}
		
		if(count($items)>0) {
			return $items[0];
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Converts the given Statement instance into an array of item instances.
	 * 
	 * @return array
	 * @param Zend_Db_Statement_Interface
	 */
	protected function mapStatement(Zend_Db_Statement_Interface $stmt) {
		$items=array();
		$item=NULL;
		
		$stmt->setFetchMode(Zend_Db::FETCH_OBJ);
		foreach($stmt as $dataset) {
			// The first item
			if(!$item or $item->getID()!==$dataset->id) {
				$item=$this->mapDataset($dataset);
				$items[]=$item;
			}
			
			if($dataset->name) {
				$item->setAttribute($dataset->name, $dataset->value);
			}
		}
		
		foreach($items as $item) {
			$item->getAttributes()
				->clearChangeLog();
		}
		
		return $items;
	}
	
	/**
	 * Creates an item from the given dataset.
	 * 
	 * @return Curly_Content_Item
	 * @param object
	 */
	protected function mapDataset($dataset) {
		$item=new Curly_Content_Item($this);
		return $item
			->setID($dataset->id)
			->setCreated(new DateTime($dataset->created, $this->GMT))
			->setUpdated(new DateTime($dataset->updated, $this->GMT))
			->setMimetype(Curly_MimeType::fromString($dataset->mimetype));
	}
	
	/**
	 * Returns the current timestamp as a string.
	 * 
	 * @return string
	 */
	protected function now() {
		return gmdate('Y-m-d H:i:s');
	}
	
	/**
	 * Sets the content item for the given id
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string Item id
	 * @param Curly_Content_Item
	 */
	public function set($id, Curly_Content_Item $item) {
		$db=$this->getDatabaseAdapter()
			->beginTransaction();
		$now=$this->now();
		try {
			$db->update(
				$this->_contentTable,
				array(
					'mimetype' => $item->getMimeType(),
					'updated' => $now
				),
				$db->quoteInto('id=?', $id)
			);
		}
		catch(Zend_Db_Exception $ex) {
			$db->rollBack();
			throw new Curly_Content_Exception('Failed to update the content item data to the database: '.$ex->getMessage());
		}
		
		try {
			$attributes=$item->getAttributes();
			foreach($attributes->getChangeLog() as $name=>$state) {
				switch($state) {
					case Curly_Content_AttributeList::ADDED:
						$db->insert(
							$this->_attributeTable,
							array(
								'content_id' => $id,
								'name' => $name,
								'value' => $attributes[$name]
							)
						);
						break;
					case Curly_Content_AttributeList::CHANGED:
						$db->update(
							$this->_attributeTable,
							array('value' => $attributes[$name]),
							$db->quoteInto('content_id=?', $id).' and '.$db->quoteInto('name=?', $name)
						);
						break;
					case Curly_Content_AttributeList::REMOVED:
						$db->delete(
							$this->_attributeTable,
							$db->quoteInto('content_id=?', $id).' and '.$db->quoteInto('name=?', $name)
						);
						break;
				}
			}
		}
		catch(Zend_Db_Exception $ex) {
			$db->rollBack();
			throw new Curly_Content_Exception('Failed to update the attribute '.$name.' to the database: '.$ex->getMessage());
		}
		
		$db->commit();
		$attributes->clearChangeLog();
		$item->setUpdated(new DateTime($now, $this->GMT));
		return $this;
	}
	
	/**
	 * Adds the given item instance to the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param Curly_Content_Item
	 */
	public function add(Curly_Content_Item $item) {
		$db=$this->getDatabaseAdapter();
		$id=md5(uniqid(time(), mt_rand()));
		
		$mimetype=$item->getMimeType();
		if($mimetype===NULL) {
			throw new Curly_Content_Exception('Missing required mimetype value');
		}
		
		$db->beginTransaction();
		$now=$this->now();
		
		// Insert content item
		try {
			$db->insert($this->_contentTable, array(
				'id' => $id,
				'created' => $now,
				'updated' => $now,
				'mimetype' => $mimetype
			));
		}
		catch(Zend_Db_Exception $ex) {
			$db->rollBack();
			throw new Curly_Content_Exception('Failed to insert the content item into the database: '.$ex->getMessage());
		}
		
		// Insert attributes
		try {
			foreach($item->getAttributes() as $key=>$value) {
				$db->insert($this->_attributeTable, array(
					'content_id' => $id,
					'name' => $key,
					'value' => $value
				));
			}
		}
		catch(Zend_Db_Exception $ex) {
			$db->rollBack();
			throw new Curly_Content_Exception('Failed to insert the attribute '.$key.' into the database: '.$ex->getMessage());
		}
		
		$db->commit();
		
		$item
			->setID($id)
			->setCreated(new DateTime($now, $this->GMT))
			->setUpdated(new DateTime($now, $this->GMT))
			->associateTo($this)
			->getAttributes()
				->clearChangeLog();
		
		return $this;
	}
	
	/**
	 * Removes the content item with the given id from the repository
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string Item id
	 */
	public function remove($id) {
		$db=$this->getDatabaseAdapter();
		try {
			$db->delete($this->_contentTable, $db->quoteInto('id=?', $id));
		}
		catch(Zend_Db_Exception $ex) {
			throw new Curly_Content_Exception('Failed to remove the content item with the given id from the database: '.$ex->getMessage());
		}
		return $this;
	}
	
	/**
	 * Queries the repository for a list of items matching the given
	 * query object.
	 * 
	 * @return Curly_Content_ItemList
	 * @param Curly_Content_Query
	 */
	public function find(Curly_Content_Query $query) {
		// TODO
	}
	
	/**
	 * Returns the content stream for the item with the given id.
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Stream_Input
	 * @param string The item id
	 */
	public function getContentStreamByItemID($id) {
		try {
			$streamData=$this->getDatabaseAdapter()
				->select()
					->from($this->_contentTable, 'content')
					->where('id=?', $id)
				->query()
				->fetchColumn();
		}
		catch(Zend_Db_Exception $ex) {
			throw new Curly_Content_Exception('Failed to select the content stream to the item with the given id from the database: '.$ex->getMessage());
		}
		
		return new Curly_Stream_Memory_Input($streamData);
	}
	
	/**
	 * Reads the content for the item with the given id from the given content stream
	 * 
	 * @throws Curly_Content_Exception
	 * @return Curly_Content_RepositoryInterface
	 * @param string The item id
	 * @param Curly_Stream_Input
	 */
	public function setContentStreamByItemID($id, Curly_Stream_Input $stream) {
		$db=$this->getDatabaseAdapter();
		
		$streamData=$this->stream2hex($stream);
		var_dump($streamData);
		if($streamData!=='') {
			$streamData=new Zend_Db_Expr($streamData);
		}
		
		try {
			$db->update(
				$this->_contentTable,
				array('content' => $streamData),
				$db->quoteInto('id=?', $id)
			);
		}
		catch(Zend_Db_Exception $ex) {
			throw new Curly_Content_Exception('Failed to update the content stream for the item with the given id in the database: '.$ex->getMessage());
		}
		
		return $this;
	}
	
	/**
	 * Converts the contents of the given stream into a hexadecimal representation.
	 * 
	 * @return string
	 * @param Curly_Stream_Input
	 */
	protected function stream2hex(Curly_Stream_Input $stream) {
		$hex='0x';
		do {
			$hex.=str_pad(
				dechex(
					ord($stream->read(1))
				),
				2, '0', STR_PAD_LEFT
			);
		}
		while($stream->available());
		return $hex;
	}
	
}