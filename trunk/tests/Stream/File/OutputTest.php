<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_File_Output test case.
 */
class Curly_Stream_File_OutputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var string
	 */
	private $outFilepath;
	
	/**
	 * @var Curly_Stream_File_Output
	 */
	private $stream;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->outFilepath=dirname(__FILE__).'outputfile.txt';
		file_put_contents($this->outFilepath, '');
	}
	
	protected function tearDown() {
		parent::tearDown();
		
		$this->stream=NULL;
	}
	
	public function testOpen() {
		$this->stream = new Curly_Stream_File_Output($this->outFilepath);
		
		$arr=(array)$this->stream;
		$this->assertTrue(is_resource($arr["\0*\0_handle"]));
	}
	
	public function testOpenCreate() {
		unlink($this->outFilepath);
		
		$this->stream = new Curly_Stream_File_Output($this->outFilepath, Curly_Stream_File::CREATE);
		
		$this->assertFileExists($this->outFilepath);
	}

	public function testOpenTruncate() {
		file_put_contents($this->outFilepath, '0123');
		
		$this->stream = new Curly_Stream_File_Output($this->outFilepath, Curly_Stream_File::OPEN|Curly_Stream_File::TRUNCATE);
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
	}

	public function testOpenInvalidCall() {
		try {
			new Curly_Stream_File_Output($this->outFilepath, 0);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('Invalid open mode given. At least the open or create mode has to be specified', $ex->getMessage());
		}
	}
	
	public function testOpenWithoutFile() {
		unlink($this->outFilepath);
		
		try {
			$this->stream = new Curly_Stream_File_Output($this->outFilepath, Curly_Stream_File::OPEN);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Exception $ex) {
			$this->assertContains('does not exist and can not been opened', $ex->getMessage());
		}
	}
	
	public function testWrite() {
		$this->stream=new Curly_Stream_File_Output($this->outFilepath, Curly_Stream_File::CLEAN);
		$this->stream->write('012345');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '012345');
		
		$this->stream->seek(2, Curly_Stream_Seekable::ORIGIN_BEGIN);
		$this->stream->write('AB');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '01AB45');
	}
	
	public function testFlush() {
		$this->stream=new Curly_Stream_File_Output($this->outFilepath, Curly_Stream_File::CLEAN);
		$this->stream->flush(); // Yep, this is all
	}
	
}

