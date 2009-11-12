<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Empty_Input test case.
 */
class Curly_Stream_Empty_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Empty_Input
	 */
	private $stream=NULL;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->stream=new Curly_Stream_Empty_Input();
	}
	
	public function testRead() {
		$this->assertEquals('', $this->stream->read(100));
	}
	
	public function testAlwaysNotAvailable() {
		$this->assertFalse($this->stream->available());
		$this->assertEquals('', $this->stream->read(100));
		$this->assertFalse($this->stream->available());
	}
	
	public function testSkip() {
		$this->assertFalse($this->stream->available());
		$this->stream->skip(100);
		$this->assertFalse($this->stream->available());
	}
	
}

