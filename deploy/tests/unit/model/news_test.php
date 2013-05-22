<?php

class News_Test extends PHPUnit_Framework_TestCase {

	protected $testedContent = array(
		'Some awesome news',
		'Edited news',
	);

	public function tearDown()
	{
		foreach ($this->testedContent as $content) {
			$testedNews = model\Base::query('News')->findByContent($content);

			if ($testedNews) {
				$testedNews->delete();
			}
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

		$lastNews = $news->createPost($this->testedContent[0], 1);

		$author = $lastNews->getAccountss()->getFirst();

		$this->assertEquals(1, $author->getAccountId());

		$this->assertEquals($this->testedContent[0], $lastNews->getContent());
	}

	public function testUpdate()
	{
		$news = new model\News();

		$lastNews = $news->createPost($this->testedContent[0], 1);

		$lastNews->setContent($this->testedContent[1]);

		$lastNews->save();

		$this->assertEquals($this->testedContent[1], $lastNews->getContent());
	}
}