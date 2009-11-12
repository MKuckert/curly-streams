<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_File_Input test case.
 */
class Curly_Stream_FileTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var string
	 */
	private $inFilepath;
	
	/**
	 * @var string
	 */
	private $outFilepath;
	
	/**
	 * @var Curly_Stream_File
	 */
	private $stream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->inFilepath=dirname(__FILE__).'inputfile.txt';
		file_put_contents($this->inFilepath, '012345678901234567890123456789');
		$this->stream=new Curly_Stream_File($this->inFilepath);
		
		$this->outFilepath=dirname(__FILE__).'outputfile.txt';
		file_put_contents($this->outFilepath, '');
	}
	
	protected function tearDown() {
		parent::tearDown();
		
		$this->stream=NULL;
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
	
	public function testReadWithNegativeLength() {
		try {
			$this->stream->read(-1);
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('is invalid for a read operation. Only positive values area valid.', $ex->getMessage());
		}
	}
	
	public function testSkipWithNegativeLength() {
		try {
			$this->stream->skip(-1);
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('is invalid for a skip operation. Only positive values area valid.', $ex->getMessage());
		}
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
		$this->markTestSkipped('This test is known as problematic. See doc comment in Curly_Stream_File');
		
		$this->assertEquals($this->stream->read(5), '01234');
		$this->stream->seek(0, Curly_Stream_Seekable::ORIGIN_END);
		$this->assertFalse($this->stream->available());
	}
	
	public function testOpen() {
		$this->stream = new Curly_Stream_File($this->outFilepath);
		
		$arr=(array)$this->stream;
		$this->assertTrue(is_resource($arr["\0*\0_handle"]));
	}
	
	public function testOpenCreate() {
		unlink($this->outFilepath);
		
		$this->stream = new Curly_Stream_File($this->outFilepath, Curly_Stream_File::CREATE);
		
		$this->assertFileExists($this->outFilepath);
	}

	public function testOpenTruncate() {
		file_put_contents($this->outFilepath, '0123');
		
		$this->stream = new Curly_Stream_File($this->outFilepath, Curly_Stream_File::OPEN|Curly_Stream_File::TRUNCATE);
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
	}

	public function testOpenInvalidCall() {
		try {
			new Curly_Stream_File($this->outFilepath, 0);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('Invalid open mode given. At least the open or create mode has to be specified', $ex->getMessage());
		}
	}
	
	public function testOpenWithoutFile() {
		unlink($this->outFilepath);
		
		try {
			$this->stream = new Curly_Stream_File($this->outFilepath, Curly_Stream_File::OPEN);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('does not exist and can not been opened', $ex->getMessage());
		}
	}
	
	public function testWrite() {
		$this->stream=new Curly_Stream_File($this->outFilepath, Curly_Stream_File::CLEAN);
		$this->stream->write('012345');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '012345');
		
		$this->stream->seek(2, Curly_Stream_Seekable::ORIGIN_BEGIN);
		$this->stream->write('AB');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '01AB45');
	}
	
	public function testFlush() {
		$this->stream=new Curly_Stream_File($this->outFilepath, Curly_Stream_File::CLEAN);
		$this->stream->flush(); // Yep, this is all
	}
	
	public function testOpenWithStringMode() {
		foreach(array('r', 'r+', 'w'. 'w+', 'a', 'a+') as $mode) {
			new Curly_Stream_File($this->outFilepath, $mode);
		}
		foreach(array('x', 'x+') as $mode) {
			unlink($this->outFilepath);
			new Curly_Stream_File($this->outFilepath, $mode);
		}
	}
	
}

