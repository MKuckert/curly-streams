<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Curly_Stream_Capsule_InputSubclass extends Curly_Stream_Capsule_Input {}

/**
 * Curly_Stream_Capsule_InputTest test case.
 */
class Curly_Stream_Capsule_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Base_ReaderSubclass
	 */
	private $reader;
	
	public function testRead() {
		$memory=new Curly_Stream_Memory_Input('0123456789ABCDEF');
		$reader=new Curly_Stream_Capsule_InputSubclass($memory);
		
		$this->assertEquals($reader->read(2), '01');
		$reader->skip(2);
		
		$this->assertEquals($memory->read(2), '45');
		
		$this->assertTrue($reader->available());
		
		$this->assertEquals($reader->getStream(), $memory);
		
		while($memory->available()) {
			$memory->read(1);
		}
		
		$this->assertFalse($reader->available());
	}
	
}

