<?php

require_once 'src/Curly/Exception.php';
require_once 'src/Curly/Stream/Exception.php';
require_once 'src/Curly/Stream/Input.php';
require_once 'src/Curly/Stream/Output.php';
require_once 'src/Curly/Stream/Seekable.php';
require_once 'src/Curly/Stream.php';
require_once 'src/Curly/Stream/Capsule/Input.php';
require_once 'src/Curly/Stream/Capsule/Output.php';
require_once 'src/Curly/Stream/Memory/Seekable.php';
require_once 'src/Curly/Stream/Memory/Input.php';
require_once 'src/Curly/Stream/Memory/Output.php';
require_once 'src/Curly/Stream/Memory.php';
require_once 'src/Curly/Stream/Buffered/Input.php';
require_once 'src/Curly/Stream/Buffered/Output.php';
require_once 'src/Curly/Stream/File/Seekable.php';
require_once 'src/Curly/Stream/File.php';
require_once 'src/Curly/Stream/File/Input.php';
require_once 'src/Curly/Stream/File/Output.php';
require_once 'src/Curly/Stream/Append/Input.php';
require_once 'src/Curly/Stream/Binary/Input.php';
require_once 'src/Curly/Stream/Binary/Output.php';
require_once 'src/Curly/Stream/Xml/Output.php';
require_once 'src/Curly/Stream/Wrapper.php';
require_once 'src/Curly/Stream/Wrapper/Registry.php';
require_once 'src/Curly/Stream/Wrapper/Exception.php';
require_once 'src/Curly/Stream/Factory/Allocator.php';
require_once 'src/Curly/Stream/Factory.php';
require_once 'src/Curly/Stream/Infinite/Input.php';
require_once 'src/Curly/Stream/Empty/Input.php';
require_once 'src/Curly/Stream/Base64/Encode/Input.php';

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
require_once 'tests/Stream/Binary/OutputTest.php';
require_once 'tests/Stream/Xml/OutputTest.php';
require_once 'tests/Stream/Wrapper/RegistryTest.php';
require_once 'tests/Stream/WrapperTest.php';
require_once 'tests/Stream/FactoryTest.php';
require_once 'tests/Stream/Infinite/InputTest.php';
require_once 'tests/Stream/Empty/InputTest.php';
require_once 'tests/Stream/Base64/InputTest.php';

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
		$this->addTestSuite('Curly_Stream_Binary_OutputTest');
		$this->addTestSuite('Curly_Stream_Xml_OutputTest');
		$this->addTestSuite('Curly_Stream_Wrapper_RegistryTest');
		$this->addTestSuite('Curly_Stream_WrapperTest');
		$this->addTestSuite('Curly_Stream_FactoryTest');
		$this->addTestSuite('Curly_Stream_Infinite_InputTest');
		$this->addTestSuite('Curly_Stream_Empty_InputTest');
		//$this->addTestSuite('Curly_Stream_Base64_InputTest');
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

