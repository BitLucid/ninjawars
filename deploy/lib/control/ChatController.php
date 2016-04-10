<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\extensions\SessionFactory;

/**
 * The controller for effects of a village request and the default index display of the page
 */
class ChatController extends AbstractController {
    const ALIVE         = false;
    const PRIV          = false;
    const DEFAULT_LIMIT = 200;
    const FIELD_SIZE    = 40;
    const MAX_CHATS     = 3000;
    const MIN_CHATS     = 30;

    /**
     * Take in a chat and record it to the database.
     */
    public function receive() {
        $char_id = SessionFactory::getSession()->get('player_id');
        $message = in('message', null, 'no filter'); // Essentially no filtering.
        $error   = null;

        if (!empty($message)) {
            if ($char_id) {
                Message:sendChat($char_id, $message);
            } else {
                $error = 'You must be logged in to chat.';
            }
        }

		return new RedirectResponse('/village/'.($error? '?error='.rawurlencode($error) : ''));
    }

    /**
     * Pull & display the chats and a chat send if logged in
     */
    public function index() {
        $view_all   = in('view_all');
        $chatlength = in('chatlength', self::DEFAULT_LIMIT, 'toInt');
        $chatlength = min(self::MAX_CHATS, max(self::MIN_CHATS, $chatlength));
        $chats      = $this->getChats($view_all ? null : $chatlength);

        $parts = [
            'field_size'        => self::FIELD_SIZE,
            'target'            => $_SERVER['PHP_SELF'],
            'chats'             => $chats,
            'error'             => in('error'),
            'more_chats_to_see' => (!$view_all && $chatlength < $this->getChatCount()),
            'authenticated'     => SessionFactory::getSession()->get('authenticated', false),
        ];

        return $this->render($parts);
    }

    private function render($parts) {
        return [
            'template' => 'village.tpl',
            'title'    => 'Chat Board',
            'parts'    => $parts,
            'options'  => [
                'quickstat' => false
            ],
        ];
    }

    /**
     * Get all the chat messages info.
     */
    private function getChats($chatlength=null) {
        $chatlength = positive_int($chatlength); // Prevent negatives.
        $limit = ($chatlength ? 'LIMIT :limit' : '');

        $bindings = [];

        if ($limit) {
            $bindings[':limit'] = $chatlength;
        }

        $chats = query("SELECT sender_id, uname, message, date, age(now(), date) AS ago FROM chat
            JOIN players ON chat.sender_id = player_id ORDER BY chat_id DESC ".$limit, $bindings);

        return $chats;
    }

    /**
     * Total number of chats available.
     *
     * @return int
     */
    private function getChatCount() {
        return query_item("SELECT count(*) FROM chat");
    }
}
