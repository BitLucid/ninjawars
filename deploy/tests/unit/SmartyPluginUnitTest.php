<?php
require_once(CORE.'/plugins/function.cachebust.php');
require_once(CORE.'/plugins/modifier.replace_urls.php');
require_once(CORE.'/plugins/modifier.markdown.php');

class SmartyPluginUnitTest extends PHPUnit_Framework_TestCase {
    const EXISTING_FILE = '/js/nw.js';
    const MISSING_FILE = '/js/nw2.js';
    const MD_TEXT = 'This is [href:http://localhost/|localhost]';
    const PLAIN_TEXT = 'This is plain';
    const URL = 'http://localhost.com/go/?query=string';

    public function testCachebustPositive() {
        $result = smarty_function_cachebust(['file'=>self::EXISTING_FILE]);
        $this->assertNotEquals(self::EXISTING_FILE, $result);
        $this->assertGreaterThan(strlen(self::EXISTING_FILE), strlen($result));
    }

    public function testCachebustNegative() {
        $this->assertEquals(self::MISSING_FILE, smarty_function_cachebust(['file'=>self::MISSING_FILE]));
    }

    public function testMarkdownPositive() {
        $result = smarty_modifier_markdown(self::MD_TEXT);
        $this->assertNotEquals(self::MD_TEXT, $result);
        $this->assertGreaterThan(strlen(self::MD_TEXT), strlen($result));
    }

    public function testMarkdownNegative() {
        $this->assertEquals(self::PLAIN_TEXT, smarty_modifier_markdown(self::PLAIN_TEXT));
    }

    public function testReplaceUrlsPositive() {
        $testText = 'The main thing '.self::URL.' is this.';
        $result = smarty_modifier_replace_urls($testText);
        $this->assertNotEquals($testText, $result);
        $this->assertGreaterThan(strlen($testText), strlen($result));
        $this->assertGreaterThan(strlen(self::URL)*2, strlen($result));
        $this->assertContains(self::URL, $result);
    }

    public function testReplaceUrlsNegative() {
        $this->assertEquals(self::PLAIN_TEXT, smarty_modifier_replace_urls(self::PLAIN_TEXT));
    }
}
