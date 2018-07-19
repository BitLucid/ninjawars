<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit

use \model\News as News;
use \model\Base;
use NinjaWars\core\data\Account;

class TestNews extends PHPUnit_Framework_TestCase {

    function setUp() {
    }

    function tearDown() {
        // Delete testing news.
        query('delete from news where title = \'Testing news title78\'');
    }

    public function obtainTestAccount(){
        return Account::findById(query_item('select account_id from accounts order by account_id asc'));
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
        $this->assertNotEmpty($one_news->author, 'Author was not present.');
        $this->assertNotEmpty($one_news->author_id, 'Author id was not present');
    }
}
