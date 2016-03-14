<?php
namespace NinjaWars\core\control;
use \model\News as News;
use \model\Base;

/**
 * Allows creation of news and displaying of news by admins
 */
class NewsController {
    const ALIVE = false;
    const PRIV  = false;

    public function __construct(){
    }

    public function index(){
        $view = 'news.tpl';
        $parts = array(
            'target' => 'news.php',
            'field_size' => '40',
        );

        // Route the request
        if (in('new') && self_char_id()) {
            // Display submit form
            $view = 'news-create.tpl';
        } elseif (in('news_submit')) {
            // Handle POST
            $news_title = in('news_title');
            $news_content = in('news_content');
            $tag = in('tag');

            // Create new post
            if ( ! empty($news_content)) {
                try {
                    // News Model
                    $news = new News();
                    $me = Base::query('Players')->findPK(self_char_id());
                    $news->createPost($news_title, $news_content, $me->getAccountss()->getFirst()->getAccountId(), $tag);
                    $parts['new_successful_submit'] = true;
                } catch (InvalidArgumentException $e) {
                    $parts['new_successful_submit'] = false;
                }
            }
        }

        // Fetch the news
        try {
            $news = new News();

            if (in('tag_query')) {
                // Search for specific tag
                $all_news = $news->findByTag(in('tag_query'));
                $parts['search_title'] = 'Result for #'.htmlentities(in('tag_query'));
            } else {
                $all_news = $news->all();
            }
        } catch (InvalidArgumentException $e) {
            $all_news = array();
        }
        $parts['all_news'] = $all_news;
        return [
            'title'=>'News Board',
            'template'=>$view,
            'parts'=>$parts,
            'options'=>[],
            ];
    }

    public function create(){

    }

    public function store(){
    }
}