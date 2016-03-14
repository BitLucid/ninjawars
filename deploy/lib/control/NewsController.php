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

    protected $pc = null;

    public function __construct(){
        $this->pc = Player::find(self_char_id());
    }

    private function hasCreateRole(Player $pc){
        if(!($pc instanceof Player)){
            throw new InvalidArgumentException("Can't check the role of a nonexistent player.");
        }
        return $pc->isAdmin();
    }

    /**
     * Display listing of posts
     */
    public function index(){
        $view = 'news.tpl';
        $create_successful = (bool) in('create_successful');
        $parts = [
            'create_successful'=>$create_successful,
            'all_news'=>[],
            'error'=>in('error'),
            'create_role'=>$this->pc? $this->hasCreateRole($this->pc) : null,
        ];

        // Fetch all the posts
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
            $parts['error'] = 'Unable to find any news like that';
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
        if($this->pc === null || !$this->hasCreateRole($this->pc)){
            return new RedirectResponse('/news/?error='.url("Sorry, you can't create a news post"));
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
        if($this->pc === null || !$this->hasCreateRole($this->pc)){
            return new RedirectResponse('/news/?error='.url("Sorry, you don't have permission to create a news post."));
        }
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
            return new RedirectResponse('/news/create/?error='.url('Unable to create news post'));
        }
    }
}