<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Memory_Input test case.
 */
class Curly_Stream_Memory_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Memory_Input
	 */
	private $numberStream;
	
	/**
	 * @var Curly_Stream_Memory_Input
	 */
	private $emptyStream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$data=implode('', range(0, 9));
		$data=str_repeat($data, 3);
		$this->numberStream = new Curly_Stream_Memory_Input($data);
		
		$this->emptyStream = new Curly_Stream_Memory_Input('');
	}
	
	public function testAvailable() {
		$this->assertTrue($this->numberStream->available());
		$this->assertFalse($this->emptyStream->available());
	}
	
	public function testRead() {
		$this->assertEquals($this->numberStream->read(0), '');
		$this->assertEquals($this->numberStream->read(1), '0');
		$this->assertEquals($this->numberStream->read(3), '123');
		$this->assertEquals($this->numberStream->read(10), '4567890123');
		$this->assertEquals($this->numberStream->read(20), '4567890123456789');
		$this->assertEquals($this->numberStream->read(1), '');
		
		$this->assertEquals($this->emptyStream->read(1), '');
	}
	
	public function testReadOverEnd() {
		$this->numberStream->read(29);
		$this->assertEquals($this->numberStream->read(1000), '9');
	}
	
	public function testSkip() {
		$this->assertEquals($this->numberStream->read(0), '');
		$this->assertEquals($this->numberStream->read(1), '0');
		$this->numberStream->skip(3);
		$this->assertEquals($this->numberStream->read(10), '4567890123');
		$this->assertEquals($this->numberStream->read(11), '45678901234');
		$this->numberStream->skip(5);
		$this->assertEquals($this->numberStream->read(1), '');
	}
	
	public function testSeek() {
		$this->assertEquals($this->numberStream->read(5), '01234');
		$this->numberStream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
		$this->assertEquals($this->numberStream->read(5), '01234');
		$this->numberStream->seek(0, Curly_Stream_Seekable::ORIGIN_END);
		$this->assertFalse($this->numberStream->available());
		$this->assertEquals($this->numberStream->read(5), '');
	}

}

