<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Buffered_Input test case.
 */
class Curly_Stream_Buffered_InputWithMemoryTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Buffered_Input
	 */
	private $buffered;
	
	/**
	 * @var Curly_Stream_Memory_Input
	 */
	private $orig;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$data=implode('', range(0, 9));
		$data=str_repeat($data, 3);
		$this->orig=new Curly_Stream_Memory_Input($data);
		
		$this->buffered=new Curly_Stream_Buffered_Input($this->orig);
	}
	
	public function testAvailable() {
		$this->assertTrue($this->buffered->available());
	}
	
	public function testRead() {
		// Fill the buffer
		$this->assertEquals($this->buffered->read(1), '0');
		
		// Everything is read from the underlying stream
		$this->assertEquals($this->orig->tell(), 30);
		
		$this->assertEquals($this->buffered->read(10), '1234567890');
		
		$arr=(array)$this->buffered;
		$this->assertEquals(
			$arr["\0Curly_Stream_Buffered_Input\0_buffer"],
			'1234567890123456789'
		);
	}
	
	public function testSkip() {
		$this->buffered->skip(1);
		
		// Everything is skipped/read from the underlying stream
		$this->assertEquals($this->orig->tell(), 30);
		
		$this->buffered->skip(10);
		
		$arr=(array)$this->buffered;
		$this->assertEquals(
			$arr["\0Curly_Stream_Buffered_Input\0_buffer"],
			'1234567890123456789'
		);
	}
	
	public function testReadWithBuffersize() {
		$this->buffered=new Curly_Stream_Buffered_Input($this->orig, 5);
		
		// Fill the buffer
		$this->assertEquals($this->buffered->read(1), '0');
		
		$this->assertEquals($this->orig->tell(), 5);
		
		$this->assertEquals($this->buffered->read(10), '1234567890');
		
		// The buffer should be empty, because we read more data than bufferSize*2
		$arr=(array)$this->buffered;
		$this->assertEquals(
			$arr["\0Curly_Stream_Buffered_Input\0_buffer"],
			''
		);
		
		// Fill the buffer
		$this->assertEquals($this->buffered->read(1), '1');
		$this->assertEquals($this->buffered->read(8), '23456789');
		
		// We read the remaining 4 bytes of the first read operation and 4
		// bytes of a newly filled buffer, so 1 byte remains
		$arr=(array)$this->buffered;
		$this->assertEquals(
			$arr["\0Curly_Stream_Buffered_Input\0_buffer"],
			'0'
		);
	}

}

