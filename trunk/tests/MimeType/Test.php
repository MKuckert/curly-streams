<?php

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Curly_MimeType_Test test case.
 */
class Curly_MimeType_Test extends PHPUnit_Framework_TestCase {
	
	public function testCreate() {
		$type=new Curly_MimeType('text', 'xml');
	}
	
}
