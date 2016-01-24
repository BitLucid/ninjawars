<?php
/**
 * Ratchets for improving codebase simplicity/quality
 */
class RatchetTest extends PHPUnit_Framework_TestCase {
	const MAX_WWW_SCRIPTS = 20;
    const MAX_WWW_SCRIPTS_RATCHET = .8;
	const MAX_NINJAMASTER_SCRIPTS = 3;

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

	public function testNinjamasterScriptsLimit(){
		$scriptList = new RegexIterator(new DirectoryIterator(ROOT.'www/ninjamaster/'), "/\\.php\$/i");
        $scriptCount = iterator_count($scriptList);

		$this->assertLessThanOrEqual(static::MAX_NINJAMASTER_SCRIPTS, $scriptCount);

        // RATCHET
        $this->assertGreaterThanOrEqual(
            static::MAX_NINJAMASTER_SCRIPTS, $scriptCount,
            "Reduce MAX_NINJAMASTER_SCRIPTS to $scriptCount to tighten the ratchet"
        );
	}
}
