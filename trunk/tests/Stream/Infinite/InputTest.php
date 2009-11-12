<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Curly_Stream_LosesDataAfterOneRead implements Curly_Stream_Input, Curly_Stream_Seekable {
	
	private $data='';
	
	public function __construct($data) {
		$this->data=$data;
	}
	
	public function available() {
		return $this->data!=='';
	}
	
	public function read($len) {
		$retval=substr($this->data, 0, $len);
		$this->data=(string)substr($this->data, $len);
		
		return $retval;
	}
	
	public function skip($len) {
		$this->read($len);
	}
	
	public function seek($offset, $origin=self::ORIGIN_CURRENT) {
		return true;
	}
	
	public function tell() {
		return 0;
	}
	
}

/**
 * Curly_Stream_Infinite_Input test case.
 */
class Curly_Stream_Infinite_InputTest extends PHPUnit_Framework_TestCase {
	
	const BUFFER='0123456789';
	
	/**
	 * @var Curly_Stream_Infinite_Input
	 */
	private $stream=NULL;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->stream=new Curly_Stream_Infinite_Input(
			new Curly_Stream_Memory_Input(self::BUFFER)
		);
	}
	
	public function testRead() {
		$this->assertEquals(
			self::BUFFER.self::BUFFER.substr(self::BUFFER, 0, 2),
			$this->stream->read(22)
		);
		
		$this->assertEquals(
			substr(self::BUFFER, 2),
			$this->stream->read(strlen(self::BUFFER)-2)
		);
		
		$buf=self::BUFFER;
		for($i=0; $i<50; $i++) {
			$this->assertEquals(
				$buf[$i%strlen(self::BUFFER)],
				$this->stream->read(1)
			);
		}
	}
	
	public function testAlwaysAvailable() {
		$this->assertTrue($this->stream->available());
		
		for($i=0; $i<50; $i++) {
			$this->stream->read(1);
			$this->assertTrue($this->stream->available());
		}
	}
	
	public function testReadWithModifiedStream() {
		$this->stream=new Curly_Stream_Infinite_Input(
			new Curly_Stream_LosesDataAfterOneRead(self::BUFFER)
		);
		
		$this->assertTrue($this->stream->available());
		
		$this->assertEquals(
			self::BUFFER,
			$this->stream->read(100)
		);
		
		$this->assertFalse($this->stream->available());
	}
	
}

