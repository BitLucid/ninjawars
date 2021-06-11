<?php
require_once(CORE.'/plugins/function.cachebust.php');
require_once(CORE.'/plugins/modifier.replace_urls.php');
require_once(CORE.'/plugins/modifier.markdown.php');
require_once(CORE.'/plugins/function.getTurnCost.php');
require_once(CORE.'/plugins/function.health_percent.php');
require_once(CORE.'/plugins/modifier.css_classify.php');
require_once(CORE.'/plugins/modifier.level_label.php');
require_once(CORE.'/plugins/modifier.to_tags.php');

class SmartyPluginUnitTest extends \PHPUnit\Framework\TestCase {
    const EXISTING_FILE = '/js/nw.js';
    const MISSING_FILE = '/js/nw2.js';
    const MD_TEXT = 'This is [href:https://localhost/|localhost]';
    const PLAIN_TEXT = 'This is plain';
    const URL = 'https://localhost.com/go/?query=string';

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
        $this->assertStringContainsString(self::URL, $result);
    }

    public function testReplaceUrlsNegative() {
        $this->assertEquals(self::PLAIN_TEXT, smarty_modifier_replace_urls(self::PLAIN_TEXT));
    }
    public function testTurnCostIsInt() {
        $this->assertIsInt(smarty_function_getTurnCost(['skillName'=>'kampo']));
    }

    public function testCSSClassifyIsString() {
        $this->assertIsString(smarty_modifier_css_classify('skillName'));
    }

    public function testLevelLabeIsString() {
        $this->assertIsString(smarty_modifier_level_label(4));
    }

    public function testHealthPercentIsFloat() {
        $this->assertIsFloat(smarty_function_health_percent(['level'=>10, 'health'=>10]));
    }

}
