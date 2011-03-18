<?php

/**
 * Curly_Stream_Xml_Output
 * 
 * Provides methods to write XML data into another output stream.
 * 
 * This class is designed to be very lightweight, so there is nearly no input
 * validiation. The developer using this class is enforced to validate any user
 * input to create a valid xml output.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Xml
 * @since 01.11.2009
 */
class Curly_Stream_Xml_Output implements Curly_Stream_Output {
	
	/**
	 * @desc Default charset to use
	 */
	const DEFAULT_CHARSET='UTF-8';
	
	/**
	 * @desc The XML version this output stream is capable of.
	 */
	const XML_VERSION='1.0';
	
	/**
	 * @var Curly_Stream_Output The capsuled output stream.
	 */
	private $_stream=NULL;
	
	/**
	 * @var string Charset to use for the XML head and the
	 * {@method escape} method.
	 */
	private $_charset=self::DEFAULT_CHARSET;
	
	/**
	 * @var array Currently collected attributes for the next opening xml tag.
	 */
	private $_attributes=array();
	
	/**
	 * @var array Inner stack of currently opened xml tags.
	 */
	private $_tagStack=array();
	
	/**
	 * Returns the capsuled output stream.
	 * 
	 * @return Curly_Stream_Output
	 */
	public function getInnerStream() {
		return $this->_stream;
	}
	
	/**
	 * Returns the charset to use.
	 * 
	 * @return string
	 */
	public function getCharset() {
		return $this->_charset;
	}
	
	/**
	 * Sets the charset to use for the XML head and the
	 * {@method escape} method.
	 * 
	 * @return Curly_Stream_Xml_Output
	 * @param string
	 */
	public function setCharset($value) {
		$this->_charset=(string)$value;
	}
	
	/**
	 * Constructor
	 * 
	 * @param Curly_Stream_Output The output stream to write the xml data to
	 * @param Charset to use for the XML head and the {@method escape} method.
	 */
	public function __construct(Curly_Stream_Output $stream, $charset=self::DEFAULT_CHARSET) {
		$this->_stream=$stream;
		$this->setCharset($charset);
	}
	
	/**
	 * Writes any buffered data into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	public function flush() {
		$this->_stream->flush();
	}
	
	/**
	 * Escapes the given data as valid xml.
	 * 
	 * The chars &, < and > and the " if the $quotes argument is set.
	 * 
	 * @return string
	 * @param string
	 * @param boolean Flag, if double quotes should be replaced
	 */
	public function escape($string, $replaceQuotes=false) {
		if($replaceQuotes) {
			return htmlspecialchars($string, ENT_COMPAT, $this->_charset);
		}
		else {
			return htmlspecialchars($string, ENT_NOQUOTES, $this->_charset);
		}
	}
	
	/**
	 * Writes the given elements as a xml textnode into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string
	 */
	public function write($data) {
		$this->_stream->write(
			$this->escape($data)
		);
		return $this;
	}
	
	/**
	 * Writes the given elements as a cdata tag into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string
	 */
	public function writeCData($data) {
		$this->_stream->write(
			'<![CDATA['.$data.']]>'
		);
		return $this;
	}
	
	/**
	 * Writes an entity with the given name into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string
	 */
	public function writeEntity($name) {
		$this->_stream->write('&'.$name.';');
		return $this;
	}
	
	/**
	 * Writes the xml head declaration.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param boolean Flag, if the standalone attribute should be set.
	 */
	public function writeHeadDeclaration($standalone=false) {
		$attributes='version="'.self::XML_VERSION.'" encoding="'.$this->_charset.'"';
		
		if($standalone) {
			$attributes.=' standalone="yes"';
		}
		
		$this->writeProcessingInstruction('xml', $attributes);
		$this->_stream->write("\n");
		
		return $this;
	}
	
	/**
	 * Adds an attribute to the list, so it's written with the next opening or
	 * empty tag.
	 * 
	 * @return Curly_Stream_Xml_Output
	 * @param string Name of the attribute
	 * @param string Value of the attribute
	 */
	public function addAttribute($name, $value) {
		$this->_attributes[(string)$name]=(string)$value;
		return $this;
	}
	
	/**
	 * Adds a list of attributes to the list, so they are written with the next
	 * opening or empty tag.
	 * 
	 * @return Curly_Stream_Xml_Output
	 * @param array Associative array of attributes
	 */
	public function addAttributes(array $attributes) {
		$this->_attributes=array_merge($this->_attributes, $attributes);
		return $this;
	}
	
	/**
	 * Writes any pending attributes into the stream.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return void
	 */
	protected function writePendingAttributes() {
		$s=$this->_stream;
		foreach($this->_attributes as $name=>$value) {
			$s->write(' '.$name.'="'.$this->escape($value, true).'"');
		}
		$this->_attributes=array();
	}
	
	/**
	 * Writes the begin of a non-empty tag.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string Name of the tag.
	 */
	public function writeStartTag($name) {
		$this->_stream->write('<'.$name);
		$this->writePendingAttributes();
		$this->_stream->write('>');
		
		$this->_tagStack[]=$name;
		return $this;
	}
	
	/**
	 * Writes an end tag with the name of the last opened start tag.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 */
	public function writeEndTag() {
		$name=array_pop($this->_tagStack);
		if($name===NULL) {
			throw new Curly_Stream_Exception('Invalid method call: No opened tag was found');
		}
		
		$this->_stream->write('</'.$name.'>');
		return $this;
	}
	
	/**
	 * Writes an empty tag.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string Name of the tag.
	 */
	public function writeTag($name) {
		$this->_stream->write('<'.$name);
		$this->writePendingAttributes();
		$this->_stream->write(' />');
		return $this;
	}
	
	/**
	 * Writes an processing instruction.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string Target name for the instruction
	 * @param string Main part of the instruction
	 */
	public function writeProcessingInstruction($name, $body) {
		$this->_stream->write('<?'.$name.' '.$body.' ?>');
		return $this;
	}
	
	/**
	 * Writes a comment.
	 * 
	 * @throws Curly_Stream_Exception
	 * @return Curly_Stream_Xml_Output
	 * @param string
	 */
	public function writeComment($data) {
		$this->_stream->write('<!--'.$data.'-->');
		return $this;
	}
	
}
