<?php
namespace NinjaWars\core\control;

require_once(CORE.'control/Player.class.php');

use NinjaWars\core\data\Message;
use NinjaWars\core\data\ClanFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;

class MessagesController {
    const PRIV  = true;
    const ALIVE = false;

    public function sendPersonal() {
        $char_id         = self_char_id();
        $to              = in('to'); // The target of the message, if any were specified.
        $to              = ($to ? $to : get_setting('last_messaged')); // Text @username
        $message         = in('message', null, null); // Unfiltered input for this message.
        $target_id       = ((int) in('target_id') ? (int) in('target_id') : ($to ? get_user_id($to) : null)); // Id takes precedence
        $type            = 0;
        $message_sent_to = null;

        if ($target_id) {
            if ($target_id) {
                Message::create([
                    'send_from' => $char_id,
                    'send_to'   => $target_id,
                    'message'   => $message,
                    'type'      => $type,
                ]);

                $message_sent_to = $to;
                $type            = 0;
            }

            $to = get_char_name($target_id);

            set_setting('last_messaged', $to);

            return new RedirectResponse('/messages.php?command=personal&individual_or_clan=1&message_sent_to='.url($to).'&informational='.url('Message sent to '.$to.'.'));
        } else {
            return new RedirectResponse('/messages.php?command=personal&error='.url('No such ninja to message.'));
        }
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
        $current_tab        = 'clan';
        $ninja              = new Player(self_char_id());
        $clan               = ClanFactory::clanOfMember($ninja);
        $has_clan           = (boolean)$clan;
        $page               = in('page', 1, 'non_negative_int');
        $limit              = 25;
        $offset             = non_negative_int(($page - 1) * $limit);

        $type = 1; // Clan chat or normal messages.

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
