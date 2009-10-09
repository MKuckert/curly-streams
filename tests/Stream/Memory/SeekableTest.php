<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Curly_Stream_Memory_SeekableMock extends Curly_Stream_Memory_Seekable {
	public function __construct($data) {
		$this->_data=$data;
		$this->_dataLen=strlen($data);
	}
	public function getPos() {
		return $this->_pos;
	}
}

/**
 * Curly_Stream_Memory_Seekable test case.
 */
class Curly_Stream_Memory_SeekableTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Memory_Seekable
	 */
	private $seekable;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->seekable = new Curly_Stream_Memory_SeekableMock('0123456789ABCDEF');
	}
	
	public function testBase() {
		$this->seekable->seek(0);
		$this->assertEquals($this->seekable->getPos(), 0);
		$this->assertEquals($this->seekable->getPos(), $this->seekable->tell());
		
		$this->seekable->seek(10);
		$this->assertEquals($this->seekable->getPos(), 10);
		$this->assertEquals($this->seekable->getPos(), $this->seekable->tell());
		
		$this->seekable->seek(-10);
		$this->assertEquals($this->seekable->getPos(), 0);
		$this->assertEquals($this->seekable->getPos(), $this->seekable->tell());
	}
	
	public function testOriginCurrent() {
		for($i=0; $i<16; $i++) {
			$this->seekable->seek(1, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->assertEquals($this->seekable->getPos(), $i+1);
		}
		for($i=15; $i>=0; $i--) {
			$this->seekable->seek(-1, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->assertEquals($this->seekable->getPos(), $i);
		}
	}
	
	public function testOriginCurrentInvalid() {
		try {
			$this->seekable->seek(-1, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value -1 is too small to seek to, 0 is the smallest possible value for the current seek origin.', $ex->getMessage());
		}
		
		$this->seekable->seek(10, Curly_Stream_Seekable::ORIGIN_CURRENT);
		
		try {
			$this->seekable->seek(-11, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value -11 is too small to seek to, -10 is the smallest possible value for the current seek origin.', $ex->getMessage());
		}
		
		$this->seekable->seek(6, Curly_Stream_Seekable::ORIGIN_CURRENT);
		
		try {
			$this->seekable->seek(1, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value 1 is too big to seek to, 0 is the biggest possible value for the current seek origin.', $ex->getMessage());
		}
		
		$this->seekable->seek(-16, Curly_Stream_Seekable::ORIGIN_CURRENT);
		
		try {
			$this->seekable->seek(17, Curly_Stream_Seekable::ORIGIN_CURRENT);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value 17 is too big to seek to, 16 is the biggest possible value for the current seek origin.', $ex->getMessage());
		}
	}
	
	public function testOriginBegin() {
		for($i=0; $i<16; $i++) {
			$this->seekable->seek($i, Curly_Stream_Seekable::ORIGIN_BEGIN);
			$this->assertEquals($this->seekable->getPos(), $i);
		}
	}
	
	public function testOriginBeginInvalid() {
		try {
			$this->seekable->seek(-1, Curly_Stream_Seekable::ORIGIN_BEGIN);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('Invalid offset value -1 given. Only positive offset values are valid for the begin seek origin.', $ex->getMessage());
		}
		
		try {
			$this->seekable->seek(17, Curly_Stream_Seekable::ORIGIN_BEGIN);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value 17 is too big to seek to, 16 is the biggest possible value for the begin seek origin.', $ex->getMessage());
		}
	}
	
	public function testOriginEnd() {
		for($i=0; $i<16; $i++) {
			$this->seekable->seek(-$i, Curly_Stream_Seekable::ORIGIN_END);
			$this->assertEquals($this->seekable->getPos(), 16-$i);
		}
	}
	
	public function testOriginEndInvalid() {
		try {
			$this->seekable->seek(1, Curly_Stream_Seekable::ORIGIN_END);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value can only be negative for the end seek origin.', $ex->getMessage());
		}
		
		try {
			$this->seekable->seek(-17, Curly_Stream_Seekable::ORIGIN_END);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('The offset value -17 is too small to seek to, -16 is the smallest possible value for the end seek origin.', $ex->getMessage());
		}
	}
	
}

