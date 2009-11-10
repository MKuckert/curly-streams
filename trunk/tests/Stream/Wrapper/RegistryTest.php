<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_Stream_Wrapper_Registry test case.
 */
class Curly_Stream_Wrapper_RegistryTest extends PHPUnit_Framework_TestCase {
	
	public function testAutocreationOfGlobalInstance() {
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$this->assertTrue($instance instanceof Curly_Stream_Wrapper_Registry);
		
		$instance2=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$this->assertEquals($instance, $instance2);
	}
	
	public function testSetGlobalInstance() {
		$instance=new Curly_Stream_Wrapper_Registry();
		
		$globalInstance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$this->assertFalse($instance===$globalInstance);
		
		Curly_Stream_Wrapper_Registry::setGlobalInstance($instance);
		
		$globalInstance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$this->assertTrue($instance===$globalInstance);
	}
	
	public function testRegisterStreamByClass() {
		$expect=array(
			'memory-in'		=> 'Curly_Stream_Memory_Input',
			'memory-out'	=> 'Curly_Stream_Memory_Output'
		);
		
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		foreach($expect as $name=>$stream) {
			$instance->register($name, $stream);
		}
		
		$this->assertEquals($expect, $instance->getAllRegistered());
	}
	
	public function testRegisterStreamByObject() {
		$expect=array(
			'memory-in'		=> new Curly_Stream_Memory_Input(''),
			'memory-out'	=> new Curly_Stream_Memory_Output()
		);
		
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		foreach($expect as $name=>$stream) {
			$instance->register($name, $stream);
		}
		
		$this->assertEquals($expect, $instance->getAllRegistered());
		
		$instance->unregister('memory-in');
		$instance->unregister('memory-out');
	}
	
	public function testInvalidRegisterStream() {
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		
		try {
			$instance->register('proto', $this);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Wrapper_Exception $ex) {
			$this->assertContains('Invalid stream instance given. InputStream or OutputStream expected', $ex->getMessage());
		}
		
		try {
			$instance->register('proto', 'NonExistentClass');
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Wrapper_Exception $ex) {
			$this->assertContains('does not exist', $ex->getMessage());
		}
		
		try {
			$instance->register('proto', __CLASS__);
			$this->fail('Expected exception');
		}
		catch(Curly_Stream_Wrapper_Exception $ex) {
			$this->assertContains('does neither implement the InputStream nor the OutputStream interface', $ex->getMessage());
		}
	}
	
	public function testUnregister() {
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$instance->register('test-proto-unreg', 'Curly_Stream_Memory_Input');
		
		$reg=$instance->getAllRegistered();
		$this->assertTrue(isset($reg['test-proto-unreg']));
		$this->assertEquals($reg['test-proto-unreg'], 'Curly_Stream_Memory_Input');
		$this->assertTrue(in_array('test-proto-unreg', stream_get_wrappers()));
		
		$this->assertTrue($instance->unregister('test-proto-unreg'));
		
		$reg=$instance->getAllRegistered();
		$this->assertFalse(isset($reg['test-proto-unreg']));
		$this->assertFalse(in_array('test-proto-unreg', stream_get_wrappers()));
	}
	
	public function testUnregisterByStream() {
		$stream=new Curly_Stream_Memory_Input('');
		
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$instance->register('test-proto-unreg', $stream);
		
		$reg=$instance->getAllRegistered();
		$this->assertTrue(isset($reg['test-proto-unreg']));
		$this->assertEquals($reg['test-proto-unreg'], $stream);
		$this->assertTrue(in_array('test-proto-unreg', stream_get_wrappers()));
		
		$this->assertTrue($instance->unregister($stream));
		
		$reg=$instance->getAllRegistered();
		$this->assertFalse(isset($reg['test-proto-unreg']));
		$this->assertFalse(in_array('test-proto-unreg', stream_get_wrappers()));
	}
	
	public function testInvalidUnregisterStream() {
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		
		$this->assertFalse($instance->unregister('this-is-really-no-protocol-you-should-have-registered'));
		$this->assertFalse($instance->unregister($this));
	}
	
	public function testIsRegisteredName() {
		$stream=new Curly_Stream_Memory_Input('');
		
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$instance->register('test-proto-name1', 'Curly_Stream_Memory_Input');
		$instance->register('test-proto-name2', $stream);
		
		$this->assertTrue($instance->isRegisteredName('test-proto-name1'));
		$this->assertTrue($instance->isRegisteredName('test-proto-name2'));
		$this->assertFalse($instance->isRegisteredName('test-proto-name3'));
		
		$instance->unregister('test-proto-name2');
		
		$this->assertTrue($instance->isRegisteredName('test-proto-name1'));
		$this->assertFalse($instance->isRegisteredName('test-proto-name2'));
		$this->assertFalse($instance->isRegisteredName('test-proto-name3'));
	}
	
	public function testGetByName() {
		$stream=new Curly_Stream_Memory_Input('');
		
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$instance->register('test-proto-name1', 'Curly_Stream_Memory_Input');
		$instance->register('test-proto-name2', $stream);
		
		$this->assertEquals('Curly_Stream_Memory_Input', $instance->getByName('test-proto-name1'));
		$this->assertEquals($stream, $instance->getByName('test-proto-name2'));
		
		$instance->unregister('test-proto-name1');
		$instance->unregister('test-proto-name2');
	}
	
	public function testCaseInsensitivity() {
		$instance=Curly_Stream_Wrapper_Registry::getGlobalInstance();
		$instance->register('reg', 'Curly_Stream_Memory_Input');
		$this->assertTrue($instance->isRegisteredName('REG'));
		$this->assertEquals('Curly_Stream_Memory_Input', $instance->getByName('Reg'));
		$this->assertTrue($instance->unregister('reG'));
		$this->assertFalse($instance->isRegisteredName('REG'));
	}
	
}
