<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use \model\News as News;
use \InvalidArgumentException;
use \ErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * Allows creation of news and displaying of news by admins
 */
class NewsController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    protected $pc = null;

    public function __construct() {
        $this->pc = Player::find(SessionFactory::getSession()->get('player_id'));
    }

    /**
     * Check whether a player has the necessary create role
     *
     * @param Player|null $pc 
     * @return boolean
     */
    private function hasCreateRole($pc) {
        if (!($pc instanceof Player)) {
            throw new InvalidArgumentException('No account permissions');
        }

        return (bool) $pc->isAdmin();
    }

    /**
     * Display listing of posts
     * 
     * @return StreamedViewResponse
     */
    public function index() {
        $request = RequestWrapper::$request;
        $view = 'news.tpl';
        $create_successful = (bool) $request->get('create_successful');

        try {
            $create_role = $this->hasCreateRole($this->pc);
        } catch (InvalidArgumentException $e) {
            $create_role = false;
        }

        $parts = [
            'create_successful' => $create_successful,
            'all_news'          => [],
            'error'             => $request->get('error'),
            'create_role'       => $create_role,
            'search_title'      => null
        ];

        // Fetch all the posts
        $news = new News();

        try {
            if ($tag = $request->get('tag_query')) { // Search for specific tag matches
                $parts['all_news'] = $news->findByTag($tag);
                $parts['search_title'] = 'Result for #'.htmlentities($request->get('tag_query'));
            } else {
                $parts['all_news'] = $news->all();
            }
        } catch (InvalidArgumentException $e) {
            $parts['error'] = 'Unable to find any matching news.';
        }

        return new StreamedViewResponse('News Board', $view, $parts);
    }

    /**
     * Create new post
     * 
     * @return StreamedViewResponse
     */
    public function create() {
        try {
            $create_role = $this->hasCreateRole($this->pc);
        } catch (InvalidArgumentException $e) {
            $error = "Sorry, you must be logged in to create a news post.";
            return new RedirectResponse('/news/?error='.rawurlencode($error));
        }

        if (!$create_role) {
            $error = 'Sorry, you do not have permission to create a news post.';
            return new RedirectResponse('/news/?error='.rawurlencode($error));
        }

        $title = 'Make New Post';
        $error = (bool) RequestWrapper::getPostOrGet('error');

        $parts = [
            'error'         => $error,
            'heading'       => $title,
            'authenticated' => SessionFactory::getSession()->get('authenticated', false),
        ];

        return new StreamedViewResponse($title, 'news.create.tpl', $parts);
    }

    /**
     * Try to store a posted post, and redirect on successes or errors.
     *
     * @return RedirectResponse
     */
    public function store() {
        $request = RequestWrapper::$request;

        try {
            $this->hasCreateRole($this->pc);
            $account = ($this->pc ? Account::findByChar($this->pc) : null);
            $account_id = $account ? $account->id() : null;
        } catch (InvalidArgumentException $e) {
            $error = "Sorry, you must be logged in to try to save a news post.";
            return new RedirectResponse('/news/?error='.rawurlencode($error));
        } catch (ErrorException $e) {
            $error = "Sorry, you don't have permission to save a news post.";
            return new RedirectResponse('/news/?error='.rawurlencode($error));
        }

        // Handle POST
        $news_title = $request->get('news_title');
        $news_content = $request->get('news_content');
        $tag = $request->get('tag');

        // Create new post
        if (!empty($news_content)) {
            try {
                // News Model
                $news = new News();
                $news->createPost($news_title, $news_content, $account_id, $tag); // Null account_id just will throw
                return new RedirectResponse('/news/?create_successful=1');
            } catch (InvalidArgumentException $e) {
                return new RedirectResponse('/news/?error='.rawurlencode('Unable to create news post.'));
            }
        } else {
            return new RedirectResponse('/news/create/?error='.rawurlencode('A News post must have a body.'));
        }
    }
}
