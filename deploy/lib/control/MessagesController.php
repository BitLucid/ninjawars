<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Message;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Player;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MessagesController {
    const PRIV  = true;
    const ALIVE = false;

    /**
     * Send a private message to a player
     */
    public function sendPersonal() {
        if ((int) in('target_id')) {
            $target_id = (int) in('target_id');
        } else if (in('to')) {
            $target_id = get_user_id(in('to'));
        } else {
            $target_id = null;
        }

        if ($target_id) {
            Message::create([
                'send_from' => self_char_id(),
                'send_to'   => $target_id,
                'message'   => in('message', null, null),
                'type'      => 0,
            ]);

            $recipient = get_char_name($target_id);

            return new RedirectResponse('/messages?command=personal&individual_or_clan=1&message_sent_to='.url($recipient).'&informational='.url('Message sent to '.$recipient.'.'));
        } else {
            return new RedirectResponse('/messages?command=personal&error='.url('No such ninja to message.'));
        }
    }

    /**
     * Send a certain message to the whole clan.
     */
    public function sendClan() {
        $message = in('message');
        $type = 1;
        $sender = Player::find(self_char_id());
        $clan = ClanFactory::clanOfMember($sender);
        $target_id_list = $clan->getMemberIds();
        Message::sendToGroup($sender, $target_id_list, $message, $type);

        return new RedirectResponse('/messages?command=clan&individual_or_clan=1&informational='.url('Message sent to clan.'));
    }

    /**
     * View the personal private messages.
     */
    public function viewPersonal() {
        $type               = 0;
        $page               = in('page', 1, 'non_negative_int');
        $limit              = 25;
        $offset             = non_negative_int(($page - 1) * $limit);
        $ninja              = Player::find(self_char_id());
        $message_count      = Message::countByReceiver($ninja, $type); // To count all the messages

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = array_merge(
            $this->configure(),
            [
                'to'            => (in('to') ? in('to') : ''),
                'informational' => in('informational'),
                'has_clan'      => (boolean)ClanFactory::clanOfMember($ninja),
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
        $ninja         = Player::find(self_char_id());
        $page          = in('page', 1, 'non_negative_int');
        $limit         = 25;
        $offset        = non_negative_int(($page - 1) * $limit);
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
                'has_clan'      => (boolean)ClanFactory::clanOfMember($ninja),
            ]
        );

        return $this->render($parts, 'Clan Messages');
    }

    /**
     * Delete the all the messages sent to you personally
     */
    public function deletePersonal() {
        $char_id = self_char_id();
        $type = 0;
        Message::deleteByReceiver(Player::find($char_id), $type);

        return new RedirectResponse('/messages?command=personal&informational='.url('Messages deleted'));
    }

    /**
     * Delete the all the messages from your clan.
     */
    public function deleteClan() {
        $char_id = self_char_id();
        $type = 1;
        Message::deleteByReceiver(Player::find($char_id), $type);

        return new RedirectResponse('/messages?command=clan&informational='.url('Messages deleted'));
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
            'error'              => in('error'), // Informational message, e.g. after redirections
            'informational'      => in('informational'), // Informational message, e.g. after redirections
            'type'               => null,
            'message_sent_to'    => null,
            'message_to'         => null,
        ];
    }

    public function render($parts, $title=null) {
        return [
            'template'  => 'messages.tpl'
            , 'title'   => $title?: 'Messages'
            , 'parts'   => $parts
            , 'options' => ['quickstat' => true]
        ];
    }
}
