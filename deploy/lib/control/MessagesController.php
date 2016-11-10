<?php
namespace NinjaWars\core\control;

use Pimple\Container;
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
     *
     * @param Container
     */
    public function sendPersonal(Container $p_dependencies) {
        $request = RequestWrapper::$request;

        if ((int) $request->get('target_id')) {
            $recipient = Player::find((int) $request->get('target_id'));
        } else if ($request->get('to')) {
            $recipient = Player::findByName($request->get('to'));
        } else {
            $recipient = null;
        }

        if ($recipient) {
            Message::create([
                'send_from' => $p_dependencies['session']->get('player_id'),
                'send_to'   => $recipient->id(),
                'message'   => $request->get('message', null),
                'type'      => 0,
            ]);

            return new RedirectResponse('/messages?command=personal&individual_or_clan=1&message_sent_to='.rawurlencode($recipient->name()).'&informational='.rawurlencode('Message sent to '.$recipient->name().'.'));
        } else {
            return new RedirectResponse('/messages?command=personal&error='.rawurlencode('No such ninja to message.'));
        }
    }

    /**
     * Send a certain message to the whole clan.
     *
     * @param Container
     */
    public function sendClan(Container $p_dependencies) {
        $message = RequestWrapper::getPostOrGet('message');
        $type = 1;
        $sender = $p_dependencies['current_player'];
        $clan = Clan::findByMember($sender);
        $target_id_list = $clan->getMemberIds();
        Message::sendToGroup($sender, $target_id_list, $message, $type);

        return new RedirectResponse('/messages?command=clan&individual_or_clan=1&informational='.rawurlencode('Message sent to clan.'));
    }

    /**
     * View the personal private messages.
     *
     * @param Container
     */
    public function viewPersonal(Container $p_dependencies) {
        $request       = RequestWrapper::$request;
        $type          = 0;
        $page          = max(1, (int) $request->get('page'));
        $limit         = 25;
        $offset        = ($page - 1) * $limit;
        $ninja         = $p_dependencies['current_player'];
        $message_count = Message::countByReceiver($ninja, $type); // To count all the messages

        Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

        $parts = array_merge(
            $this->configure(),
            [
                'to'            => $request->get('to', ''),
                'informational' => $request->get('informational'),
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
     *
     * @param Container
     */
    public function viewClan(Container $p_dependencies) {
        $ninja         = $p_dependencies['current_player'];
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
     *
     * @param Container
     */
    public function deletePersonal(Container $p_dependencies) {
        Message::deleteByReceiver($p_dependencies['current_player'], 0);

        return new RedirectResponse('/messages?command=personal&informational='.rawurlencode('Messages deleted'));
    }

    /**
     * Delete the all the messages from your clan.
     *
     * @param Container
     */
    public function deleteClan(Container $p_dependencies) {
        Message::deleteByReceiver($p_dependencies['current_player'], 1);

        return new RedirectResponse('/messages?command=clan&informational='.rawurlencode('Messages deleted'));
    }

    /**
     * Pulls the initial data required to be initialized in the template.
     */
    public function configure() {
        $request = RequestWrapper::$request;

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
            'error'              => $request->get('error'), // Informational message, e.g. after redirections
            'informational'      => $request->get('informational'), // Informational message, e.g. after redirections
            'type'               => null,
            'message_sent_to'    => null,
            'message_to'         => null,
        ];
    }

    public function render($parts, $title='Messages') {
        return new StreamedViewResponse($title, 'messages.tpl', $parts, ['quickstat' => true]);
    }
}
