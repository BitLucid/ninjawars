<?php
namespace NinjaWars\core\control;

require_once(CORE.'data/Message.php');
require_once(CORE.'data/ClanFactory.php');
require_once(CORE.'control/Player.class.php');

use app\data\Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use \ClanFactory;

class MessagesController {
    const PRIV  = true;
    const ALIVE = false;

    public function sendPersonal() {
        $char_id         = self_char_id();
        $to              = in('to'); // The target of the message, if any were specified.
        $to              = ($to ? $to : get_setting('last_messaged')); // Text @username
        $message         = in('message', null, null); // Unfiltered input for this message.
        $target_id       = ((int) in('target_id') ? (int) in('target_id') : ($to ? get_user_id($to) : null)); // Id takes precedence
        $messaged        = in('messaged');
        $type            = 0;
        $message_sent_to = null;
        $current_tab     = 'messages';

        if ($target_id) {
            if ($target_id) {
                Message::create(
                    [
                        'send_from' => $char_id,
                        'send_to'   => $target_id,
                        'message'   => $message,
                        'type'      => $type
                    ]
                );

                $message_sent_to = $to;
                $message_to      = 'individual';
                $type            = 0;
            }

            $to = get_char_name($target_id);
        } else {
            return new RedirectResponse('/messages.php?command=personal&error='.url('No such ninja to message.'));
        }

        set_setting('last_messaged', $to);

        return new RedirectResponse('/messages.php?command=personal&individual_or_clan=1&message_sent_to='.url($to).'&informational='.url('Message sent to '.$to.'.'));
    }

    public function sendClan() {
        $message = in('message');
        $type = 1;
        $sender = new Player(self_char_id());
        $clan = ClanFactory::clanOfMember($sender);
        $target_id_list = $clan->getMemberIds();
        Message::sendToGroup($sender, $target_id_list, $message, $type);

        return new RedirectResponse('/messages.php?command=clan&individual_or_clan=1&informational='.url('Message sent to clan.'));
    }

    public function viewPersonal() {
        $to                 = in('to'); // This can come from locations like the pc profile
        $message            = in('message', null, null); // Unfiltered input for this message.
        $to                 = ($to ? $to : get_setting('last_messaged'));
        $type               = 0;
        $page               = in('page', 1, 'non_negative_int');
        $limit              = 25;
        $offset             = non_negative_int(($page - 1) * $limit);
        $ninja              = new Player(self_char_id());
        $clan               = ClanFactory::clanOfMember($ninja);
        $has_clan           = (boolean)$clan;
        $current_tab        = 'messages';
        $individual_or_clan = in('individual_or_clan');
        $informational      = in('informational');
        $messages           = Message::findByReceiver($ninja, $type, $limit, $offset);
        $message_count      = Message::countByReceiver($ninja, $type); // To count all the messages
        $pages              = ceil($message_count / $limit);  // Total pages.
        $current_page       = $page;

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = compact('messages', 'current_tab', 'has_clan', 'to', 'type', 'messages_type',
            'pages', 'current_page', 'informational');

        $parts = array_merge($this->configure(), $parts);

        return $this->render($parts);
    }

    public function viewClan() {
        $command            = in('command');
        $to                 = in('to'); // The target of the message, if any were specified.
        $to                 = ($to ? $to : get_setting('last_messaged'));
        $to_clan            = in('toclan');
        $current_tab        = 'clan';
        $individual_or_clan = in('individual_or_clan');
        $messenger          = in('messenger'); // naive spam detection attempt
        $message            = in('message', null, null); // Unfiltered input for this message.
        $ninja              = new Player(self_char_id());
        $clan               = ClanFactory::clanOfMember($ninja);
        $has_clan           = (boolean)$clan;
        $page               = in('page', 1, 'non_negative_int');
        $limit              = 25;
        $offset             = non_negative_int(($page - 1) * $limit);
        $informational      = in('informational');

        $type = 1; // Clan chat or normal messages.

        $message_sent_to = null; // Names or name to display.
        $message_to = null; // strings clan or individual if sent to those respectively.

        $messages = Message::findByReceiver($ninja, $type, $limit, $offset);

        $message_count = Message::countByReceiver($ninja, $type); // To count all the messages
        $pages         = ceil($message_count / $limit);  // Total pages.
        $current_page = $page;

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = array_merge(
            $this->configure(),
            [
                'messages'      => $messages,
                'message_count' => $message_count,
                'pages'         => $pages,
                'current_page'  => $current_page,
                'current_tab'   => $current_tab,
                'has_clan'      => $has_clan,
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
        Message::deleteByReceiver(new Player($char_id), $type);

        return new RedirectResponse('/messages.php?command=personal&informational='.url('Messages deleted'));
    }

    /**
     * Delete the all the messages from your clan.
     */
    public function deleteClan() {
        $char_id = self_char_id();
        $type = 1;
        Message::deleteByReceiver(new Player($char_id), $type);

        return new RedirectResponse('/messages.php?command=clan&informational='.url('Messages deleted'));
    }

    /**
     * Pulls the initial data required to be initialized in the template.
     */
    public function configure() {
        $ninja              = null;
        $clan               = null;
        $has_clan           = null;
        $message_sent_to    = null;
        $target_id          = null;
        $messages           = null;
        $message            = null;
        $messages_type      = null;
        $message_count      = null;
        $pages              = null;
        $page               = null;
        $message_to         = null;
        $to                 = null;
        $to_clan            = null;
        $current_page       = null;
        $current_tab        = null;
        $individual_or_clan = null;
        $limit              = null;
        $offset             = null;
        $type               = null;
        $informational      = in('informational'); // Informational message, e.g. after redirections
        $error              = in('error'); // Informational message, e.g. after redirections

        return [
            'to'                 => $to,
            'to_clan'            => $to_clan,
            'messenger'          => 'messenger',
            'message'            => $message,
            'messages'           => $messages,
            'messages_type'      => $messages_type,
            'individual_or_clan' => $individual_or_clan,
            'pages'              => $pages,
            'current_page'       => $current_page,
            'current_tab'        => $current_tab,
            'to'                 => $to,
            'target_id'          => $target_id,
            'ninja'              => $ninja,
            'clan'               => $clan,
            'has_clan'           => $has_clan,
            'page'               => $page,
            'limit'              => $limit,
            'offset'             => $offset,
            'error'              => $error,
            'informational'      => $informational,
            'type'               => $type,
            'message_sent_to'    => $message_sent_to,
            'message_to'         => $message_to,
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
