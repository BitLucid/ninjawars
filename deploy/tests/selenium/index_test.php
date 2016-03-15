<?php
/**
 * @todo read hostname from config
 */
class PageTest extends PHPUnit_Extensions_Selenium2TestCase {
    public function setUp() {
        $this->setBrowser('firefox');

        $this->setBrowserUrl('http://nw.local/');
    }

    public function testTitlePresent() {
        $title = $this->title();
        $in_string = (false !== strpos($title, 'Live by the Shuriken'));
        $this->assertNotEmpty($title, 'Title should not be empty');
        $this->assertTrue($in_string, 'Title should contain live by the shuriken');
    }

    public function testLoginPageLoadsAndHasTitle() {
        $this->url('http://nw.local/login.php');
        $title = $this->title();
        $this->assertNotEmpty($title);
        $this->assertTrue((false !== strpos($title, 'Login')));
    }

    public function testSignupPageLoadsAndHasTitle() {
        $this->url('http://nw.local/signup.php');
        $title = $this->title();
        $pageH1 = $this->byCssSelector('h1');
        $this->assertNotEmpty($title);
        $this->assertTrue((false !== strpos($title, 'Become a Ninja')));
        $this->assertTrue((false !== strpos($pageH1, 'Become a Ninja')));
    }

    public function testChatPageLoads() {
        $this->url('http://nw.local/village.php');
        $title = $this->title();
        $pageH1 = $this->byCssSelector('h1');
        $this->assertNotEmpty($title);
        $this->assertTrue((false !== strpos($title, 'Chat')));
        $this->assertTrue((false !== strpos($pageH1, 'Chat')));
    }
}
