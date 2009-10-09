<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Memory_Output test case.
 */
class Curly_Stream_Memory_OutputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Memory_Output
	 */
	private $stream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->stream = new Curly_Stream_Memory_Output();
	}
	
	public function testWrite() {
		$this->stream->write('0123');
		$this->stream->write('4567');
		
		$this->assertEquals($this->stream->getBuffer(), '01234567');
	}
	
	public function testSeek() {
		$this->stream->write('0123');
		$this->stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
		$this->stream->write('AB');
		
		$this->assertEquals($this->stream->getBuffer(), 'AB23');
	}

}

