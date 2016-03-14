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
        $view = 'news.tpl';
        $parts = [];
        $create_successful = (bool) in('create_successful');
        $parts['create_successful'] = $create_successful;
        $parts['all_news'] = [];
        $parts['error'] = in('error');

        // Fetch the news
        try {
            $news = new News();

            if ($tag = in('tag_query')) {
                // Search for specific tag
                $parts['all_news'] = $news->findByTag($tag);
                $parts['search_title'] = 'Result for #'.htmlentities(in('tag_query'));
            } else {
                $parts['all_news'] = $news->all();
            }
        } catch (InvalidArgumentException $e) {
            $error = 'Unable to find any news like that';
        }

        return [
            'title'=>'News Board',
            'template'=>$view,
            'parts'=>$parts,
            'options'=>[],
            ];
    }

    /**
     * post creation
     */
    public function create(){
        $title = 'Make New Post';
        $error = (bool) in('error');
        $parts = array(
            'error'=>$error,
            'heading'=>$title,
        );

        return [
            'title'=>$title,
            'template'=>'news-create.tpl',
            'parts'=>$parts,
            'options'=>[],
            ];
    }

    /**
     * Try to store a posted post, and redirect on successes or errors.
     */
    public function store(){
            $pc = Player::find(self_char_id());
            $account = $pc? AccountFactory::findByChar($pc) : null;
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
            } else {
                return new RedirectResponse('/news/create/?error=1');
            }
    }
}