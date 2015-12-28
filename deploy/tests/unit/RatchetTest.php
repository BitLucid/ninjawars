<?php
/**
 * Ratchets for improving codebase simplicity/quality
**/

class RatchetTest extends PHPUnit_Framework_TestCase {

	const MAX_WWW_SCRIPTS = 46;
	const MAX_NINJAMASTER_SCRIPTS = 3;

	function before() {
	}

	public function testWwwScriptsLimit(){
		$it = new RegexIterator(new DirectoryIterator(ROOT.'www/'), "/\\.php\$/i");
		$this->assertLessThanOrEqual(static::MAX_WWW_SCRIPTS, iterator_count($it));
		$this->assertGreaterThanOrEqual(round(static::MAX_WWW_SCRIPTS*0.95), iterator_count($it)); // Change ratchet if this trips
	}

	public function testNinjamasterScriptsLimit(){
		$it = new RegexIterator(new DirectoryIterator(ROOT.'www/ninjamaster/'), "/\\.php\$/i");
		$this->assertLessThanOrEqual(static::MAX_NINJAMASTER_SCRIPTS, iterator_count($it));
		$this->assertGreaterThanOrEqual(static::MAX_NINJAMASTER_SCRIPTS, iterator_count($it)); // Change ratchet if this trips
	}
	
}