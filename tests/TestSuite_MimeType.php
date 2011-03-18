<?php

require_once 'src/Exception.php';
require_once 'src/MimeType/Exception.php';
require_once 'src/MimeType.php';

require_once 'tests/MimeType/Test.php';

require_once 'PHPUnit/Framework/TestSuite_MimeType.php';

/**
 * Static test suite.
 */
class TestSuite_MimeType extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName( 'Curly MimeType' );
		
		$this->addTestSuite('Curly_MimeType_Test');
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

