<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Binary_Output test case.
 */
class Curly_Stream_Binary_OutputTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Binary_Output
	 */
	private $bwriter;
	
	public function testWrite() {
		$this->bwriter=new Curly_Stream_Binary_Output(
			new Curly_Stream_Buffered_Output(
				new Curly_Stream_File_Output(
					dirname(__FILE__).'/testfile.bigendian-testcase',
					Curly_Stream_File::OPEN|Curly_Stream_File::CREATE
				)
			)
		);
		
		$this->bwriter->writeByte(1);
		$this->bwriter->writeByte(0x0A);
		$this->bwriter->writeShort(2);
		$this->bwriter->writeByte(0x0A);
		$this->bwriter->writeInteger(3);
		$this->bwriter->writeInteger(0x0A);
		$this->bwriter->writeInteger(4);
		$this->bwriter->writeByte(0x0A);
		$this->bwriter->writeInteger(5);
	}
	
}
