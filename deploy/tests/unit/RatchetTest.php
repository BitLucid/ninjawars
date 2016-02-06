<?php
/**
 * Ratchets for improving codebase simplicity/quality
 */
class RatchetTest extends PHPUnit_Framework_TestCase {
	const MAX_WWW_SCRIPTS = 23;
    const MAX_WWW_SCRIPTS_RATCHET = .6;

	function before() {
	}

	public function testWwwScriptsLimit(){
		$scriptList = new RegexIterator(new DirectoryIterator(ROOT.'www/'), "/\\.php\$/i");
        $scriptCount = iterator_count($scriptList);

		$this->assertLessThanOrEqual(static::MAX_WWW_SCRIPTS, $scriptCount);

        // RATCHET
        $this->assertGreaterThanOrEqual(
            round(static::MAX_WWW_SCRIPTS*static::MAX_WWW_SCRIPTS_RATCHET), $scriptCount,
            "Reduce MAX_WWW_SCRIPTS to $scriptCount to tighten the ratchet"
        );
	}
}
