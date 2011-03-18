<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Base64_Input test case.
 */
class Curly_Stream_Base64_InputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
	}
	
	public function testRead() {
		$in='abcdefghijklmnopqrstuvwxyz';
		$assert=base64_encode($in);
		$stream=new Curly_Stream_Memory_Input($in);
		$base64=new Curly_Stream_Base64_Encode_Input($stream);
		
		$read=$base64->read(10);
		$this->assertEquals(substr($assert, 0, 10), $read);
	}
	
}
