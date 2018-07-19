<?php

/**
 * News model class
 *
 * @package NinjaWars
 * @category Model
 * @author Taufan Aditya<toopay@taufanaditya.com>
 */

namespace model;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use \PDO;

class News {


	// Get the data for the first author of a news post
	public function firstAuthor(){
		$query = 'select uname, player_id from players 
			left join account_players on _player_id = player_id 
			left join account_news on account_news._account_id = account_players._account_id 
			where _news_id = :id';
		return !$this->id ? null : query_row($query, [':id'=>[$this->id, \PDO::PARAM_INT]]);
	}

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
	public function createPost($title = '', $content = '', $authorId = 0, $tags = ''){
		// Validate the account
		$author = Account::findById($authorId);

		if ( ! ($author instanceof Account)) {
			throw new \InvalidArgumentException('Account not found');
		}

		// Create and save new post
		$news = new News();
		$news->title = $title;
		$news->content = $content;
		$news->tags = $tags;
		$news->authorFull = $author;
		$news->save();

		return $news;
	}

	/**
	 * Create a news post and save it's author
	 * @return int news_id
	 */
	public function save(){
		if(!isset($this->id)){
			// Return id during insert
			$stmt = insert_query(
				'insert into news (title, content, tags) values (:title, :content, :tags) returning news_id',
				[':title'=>$this->title, ':content'=>$this->content, ':tags'=>$this->tags]);
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$this->id = reset($data)['news_id'] ?? null;
			insert_query('insert into account_news (_account_id, _news_id) values (:aid, :nid)',
				[':aid'=>$this->authorFull->id(), ':nid'=>$this->id]);
			return $this->id;
		} else {
			throw new \InvalidArgumentException('Updating of existing news not yet implemented!');
		}
	}

	// Static functions start here


	/**
	 * Get all the existant tags by pulling tag data and creating individual tags from db
	 */
	public static function availableTags(){
		$normalized_tags = [];
		$tags = query('select tags from news group by tags');
		foreach($tags as $tag_line){
			$individual_tags = explode(",", $tag_line['tags']);
			foreach($individual_tags as $tag){
				$normalized_tags[$tag] = true;
			}
		}
		return array_keys($normalized_tags);
	}

	/**
	 * The standard fields to return
	 * 
	 * @return String of standard fields
	 */
	public static function fields(){
		return implode(', ', ['news_id', 'news_id as id', 'title', 'content', 'created', 'updated', 'tags', 'uname as author', 'player_id as author_id']);
	}

	public static function authorJoined(){
		return ' left join account_news on account_news._news_id = news.news_id left join account_players on account_players._account_id = account_news._account_id left join players on players.player_id = account_players._player_id ';
	}

	/**
	 * Find based tag
	 *
	 * @param string some tag
	 * @return Collection
	 */
	public static function findByTag($tag = ''){
		$news = query_array(
			'select '.static::fields().' from news '.static::authorJoined().' 
				where tags like \'%\' || :tag || \'%\' 
				order by news_id desc',
			[':tag'=>$tag]
		);

		if ( empty($news)) {
			throw new \InvalidArgumentException('Tagged #'.$tag.' news not found');
		}

		return array_map(function($n) { return (object) $n;}, $news);
	}

	/**
	 * All news
	 *
	 * @return Collection
	 */
	public static function all(){
		$news = query_array('select '.static::fields().' from news '.static::authorJoined().' 
			order by news_id desc');

		if (empty($news)) {
			throw new \InvalidArgumentException('News is empty');
		}

		return array_map(function($n) { return (object) $n;}, $news);
	}

	/**
	 * Get last news
	 *
	 * @throws InvalidArgumentException
	 * @return orm\News
	 */
	public static function last(){
		$news = query_row('select '.static::fields().' from news '.static::authorJoined().' order by news_id desc limit 1');

		if ( empty($news)) {
			throw new \InvalidArgumentException('News not found');
		}

		return (object) $news;
	}
}