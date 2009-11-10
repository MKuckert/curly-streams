<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Append_Input test case.
 */
class Curly_Stream_Append_InputTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var string
	 */
	private $filepath;
	
	/**
	 * @var Curly_Stream_File_Input
	 */
	private $fstream1;
	
	/**
	 * @var Curly_Stream_File_Input
	 */
	private $fstream2;
	
	/**
	 * @var Curly_Stream_Memory_Input
	 */
	private $mstream;
	
	/**
	 * @var Curly_Stream_Append_Input
	 */
	private $append;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->filepath=dirname(__FILE__).'inputfile.txt';
		file_put_contents($this->filepath, '0123456789');
		
		$this->fstream1=new Curly_Stream_File_Input($this->filepath);
		$this->fstream2=new Curly_Stream_File_Input($this->filepath);
		$this->mstream=new Curly_Stream_Memory_Input('ABCDEFGHIJ');
		
		$this->append=new Curly_Stream_Append_Input($this->fstream1);
		$this->append
			->append($this->mstream)
			->append($this->fstream2);
	}
	
	public function testCtr() {
		$stream=new Curly_Stream_Append_Input();
		$this->assertEquals($stream->read(10), '');
		
		$stream=new Curly_Stream_Append_Input($this->mstream);
		$this->assertEquals($stream->read(10), 'ABCDEFGHIJ');
	}
	
	public function testAvailable() {
		$this->assertTrue($this->append->available());
		$this->assertEquals($this->append->read(9), '012345678');
		$this->assertTrue($this->append->available());
		$this->assertEquals($this->append->read(4), '9ABC');
		$this->assertTrue($this->append->available());
		$this->assertEquals($this->append->read(7), 'DEFGHIJ');
		$this->assertTrue($this->append->available());
		$this->assertEquals($this->append->read(9), '012345678');
		$this->assertTrue($this->append->available());
		$this->assertEquals($this->append->read(1), '9');
		//$this->assertFalse($this->append->available());
		$this->assertEquals($this->append->read(1), '');
		$this->assertFalse($this->append->available());
	}
	
	public function testAvailableIfAllStreamsAreUnavailable() {
		// Read all streams before the append stream can do that
		$this->fstream1->read(1000);
		$this->fstream2->read(1000);
		$this->mstream->read(1000);
		
		$dump=(array)$this->append;
		$this->assertGreaterThan(0, $dump["\0Curly_Stream_Append_Input\0_streamsCount"]);
		
		$this->assertFalse($this->append->available());
	}
	
	public function testReadInOnce() {
		$this->assertEquals($this->append->read(100), '0123456789ABCDEFGHIJ0123456789');
	}
	
	public function testReadWithInvalidValue() {
		try {
			$this->append->read(-1);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('is invalid for a read operation. Only positive values area valid.', $ex->getMessage());
		}
	}
	
	public function testSkip() {
		$this->append->skip(100);
		$this->assertFalse($this->append->available());
	}

}

