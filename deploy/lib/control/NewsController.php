<?php
namespace NinjaWars\core\control;

use \model\News as News;
use \model\Base;
use \InvalidArgumentException;
use \ErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\AccountFactory;

/**
 * Allows creation of news and displaying of news by admins
 */
class NewsController {
    const ALIVE = false;
    const PRIV  = false;

    protected $pc = null;

    public function __construct(){
        $this->pc = Player::find(self_char_id());
    }

    /**
     * Check whether a player has the necessary create role
     * @return boolean
     */
    private function hasCreateRole($pc){
        if(!($pc instanceof Player)){
            throw new InvalidArgumentException('No account permissions');
        }
        return (bool) $pc->isAdmin();
    }

    /**
     * Display listing of posts
     */
    public function index(){
        $view = 'news.tpl';
        $create_successful = (bool) in('create_successful');
        try{
            $create_role = $this->hasCreateRole($this->pc);
        } catch(InvalidArgumentException $e){
            $create_role = false;
        }
        $parts = [
            'create_successful'=>$create_successful,
            'all_news'=>[],
            'error'=>in('error'),
            'create_role'=>$create_role,
            'search_title'=>null
        ];

        // Fetch all the posts
        $news = new News();
        try {
            if ($tag = in('tag_query')) { // Search for specific tag matches
                $parts['all_news'] = $news->findByTag($tag);
                $parts['search_title'] = 'Result for #'.htmlentities(in('tag_query'));
            } else {
                $parts['all_news'] = $news->all();
            }
        } catch (InvalidArgumentException $e) {
            $parts['error'] = 'Unable to find any matching news.';
        }

        return [
            'title'=>'News Board',
            'template'=>$view,
            'parts'=>$parts,
            'options'=>[],
            ];
    }

    /**
     * Create new post
     */
    public function create(){
        try{
            $create_role = $this->hasCreateRole($this->pc);
        } catch(InvalidArgumentException $e){
            $error = "Sorry, you must be logged in to create a news post.";
            return new RedirectResponse('/news/?error='.url($error));
        }
        if(!$create_role){
            $error = 'Sorry, you do not have permission to create a news post.';
            return new RedirectResponse('/news/?error='.url($error));
        }
        $title = 'Make New Post';
        $error = (bool) in('error');
        $parts = array(
            'error'=>$error,
            'heading'=>$title,
        );

        return [
            'title'=>$title,
            'template'=>'news.create.tpl',
            'parts'=>$parts,
            'options'=>[],
            ];
    }

    /**
     * Try to store a posted post, and redirect on successes or errors.
     * @return RedirectResponse
     */
    public function store(){
        try{
            $this->hasCreateRole($this->pc);
            $account = $this->pc? AccountFactory::findByChar($this->pc) : null;
            $account_id = $account->id();
        } catch(InvalidArgumentException $e){
            $error = "Sorry, you must be logged in to try to save a news post.";
            return new RedirectResponse('/news/?error='.url($error));            
        } catch(ErrorException $e){
            $error = "Sorry, you don't have permission to save a news post.";
            return new RedirectResponse('/news/?error='.url($error));
        }
        // Handle POST
        $news_title = in('news_title');
        $news_content = in('news_content');
        $tag = in('tag');

        // Create new post
        if ( ! empty($news_content)) {
            try {
                // News Model
                $news = new News();
                $news->createPost($news_title, $news_content, $account_id, $tag);
                return new RedirectResponse('/news/?create_successful=1');
            } catch (InvalidArgumentException $e) {
                return new RedirectResponse('/news/?error='.url('Unable to create news post.'));
            }
        } else {
            return new RedirectResponse('/news/create/?error='.url('A News post must have a body.'));
        }
    }
}
