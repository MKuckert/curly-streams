<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Xml_OutputTest test case.
 */
class Curly_Stream_Xml_OutputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Memory_Output
	 */
	private $mstream;
	
	/**
	 * @var Curly_Stream_Xml_Output
	 */
	private $stream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->mstream = new Curly_Stream_Memory_Output();
		$this->stream = new Curly_Stream_Xml_Output(
			$this->mstream
		);
	}
	
	public function testWriteSimpleDoc() {
		$this->stream
			->writeHeadDeclaration()
			->writeStartTag('root')
			->writeStartTag('data')
			->write('DATA')
			->writeEndTag()
			->writeEndTag();
		
		$this->assertEquals(
			'<?xml version="1.0" encoding="UTF-8" ?>'."\n".
			'<root><data>DATA</data></root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteStandalone() {
		$this->stream
			->writeHeadDeclaration(true)
			->writeStartTag('root')
			->writeStartTag('data')
			->write('DATA')
			->writeEndTag()
			->writeEndTag();
		
		$this->assertEquals(
			'<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'."\n".
			'<root><data>DATA</data></root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteSimpleAttributes() {
		$this->stream->writeHeadDeclaration();
		$this->stream->addAttribute('x', 'y');
		$this->stream->addAttribute('ABC', 'DEF');
		$this->stream->writeStartTag('root');
		$this->stream->addAttribute('xmlns:c', 'http://curlybracket.de/');
		$this->stream->writeStartTag('c:data');
		$this->stream->write('DATA');
		$this->stream->writeEndTag();
		$this->stream->writeEndTag();
		
		$this->assertEquals(
			'<?xml version="1.0" encoding="UTF-8" ?>'."\n".
			'<root x="y" ABC="DEF"><c:data xmlns:c="http://curlybracket.de/">DATA</c:data></root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteSimpleAttributesAssoc() {
		$this->stream->writeHeadDeclaration();
		$this->stream->addAttributes(array(
			'x' => 'y',
			'ABC' => 'DEF'
		));
		$this->stream->writeStartTag('root');
		$this->stream->addAttributes(array(
			'xmlns:c'=>'http://curlybracket.de/'
		));
		$this->stream->writeStartTag('c:data');
		$this->stream->write('DATA');
		$this->stream->writeEndTag();
		$this->stream->writeEndTag();
		
		$this->assertEquals(
			'<?xml version="1.0" encoding="UTF-8" ?>'."\n".
			'<root x="y" ABC="DEF"><c:data xmlns:c="http://curlybracket.de/">DATA</c:data></root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testEntityEscaping() {
		$this->stream->writeStartTag('root');
		$this->stream->write('&<>');
		$this->stream->writeEndTag();
		
		$this->assertEquals(
			'<root>&amp;&lt;&gt;</root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testEntityEscapingInAttribute() {
		$this->stream->addAttribute('amp', '&');
		$this->stream->addAttribute('lt', '<');
		$this->stream->addAttribute('gt', '>');
		$this->stream->addAttribute('quot', '"');
		$this->stream->writeTag('root');
		
		$this->assertEquals(
			'<root amp="&amp;" lt="&lt;" gt="&gt;" quot="&quot;" />',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteCData() {
		$this->stream
			->writeStartTag('root')
			->writeCData('&<>"')
			->writeEndTag();
		
		$this->assertEquals(
			'<root><![CDATA[&<>"]]></root>',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteEntity() {
		$this->stream
			->writeEntity('myEntity');
		
		$this->assertEquals(
			'&myEntity;',
			$this->mstream->getBuffer()
		);
	}
	
	public function testWriteComment() {
		$this->stream
			->writeComment('This is my comment');
		
		$this->assertEquals(
			'<!--This is my comment-->',
			$this->mstream->getBuffer()
		);
	}
	
}
