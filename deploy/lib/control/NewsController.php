<?php
namespace NinjaWars\core\control;
use \model\News as News;
use \model\Base;
use \InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use \NinjaWars\core\data\AccountFactory;

/**
 * Allows creation of news and displaying of news by admins
 */
class NewsController {
    const ALIVE = false;
    const PRIV  = false;

    public function __construct(){
    }

    public function index(){
        $pc = Player::find(self_char_id());
        $account = $pc? AccountFactory::findByChar($pc) : null;
        $view = 'news.tpl';
        $parts = array(
            'target' => '/news',
            'field_size' => '40',
        );
        $create_successful = (bool) in('create_successful');
        $parts['create_successful'] = $create_successful;

        // Route the request
        if (in('new') && $account) {
            // Display submit form
            $view = 'news-create.tpl';
        } elseif (in('news_submit') && $account) {
            // Handle POST
            $news_title = in('news_title');
            $news_content = in('news_content');
            $tag = in('tag');

            // Create new post
            if ( ! empty($news_content)) {
                try {
                    // News Model
                    $news = new News();
                    $news->createPost($news_title, $news_content, $account->id(), $tag);
                    return new RedirectResponse('/news/?create_successful=1');
                } catch (InvalidArgumentException $e) {
                    return new RedirectResponse('/news/');
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