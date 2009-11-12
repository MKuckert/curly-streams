<?php

require_once 'src/Exception.php';
require_once 'src/Stream/Exception.php';
require_once 'src/Stream/Input.php';
require_once 'src/Stream/Output.php';
require_once 'src/Stream/Seekable.php';
require_once 'src/Stream.php';
require_once 'src/Stream/Memory/Seekable.php';
require_once 'src/Stream/Memory/Input.php';
require_once 'src/Stream/Memory/Output.php';
require_once 'src/Stream/Memory.php';
require_once 'src/Stream/Buffered/Input.php';
require_once 'src/Stream/Buffered/Output.php';
require_once 'src/Stream/File/Seekable.php';
require_once 'src/Stream/File.php';
require_once 'src/Stream/File/Input.php';
require_once 'src/Stream/File/Output.php';
require_once 'src/Stream/Append/Input.php';
require_once 'src/Stream/Capsule/Input.php';
require_once 'src/Stream/Binary/Input.php';
require_once 'src/Stream/Xml/Output.php';
require_once 'src/Stream/Wrapper.php';
require_once 'src/Stream/Wrapper/Registry.php';
require_once 'src/Stream/Wrapper/Exception.php';
require_once 'src/Stream/Factory/Allocator.php';
require_once 'src/Stream/Factory.php';

require_once 'tests/Stream/Buffered/InputWithMemoryTest.php';
require_once 'tests/Stream/Buffered/OutputWithFileTest.php';
require_once 'tests/Stream/Memory/InputTest.php';
require_once 'tests/Stream/Memory/OutputTest.php';
require_once 'tests/Stream/Memory/SeekableTest.php';
require_once 'tests/Stream/MemoryTest.php';
require_once 'tests/Stream/File/InputTest.php';
require_once 'tests/Stream/File/OutputTest.php';
require_once 'tests/Stream/FileTest.php';
require_once 'tests/Stream/Append/InputTest.php';
require_once 'tests/Stream/Capsule/InputTest.php';
require_once 'tests/Stream/Binary/InputTest.php';
require_once 'tests/Stream/Xml/OutputTest.php';
require_once 'tests/Stream/Wrapper/RegistryTest.php';
require_once 'tests/Stream/WrapperTest.php';
require_once 'tests/Stream/FactoryTest.php';

require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * Static test suite.
 */
class TestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName( 'Curly Streams' );
		
		$this->addTestSuite('Curly_Stream_Memory_SeekableTest');
		$this->addTestSuite('Curly_Stream_Memory_InputTest');
		$this->addTestSuite('Curly_Stream_Memory_OutputTest');
		$this->addTestSuite('Curly_Stream_MemoryTest');
		$this->addTestSuite('Curly_Stream_File_InputTest');
		$this->addTestSuite('Curly_Stream_File_OutputTest');
		$this->addTestSuite('Curly_Stream_FileTest');
		$this->addTestSuite('Curly_Stream_Buffered_InputWithMemoryTest');
		$this->addTestSuite('Curly_Stream_Buffered_OutputWithFileTest');
		$this->addTestSuite('Curly_Stream_Append_InputTest');
		$this->addTestSuite('Curly_Stream_Capsule_InputTest');
		$this->addTestSuite('Curly_Stream_Binary_InputTest');
		$this->addTestSuite('Curly_Stream_Xml_OutputTest');
		$this->addTestSuite('Curly_Stream_Wrapper_RegistryTest');
		$this->addTestSuite('Curly_Stream_WrapperTest');
		$this->addTestSuite('Curly_Stream_FactoryTest');
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

