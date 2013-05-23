<?php

class News_Test extends PHPUnit_Framework_TestCase {

	protected $testedAccountId = 0;
	protected $testedContent = array(
		'Some awesome news',
		'Edited news',
	);

	public function setUp()
	{
		$existsAccount = model\Base::query('Accounts')->findByAccountIdentity('Foo is Bar');
		if ( ! $existsAccount instanceof \PropelCollection || $existsAccount->count() <= 0) {
			$testedAccount = model\Base::create('Accounts');
			$testedAccount->setAccountIdentity('Foo is Bar');
			$testedAccount->setActiveEmail('foo@bar.com');
			$testedAccount->save();

			$testedAccountId = $testedAccount->getAccountId();
		} else {
			$testedAccountId = $existsAccount->getFirst()->getAccountId();
		}

		$this->testedAccountId = $testedAccountId;
	}

	public function tearDown()
	{
		if ( ! empty($this->testedAccountId)) {
			$testedAccount = model\Base::query('Accounts')->findPK($this->testedAccountId);
			$testedAccount and $testedAccount->delete();
		}

		foreach ($this->testedContent as $content) {
			$testedNews = model\Base::query('News')->findByContent($content);

			$testedNews and $testedNews->delete();
		}
	}

	public function testContructor() 
	{
		$news = new model\News();

		$this->assertInstanceOf('\model\Base', $news);

		$this->assertInstanceOf('\model\News', $news);
	}

	public function testCreate()
	{
		$news = new model\News();

		$lastNews = $news->createPost($this->testedContent[0], $this->testedAccountId);

		$author = $lastNews->getAccountss()->getFirst();

		$this->assertEquals($this->testedAccountId, $author->getAccountId());

		$this->assertEquals($this->testedContent[0], $lastNews->getContent());
	}

	public function testFetch()
	{
		$news = new model\News();

		$lastNews = $news->createPost($this->testedContent[0], $this->testedAccountId);

		$lastFetchedNews = $news->last();

		$allNews = $news->all();

		$this->assertEquals($lastNews,$lastFetchedNews);

		$this->assertTrue($news->isCollection($allNews));

		$this->assertEquals(3,strlen($news->lastPreview(3)));
	}

	public function testUpdate()
	{
		$news = new model\News();

		$lastNews = $news->createPost($this->testedContent[0], $this->testedAccountId);

		$lastNews->setContent($this->testedContent[1]);

		$lastNews->save();

		$this->assertEquals($this->testedContent[1], $lastNews->getContent());
	}
}