<?php

require_once 'PHPUnit/Framework/TestCase.php';

class OtherNamespace_F extends Curly_Stream_File {}
class Curly_Stream_FactoryTest_Allocator extends Curly_Stream_Factory_Allocator {
	public $called=false;
	public function createInstance($class, array $args) {
		$this->called=true;
		return parent::createInstance($class, $args);
	}
}

/**
 * Curly_Stream_Factory test case.
 */
class Curly_Stream_FactoryTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Curly_Stream_Factory
	 */
	private $factory=NULL;
	
	protected function setUp() {
		parent::setUp();
		$this->factory=new Curly_Stream_Factory();
	}
	
	public function testCreateFileStream() {
		$stream=$this->factory->createStream('file', __FILE__);
		$this->assertTrue($stream instanceof Curly_Stream_File);
		
		$stream=$this->factory->createStream('FILE', __FILE__);
		$this->assertTrue($stream instanceof Curly_Stream_File);
	}
	
	public function testCreateFileInputStream() {
		$stream=$this->factory->createInputStream('file', __FILE__);
		$this->assertTrue($stream instanceof Curly_Stream_File_Input);
	}
	
	public function testCreateFileOutputStream() {
		$stream=$this->factory->createOutputStream('file', __FILE__);
		$this->assertTrue($stream instanceof Curly_Stream_File_Output);
	}
	
	public function testCreateStreamWithArrayArg() {
		$stream=$this->factory->createOutputStream('file', array(
			__FILE__,
			Curly_Stream_File::OPEN
		));
		$this->assertTrue($stream instanceof Curly_Stream_File_Output);
	}
	
	public function testTryCreateUnknownStream() {
		$stream=$this->factory->createStream('unknown');
		$this->assertNull($stream);
		
		$stream=$this->factory->createInputStream('unknown');
		$this->assertNull($stream);
		
		$stream=$this->factory->createOutputStream('unknown');
		$this->assertNull($stream);
	}
	
	public function testCreateStreamOfOtherNamespace() {
		$this->assertEquals(1, count($this->factory->getLoadNamespaces()));
		$this->factory->addLoadNamespace('OtherNamespace');
		$this->assertEquals(2, count($this->factory->getLoadNamespaces()));
		$stream=$this->factory->createStream('F', __FILE__);
		$this->assertTrue($stream instanceof OtherNamespace_F);
	}
	
	public function testRemoveOtherNamespace() {
		$this->factory->addLoadNamespace('OtherNamespace');
		$stream=$this->factory->createStream('F', __FILE__);
		$this->assertTrue($stream instanceof OtherNamespace_F);
		
		$this->factory->removeLoadNamespace('OtherNamespace');
		$this->assertEquals(1, count($this->factory->getLoadNamespaces()));
		$stream=$this->factory->createStream('F', __FILE__);
		$this->assertNull($stream);
	}
	
	public function testSetAllocator() {
		$allocator=new Curly_Stream_FactoryTest_Allocator();
		
		$this->factory->setAllocator($allocator);
		$this->assertEquals($allocator, $this->factory->getAllocator());
	
		$stream=$this->factory->createOutputStream('file', __FILE__);
		$this->assertTrue($stream instanceof Curly_Stream_File_Output);
		$this->assertTrue($allocator->called);
	}
	
}

