<?php

/**
 * Curly_Annotations_Parser
 * 
 * A simple parser to extract annotations out of doc comments.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly.Annotations
 * @since 30.12.2009
 */
class Curly_Annotations_Parser {
	
	/**
	 * @var array List of already parsed tags
	 */
	private $_tags=array();
	
	/**
	 * @var array List of singleton tags. If a tag occures more than once all
	 *  matches are concatenated.
	 */
	private $_singleTags=array('desc');
	
	/**
	 * Returns the list of singleton tags.
	 * 
	 * @return array
	 */
	public function getSingleTags() {
		return($this->_singleTags);
	}
	
	/**
	 * Sets the list of singleton tags.
	 * 
	 * @return Curly_Annotations_Parser
	 * @param array
	 */
	public function setSingleTags(array $tags) {
		$tags=array_map('strval', $tags);
		$this->_singleTags=$tags;
		return $this;
	}
	
	/**
	 * Marks a tag as a singleton tag.
	 * 
	 * @return Curly_Annotations_Parser
	 * @param string
	 */
	public function addSingleTag($tag) {
		$this->_singleTags[]=(string)$tag;
		return $this;
	}
	
	/**
	 * Parses the given string as a doc comment and extracts all tags.
	 * Returns an array with all extracted annotations.
	 * 
	 * @return array
	 * @param string
	 */
	public function parse($code) {
		$this->_tags=array();
		
		$lines=$this->extractLines($code);
		$this->parseTags($lines);
		$this->combineTags();
		
		return $this->_tags;
	}
	
	/**
	 * Extracts the raw content lines of the given doc comment string.
	 * 
	 * @return array
	 * @param string
	 */
	protected function extractLines($code) {
		$lines=array();
		foreach(preg_split('~[\r\n]+~', $code, -1, PREG_SPLIT_NO_EMPTY) as $line) {
			$line=trim($line, " \t\n\r\0\x0B*");
			// Kommentarbeginn/Ende übergehen
			if($line=='/') {
				continue;
			}
			
			$lines[]=$line;
		}
		return $lines;
	}
	
	/**
	 * Parses the given raw content lines
	 * 
	 * @return void
	 * @param array
	 */
	protected function parseTags(array $lines) {
		$currentTagName='desc';
		$currentTag=NULL;
		
		foreach($lines as $line) {
			list($tag, $content)=$this->parseLine($line);
			
			if($tag==NULL) {
				if($content=='') {
					continue;
				}
				
				if($currentTag==NULL) {
					$currentTag=$content;
				}
				else {
					$currentTag.=' '.$content;
				}
			}
			else {
				if(!isset($this->_tags[$currentTagName])) {
					$this->_tags[$currentTagName]=array();
				}
				$this->_tags[$currentTagName][]=$currentTag;
				$currentTagName=$tag;
				$currentTag=$content;
			}
		}
		
		// Append last parsed item
		if(!isset($this->_tags[$currentTagName])) {
			$this->_tags[$currentTagName]=array($currentTag);
		}
		else {
			$this->_tags[$currentTagName][]=$currentTag;
		}
	}
	
	/**
	 * Extracts the tag and content of the given content line.
	 * 
	 * @return array (0=>tag name, 1=>content)
	 * @param string
	 */
	protected function parseLine($line) {
		// Tag found
		if(isset($line[0]) and $line[0]==='@') {
			$pos=strpos($line, ' ');
			if($pos===false) {
				$tag=substr($line, 1);
				$content='';
			}
			else {
				$tag=substr($line, 1, $pos-1);
				$content=ltrim(substr($line, $pos));
			}
			return(array($tag, $content));
		}
		// No tag
		else {
			return(array(NULL, $line));
		}
	}
	
	/**
	 * Combines each entry for a singleton tag
	 * 
	 * @return void
	 */
	protected function combineTags() {
		foreach($this->_singleTags as $tag) {
			if(isset($this->_tags[$tag])) {
				$this->_tags[$tag]=implode(" ", $this->_tags[$tag]);
			}
		}
	}
	
}
