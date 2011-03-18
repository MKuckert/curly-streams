<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Curly_Stream_WrapperTest_InputMock extends Curly_Stream_Memory {
	public function __construct() {
		throw new Curly_Stream_Exception('This really should happen!');
	}
}

/**
 * Curly_Stream_Wrapper test case.
 */
class Curly_Stream_WrapperTest extends PHPUnit_Framework_TestCase {
	
	public function errorHandler($errno, $errstr) {
		throw new ErrorException($errstr, $errno);
	}
	
	protected function setUp() {
		parent::setUp();
		set_error_handler(array($this, 'errorHandler'));
	}
	
	public function testMemoryInputStream() {
		$expect='0123456789';
		$stream=new Curly_Stream_Memory_Input($expect);
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', $stream);
		
		$handle=fopen('memory-in://', 'r');
		$this->assertEquals(
			$expect,
			fread($handle, 10)
		);
		
		$this->assertFalse($stream->available());
	}
	
	public function testLargeDataWithInternalPhpCaching() {
		$expect=str_repeat('0123456789', 2000);	// 20000 in total
		$stream=new Curly_Stream_Memory_Input($expect);
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', $stream);
		
		$handle=fopen('memory-in://', 'r');
		$this->assertEquals(
			substr($expect, 0, 10),
			fread($handle, 10)
		);
		$this->assertTrue($stream->available());
		
		$this->assertEquals(
			substr($expect, 10, 990),
			fread($handle, 990)
		);
		$this->assertTrue($stream->available());
		
		$this->assertEquals(
			substr($expect, 1000, 19000),
			fread($handle, 20000)
		);
		
		// Trigger feof
		fread($handle, 1);
		
		$this->assertFalse($stream->available());
	}
	
	public function testMemoryInputStreamByClass() {
		$expect='0123456789';
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', 'Curly_Stream_Memory_Input');
		
		$handle=fopen('memory-in://'.$expect, 'r');
		$this->assertEquals(
			$expect,
			fread($handle, 10)
		);
	}
	
	public function testMemoryOutputStream() {
		$expect='0123456789';
		$stream=new Curly_Stream_Memory_Output();
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-out', $stream);
		
		$handle=fopen('memory-out://', 'r');
		fwrite($handle, $expect);
		$this->assertEquals(
			$expect,
			$stream->getBuffer()
		);
	}
	
	public function testWriteToInputStream() {
		$expect='0123456789';
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', 'Curly_Stream_Memory_Input');
		
		$handle=fopen('memory-in://'.$expect, 'r');
		$this->assertEquals(0, fwrite($handle, $expect));
	}
	
	public function testReadFromOutputStream() {
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-out', 'Curly_Stream_Memory_Output');
		
		$handle=fopen('memory-out://', 'r');
		$this->assertEquals('', fread($handle, 10));
	}
	
	public function testSeek() {
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('file-out', 'Curly_Stream_File_Output');
		
		$file=dirname(__FILE__).'/wrappertest.txt';
		
		$handle=fopen('file-out://'.$file, Curly_Stream_File::CLEAN);
		fwrite($handle, 'testdata');
		
		fseek($handle, 0, SEEK_SET);
		$this->assertEquals(0, ftell($handle));
		
		fseek($handle, 0, SEEK_END);
		$this->assertEquals(8, ftell($handle));
		
		fseek($handle, -2, SEEK_CUR);
		$this->assertEquals(6, ftell($handle));
		
		fseek($handle, 2, SEEK_SET);
		$this->assertEquals(2, ftell($handle));
		
		fseek($handle, 2, SEEK_CUR);
		$this->assertEquals(4, ftell($handle));
	}
	
	public function testOpenDirectly() {
		$expect='0123456789';
		$wrapper=new Curly_Stream_Wrapper();
		$this->assertTrue($wrapper->stream_open('memory-in://'.$expect, 'r', STREAM_REPORT_ERRORS));
		$this->assertEquals($expect, $wrapper->stream_read(10));
		$this->assertTrue($wrapper->stream_eof());
	}
	
	public function testOpenDirectlyWithInvalidPath() {
		$wrapper=new Curly_Stream_Wrapper();
		
		try {
			$wrapper->stream_open('this-is-invalid', 'r', STREAM_REPORT_ERRORS);
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('Invalid path given. No protocol was found', $ex->getMessage());
		}
	}
	
	public function testOpenDirectlyWithUnregisteredProtocol() {
		$wrapper=new Curly_Stream_Wrapper();
		
		try {
			$wrapper->stream_open('you-really-should-not-have-such-an-protocol://', 'r', STREAM_REPORT_ERRORS);
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('No stream instance or class was found for the given protocol ', $ex->getMessage());
		}
	}
	
	public function testOpenDirectlyWithFailingStreamCtr() {
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', 'Curly_Stream_WrapperTest_InputMock');
		
		$wrapper=new Curly_Stream_Wrapper();
		try {
			$wrapper->stream_open('memory-in://', 'r', STREAM_REPORT_ERRORS);
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('This really should happen!', $ex->getMessage());
		}
	}
	
	public function testCloseAndInteract() {
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', 'Curly_Stream_Memory_Input');
		
		$wrapper=new Curly_Stream_Wrapper();
		$wrapper->stream_open('memory-in://', 'r', STREAM_REPORT_ERRORS);
		$wrapper->stream_close();
		
		try {
			$wrapper->stream_read(1);
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('This stream has been closed and is not readable anymore', $ex->getMessage());
		}
		
		try {
			$wrapper->stream_write('DATA');
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('This stream has been closed and is not writable anymore', $ex->getMessage());
		}
	}
	
	public function testDirectlyWriteToInputStream() {
		$expect='0123456789';
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-in', 'Curly_Stream_Memory_Input');
		
		$wrapper=new Curly_Stream_Wrapper();
		$wrapper->stream_open('memory-in://'.$expect, 'r', STREAM_REPORT_ERRORS);
		
		try {
			$wrapper->stream_write('DATA');
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('This stream is not writable', $ex->getMessage());
		}
	}
	
	public function testDirectlyReadFromOutputStream() {
		Curly_Stream_Wrapper_Registry::getGlobalInstance()
			->register('memory-out', 'Curly_Stream_Memory_Output');
		
		$wrapper=new Curly_Stream_Wrapper();
		$wrapper->stream_open('memory-out://', 'r', STREAM_REPORT_ERRORS);
	
		try {
			$wrapper->stream_read(1);
			$this->fail('Expected exception');
		}
		catch(ErrorException $ex) {
			$this->assertContains('This stream is not readable', $ex->getMessage());
		}
	}
	
}
