<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_File_Input test case.
 */
class Curly_Stream_File_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var string
	 */
	private $inFilepath;
	
	/**
	 * @var Curly_Stream_File_Input
	 */
	private $stream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->inFilepath=dirname(__FILE__).'inputfile.txt';
		file_put_contents($this->inFilepath, '012345678901234567890123456789');
		$this->stream=new Curly_Stream_File_Input($this->inFilepath);
	}
	
	public function testRead() {
		$this->assertTrue($this->stream->available());
		
		$this->assertEquals($this->stream->read(0), '');
		$this->assertEquals($this->stream->read(1), '0');
		$this->assertEquals($this->stream->read(3), '123');
		$this->assertEquals($this->stream->read(10), '4567890123');
		$this->assertEquals($this->stream->read(20), '4567890123456789');
		$this->assertFalse($this->stream->available());
		$this->assertEquals($this->stream->read(1), '');
	}
	
	public function testReadOverEnd() {
		$this->stream->read(29);
		$this->assertEquals($this->stream->read(1000), '9');
	}
	
	public function testSkip() {
		$this->assertEquals($this->stream->read(0), '');
		$this->assertEquals($this->stream->read(1), '0');
		$this->stream->skip(3);
		$this->assertEquals($this->stream->read(10), '4567890123');
		$this->assertEquals($this->stream->read(11), '45678901234');
		$this->stream->skip(5);
		$this->assertEquals($this->stream->read(1), '');
	}
	
	public function testSeek() {
		$this->assertEquals($this->stream->read(5), '01234');
		$this->stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
		$this->assertEquals($this->stream->read(5), '01234');
		$this->stream->seek(0, Curly_Stream_Seekable::ORIGIN_END);
		$this->assertEquals($this->stream->read(5), '');
		$this->assertFalse($this->stream->available());
	}
	
	public function testSeekToEnd() {
		$this->markTestSkipped('This test is known is problematic. See doc comment in Curly_Stream_File_Input');
		
		$this->assertEquals($this->stream->read(5), '01234');
		$this->stream->seek(0, Curly_Stream_Seekable::ORIGIN_END);
		$this->assertFalse($this->stream->available());
	}
	
}

