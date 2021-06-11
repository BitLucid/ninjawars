<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use \model\News as News;
use \model\Base;
use NinjaWars\core\data\Account;

class TestNews extends NWTest {

    function setUp():void {
        parent::setUp();
        $test_account = $this->obtainTestAccount();
        $news = new News();
        assert($test_account->id() > 0);
        $news->createPost('Testing news title78', 'phpunit testing content', $test_account->id(), 'need,some, fake, tags');
    }

    function tearDown():void {
        // Delete testing news.
        query('delete from news where title = \'Testing news title78\'');
        TestAccountCreateAndDestroy::destroy();
        parent::tearDown();
    }

    public function obtainTestAccount(){
        return Account::findById(TestAccountCreateAndDestroy::account_id());
    }

    public function testNewsCanInstantiate(){
        $news = new News();
        $this->assertTrue($news instanceof News);
    }

    public function testNewsClassHasACreateMethod(){
        $this->assertTrue(is_callable('News', 'createPost'), 'No create method found on news object!');
    }

    public function testNewsPostCanBeCreated(){
        $first_account = $this->obtainTestAccount();
        $news = new News();
        $news->title = 'Testing news title78';
        $news->content = 'Some news post content';
        $news->tags = null;
        $news->authorFull = $first_account;
        $news->save();
        $this->assertGreaterThan(0, $news->id, 'News post created did not have a valid id');
        $this->assertEquals('Testing news title78', $news->title);
    }

    public function testNewsPostCreatedViaPostMethodCreatesFullAuthor(){
        $news = new News();
        $updated = $news->createPost('Testing news title78', 'phpunit testing content', $this->obtainTestAccount()->id(), 'need,some, fake, tags');
        $found = News::findById($updated->id);
        $this->assertGreaterThan(0, $found->id);
        $this->assertNotEmpty($found->title);
        $this->assertEquals('phpunit testing content', $found->content);
        $this->assertNotEmpty($found->author_id, 'Author_id not found in news returned.');
        $this->assertNotEmpty($found->author, 'Author not found in news returned');
    }

    public function testNewsHasAvailableTags(){
        $tags = News::availableTags();
        $this->assertGreaterThan(0, count($tags));
    }

    public function testNewsGetByTag(){
        $tags = News::availableTags();
        $this->assertGreaterThan(0, count($tags));
        $this->assertNotEmpty(reset($tags), 'No tags available from database');
        $news = News::findByTag(reset($tags));
        $this->assertNotEmpty($news, 'News returned by tags was empty');
        $first_news = reset($news);
        $this->assertGreaterThan(0, $first_news->id);
        $this->assertNotEmpty($first_news->title);
    }

    public function testGetLatestNews(){
        $last_news = News::last();
        $this->assertGreaterThan(0, $last_news->id);
        $this->assertNotEmpty($last_news->title);
        $this->assertNotEmpty($last_news->author, 'News Author was not present.');
    }

    public function testGetAllNewsReturnsANews(){
        $all_news = News::all();
        $one_news = reset($all_news);
        $this->assertGreaterThan(0, $one_news->id);
        $this->assertNotEmpty($one_news->title);
    }

    public function testGetAllNewsReturnsAuthorAndAuthorId(){
        $all_news = News::all();
        $one_news = reset($all_news);
        $this->assertGreaterThan(0, $one_news->id);
        $this->assertNotEmpty($one_news->title, 'Title was not present.');
        $this->assertNotEmpty($one_news->author, 'Author was not present.');
        $this->assertNotEmpty($one_news->author_id, 'Author id was not present');
    }
}
