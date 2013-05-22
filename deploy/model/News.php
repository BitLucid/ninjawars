<?php 

/**
 * News model class
 *
 * @package NinjaWars
 * @category Model
 * @author Taufan Aditya<toopay@taufanaditya.com>
 */

namespace model;

class News extends Base {

	/**
	 * Create news
	 *
	 * @param string Content
	 * @param int Account ID
	 * @throws InvalidArgumentException
	 * @return orm\News
	 */
	public function createPost($content = '', $authorId = 0)
	{
		// Validate the account
		$creator = self::query('Accounts')->findPK($authorId);

		if ( ! $this->isObject($creator)) {
			throw new \InvalidArgumentException('Account not found');
		}

		// Create and save new post
		$news = self::create('News');
		$news->setContent($content);
		$news->setAccountss($this->collection($creator));

		$news->save();

		return $news;
	}

	/**
	 * Get last news
	 *
	 * @throws InvalidArgumentException
	 * @return orm\News
	 */
	public function last()
	{
		$news = self::query('News')->orderBy('news_id', 'desc')
			->limit(1)
			->find();

		if ($this->isCollection($news)) {
			throw new \InvalidArgumentException('News not found');
		}

		return $news->getFirst();
	}

}