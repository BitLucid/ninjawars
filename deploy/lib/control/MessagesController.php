<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\Filter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

class MessagesController extends AbstractController {
    const PRIV  = true;
    const ALIVE = false;

    /**
     * Send a private message to a player
     */
    public function sendPersonal() {
        if ((int) RequestWrapper::getPostOrGet('target_id')) {
            $recipient = Player::find((int) RequestWrapper::getPostOrGet('target_id'));
        } else if (RequestWrapper::getPostOrGet('to')) {
            $recipient = Player::findByName(RequestWrapper::getPostOrGet('to'));
        } else {
            $recipient = null;
        }

        if ($recipient) {
            Message::create([
                'send_from' => SessionFactory::getSession()->get('player_id'),
                'send_to'   => $recipient->id(),
                'message'   => RequestWrapper::getPostOrGet('message', null),
                'type'      => 0,
            ]);

            return new RedirectResponse('/messages?command=personal&individual_or_clan=1&message_sent_to='.rawurlencode($recipient->name()).'&informational='.rawurlencode('Message sent to '.$recipient->name().'.'));
        } else {
            return new RedirectResponse('/messages?command=personal&error='.rawurlencode('No such ninja to message.'));
        }
    }

    /**
     * Send a certain message to the whole clan.
     */
    public function sendClan() {
        $message = RequestWrapper::getPostOrGet('message');
        $type = 1;
        $sender = Player::find(SessionFactory::getSession()->get('player_id'));
        $clan = Clan::findByMember($sender);
        $target_id_list = $clan->getMemberIds();
        Message::sendToGroup($sender, $target_id_list, $message, $type);

        return new RedirectResponse('/messages?command=clan&individual_or_clan=1&informational='.rawurlencode('Message sent to clan.'));
    }

    /**
     * View the personal private messages.
     */
    public function viewPersonal() {
        $type               = 0;
        $page               = max(1, (int) RequestWrapper::getPostOrGet('page'));
        $limit              = 25;
        $offset             = ($page - 1) * $limit;
        $ninja              = Player::find(SessionFactory::getSession()->get('player_id'));
        $message_count      = Message::countByReceiver($ninja, $type); // To count all the messages

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = array_merge(
            $this->configure(),
            [
                'to'            => RequestWrapper::getPostOrGet('to', ''),
                'informational' => RequestWrapper::getPostOrGet('informational'),
                'has_clan'      => (boolean)Clan::findByMember($ninja),
                'current_tab'   => 'message',
                'messages'      => Message::findByReceiver($ninja, $type, $limit, $offset),
                'current_page'  => $page,
                'pages'         => ceil($message_count / $limit),
            ]
        );

        return $this->render($parts);
    }

    /**
     * View clan messages
     */
    public function viewClan() {
        $ninja         = Player::find(SessionFactory::getSession()->get('player_id'));
        $page          = max(1, (int) RequestWrapper::getPostOrGet('page'));
        $limit         = 25;
        $offset        = ($page - 1) * $limit;
        $type          = 1; // Clan chat or normal messages.
        $message_count = Message::countByReceiver($ninja, $type); // To count all the messages

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = array_merge(
            $this->configure(),
            [
                'messages'      => Message::findByReceiver($ninja, $type, $limit, $offset),
                'message_count' => $message_count,
                'pages'         => ceil($message_count / $limit),
                'current_page'  => $page,
                'current_tab'   => 'clan',
                'has_clan'      => (boolean)Clan::findByMember($ninja),
            ]
        );

        return $this->render($parts, 'Clan Messages');
    }

    /**
     * Delete the all the messages sent to you personally
     */
    public function deletePersonal() {
        $char_id = SessionFactory::getSession()->get('player_id');
        $type = 0;
        Message::deleteByReceiver(Player::find($char_id), $type);

        return new RedirectResponse('/messages?command=personal&informational='.rawurlencode('Messages deleted'));
    }

    /**
     * Delete the all the messages from your clan.
     */
    public function deleteClan() {
        $char_id = SessionFactory::getSession()->get('player_id');
        $type = 1;
        Message::deleteByReceiver(Player::find($char_id), $type);

        return new RedirectResponse('/messages?command=clan&informational='.rawurlencode('Messages deleted'));
    }

    /**
     * Pulls the initial data required to be initialized in the template.
     */
    public function configure() {
        return [
            'to'                 => null,
            'to_clan'            => null,
            'messenger'          => 'messenger',
            'message'            => null,
            'messages'           => null,
            'messages_type'      => null,
            'individual_or_clan' => null,
            'pages'              => null,
            'current_page'       => null,
            'current_tab'        => null,
            'to'                 => null,
            'target_id'          => null,
            'ninja'              => null,
            'clan'               => null,
            'has_clan'           => null,
            'page'               => null,
            'limit'              => null,
            'offset'             => null,
            'error'              => RequestWrapper::getPostOrGet('error'), // Informational message, e.g. after redirections
            'informational'      => RequestWrapper::getPostOrGet('informational'), // Informational message, e.g. after redirections
            'type'               => null,
            'message_sent_to'    => null,
            'message_to'         => null,
        ];
    }

    public function render($parts, $title='Messages') {
        return new StreamedViewResponse($title, 'messages.tpl', $parts, ['quickstat' => true]);
    }
}
