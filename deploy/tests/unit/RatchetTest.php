<?php
/**
 * Ratchets for improving codebase simplicity/quality
**/

class RatchetTest extends PHPUnit_Framework_TestCase {

	const MAX_WWW_SCRIPTS = 51;
	const MAX_NINJAMASTER_SCRIPTS = 2;

	function before() {
	}

	public function testWwwScriptsLimit(){
		$it = new RegexIterator(new DirectoryIterator(ROOT.'www/'), "/\\.php\$/i");
		$this->assertLessThanOrEqual(static::MAX_WWW_SCRIPTS, iterator_count($it));
	}

	public function testNinjamasterScriptsLimit(){
		$it = new RegexIterator(new DirectoryIterator(ROOT.'www/ninjamaster/'), "/\\.php\$/i");
		$this->assertLessThanOrEqual(static::MAX_NINJAMASTER_SCRIPTS, iterator_count($it));
	}
	
}