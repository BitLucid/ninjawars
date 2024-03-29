<?php

namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Communication;
use NinjaWars\core\Filter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

/**
 * The controller for effects of a village request and the default index display of the page
 */
class ChatController extends AbstractController
{
    public const ALIVE         = false;
    public const PRIV          = false;
    public const DEFAULT_LIMIT = 200;
    public const FIELD_SIZE    = 40;
    public const MAX_CHATS     = 3000;
    public const MIN_CHATS     = 30;

    /**
     * Take in a chat and record it to the database.
     *
     * @return Response
     */
    public function receive()
    {
        $char_id = SessionFactory::getSession()->get('player_id');
        $message = RequestWrapper::getPostOrGet('message');
        $error   = null;

        if (!empty($message)) {
            if ($char_id) {
                Communication::sendChat($char_id, $message);
            } else {
                $error = 'You must be logged in to chat.';
            }
        }

        return new RedirectResponse('/village/'.($error ? '?error='.rawurlencode($error) : ''));
    }

    /**
     * Pull & display the chats and a chat send if logged in
     *
     * @return Response
     */
    public function index()
    {
        $request    = RequestWrapper::$request;
        $view_all   = $request->get('view_all');
        $chatlength = max(self::DEFAULT_LIMIT, (int) $request->get('chatlength'));
        $chatlength = min(self::MAX_CHATS, max(self::MIN_CHATS, $chatlength));
        $chats      = $this->getChats($view_all ? null : $chatlength);

        $parts = [
            'field_size'        => self::FIELD_SIZE,
            'target'            => $_SERVER['PHP_SELF'],
            'chats'             => $chats,
            'error'             => $request->get('error'),
            'more_chats_to_see' => (!$view_all && $chatlength < $this->getChatCount()),
            'authenticated'     => SessionFactory::getSession()->get('authenticated', false),
        ];

        return $this->render($parts);
    }

    /**
     * @return Response
     */
    private function render($parts)
    {
        return new StreamedViewResponse('Chat Board', 'village.tpl', $parts, [ 'quickstat' => false ]);
    }

    /**
     * Get all the chat messages info.
     */
    private function getChats($chatlength = null)
    {
        $chatlength = Filter::toNonNegativeInt($chatlength); // Prevent negatives.
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
    private function getChatCount()
    {
        return query_item("SELECT count(*) FROM chat");
    }
}
