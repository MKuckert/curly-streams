<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Binary_Input test case.
 */
class Curly_Stream_Binary_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Binary_Input
	 */
	private $breader;
	
	public function testBigEndian() {
		$this->breader=new Curly_Stream_Binary_Input(
			new Curly_Stream_Buffered_Input(
				new Curly_Stream_File_Input(
					dirname(__FILE__).'/testfile.bigendian'
				)
			)
		, Curly_Stream::ENDIAN_BIG);
		
		$this->doRead();
	}
	
	public function testLittleEndian() {
		$this->breader=new Curly_Stream_Binary_Input(
			new Curly_Stream_Buffered_Input(
				new Curly_Stream_File_Input(
					dirname(__FILE__).'/testfile.littleendian'
				)
			)
		, Curly_Stream::ENDIAN_LITTLE);
		
		$this->doRead();
	}
	
	protected function doRead() {
		// Byte
		$this->assertEquals($this->breader->readByte(), 1);
		
		// Linebreak
		$this->assertEquals($this->breader->readByte(), 0x0A);
		
		// Short
		$this->assertEquals($this->breader->readShort(), 2);
		
		// Linebreak
		$this->assertEquals($this->breader->readByte(), 0x0A);
		
		// Integer
		$this->assertEquals($this->breader->readInteger(), 3);
		$this->assertEquals($this->breader->readByte(), 0x0A);
		$this->assertEquals($this->breader->readInteger(), 4);
		$this->assertEquals($this->breader->readByte(), 0x0A);
		$this->assertEquals($this->breader->readInteger(), 5);
	}
	
	public function testReadNoDataEdgeCases() {
		$breader=new Curly_Stream_Binary_Input(
			new Curly_Stream_Memory_Input('')
		);
		$this->assertNull($breader->readByte(), NULL);
		
		$breader=new Curly_Stream_Binary_Input(
			new Curly_Stream_Memory_Input(chr(0))
		);
		$this->assertNull($breader->readShort(), NULL);
		
		$breader=new Curly_Stream_Binary_Input(
			new Curly_Stream_Memory_Input(chr(0))
		);
		$this->assertNull($breader->readInteger(), NULL);
	}
	
}

