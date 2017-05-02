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
	 * @param string Title
	 * @param string Content
	 * @param int Account ID
	 * @param string Tags
	 * @throws InvalidArgumentException
	 * @return orm\News
	 */
	public function createPost($title = '', $content = '', $authorId = 0, $tags = '')
	{
		// Validate the account
		$creator = self::query('Accounts')->findPK($authorId);

		if ( ! $this->isObject($creator)) {
			throw new \InvalidArgumentException('Account not found');
		}

		// Create and save new post
		$news = self::create('News');
		$news->setTitle($title);
		$news->setContent($content);
		$news->setTags($tags);
		$news->setAccountss($this->collection($creator));

		$news->save();

		return $news;
	}

	/**
	 * Find based tag
	 *
	 * @param string some tag
	 * @return Collection
	 */
	public function findByTag($tag = '')
	{
		$news = self::query('News')->orderBy('news_id', 'desc')->filterByTags('%'.$tag.'%')->find();

		if ( ! $this->isCollection($news)) {
			throw new \InvalidArgumentException('Tagged #'.$tag.' news not found');
		}

		return $news;
	}

	/**
	 * All news
	 *
	 * @return Collection
	 */
	public function all()
	{
		$all_news = self::query('News')->orderBy('news_id', 'desc')->find();

		if ( ! $this->isCollection($all_news)) {
			throw new \InvalidArgumentException('News is empty');
		}

		return $all_news;
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

		if ( ! $this->isCollection($news)) {
			throw new \InvalidArgumentException('News not found');
		}

		return $news->getFirst();
	}

	/**
	 * View helper, to display the last news preview content
	 *
	 * @param int Max length to display
	 * @param string Suffix
	 * @return string
	 */
	public function lastPreview($max = 150, $suffix = '')
	{
		try {
			$last_news = $this->last();
		} catch (\InvalidArgumentException $e) {
			return false;
		}

		if ( ! $last_news instanceof orm\News) {
			return '';
		}

		$preview = $last_news->getContent();

		return strlen($preview) > $max ? substr($preview, 0, $max).$suffix : $preview;
	}

}