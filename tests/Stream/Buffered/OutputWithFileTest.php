<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Buffered_OutputWithFileTest test case.
 */
class Curly_Stream_Buffered_OutputWithFileTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var string
	 */
	private $outFilepath;
	
	/**
	 * @var Curly_Stream_Buffered_Output
	 */
	private $bufferedStream;
	
	/**
	 * @var Curly_Stream_File_Output
	 */
	private $fileStream;
	
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
		
		$this->bufferedStream=NULL;
		$this->fileStream=NULL;
	}
	
	public function testOpen() {
		$this->fileStream = new Curly_Stream_File_Output($this->outFilepath);
		$this->bufferedStream = new Curly_Stream_Buffered_Output($this->fileStream);
	}
	
	public function testCapsuledStream() {
		$this->testOpen();
		$this->assertEquals($this->fileStream, $this->bufferedStream->getCapsuledStream());
	}
	
	public function testWrite() {
		$this->testOpen();
		
		$this->bufferedStream->write('012345');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
		
		$this->bufferedStream->write('AB');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
		
		$bufRemaining=Curly_Stream_Buffered_Output::BUFFERSIZE-8;
		
		$this->bufferedStream->write(str_repeat(' ', $bufRemaining-1));
		$this->assertEquals(file_get_contents($this->outFilepath), '');
		
		$this->bufferedStream->write('X');
		$this->assertEquals(Curly_Stream_Buffered_Output::BUFFERSIZE, filesize($this->outFilepath));
	}
	
	public function testFlush() {
		$this->testOpen();
		
		$this->bufferedStream->write('012345');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
		
		$this->bufferedStream->flush();
		
		$this->assertEquals(file_get_contents($this->outFilepath), '012345');
	}
	
	public function testImplicitFlushOfDestruct() {
		$this->testOpen();
		
		$this->bufferedStream->write('012345');
		
		$this->assertEquals(file_get_contents($this->outFilepath), '');
		
		$this->bufferedStream=NULL;
		
		$this->assertEquals(file_get_contents($this->outFilepath), '012345');
	}
	
}

