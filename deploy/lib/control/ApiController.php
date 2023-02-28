<?php

namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\Api;
use Symfony\Component\HttpFoundation\Response;
use PDO;

/**
 * This is a class that provides a jsonP get api via passing in a callback
 * It is not a REST api
 */
class ApiController extends AbstractController {
    public const ALIVE = false;
    public const PRIV  = false;

    /**
     * Determine which function to call to get the json for.
     *
     * @return Response
     */
    public function nw_json() {
        $request = RequestWrapper::$request;
        $type = $request->get('type');
        $dirty_jsoncallback = $request->get('jsoncallback'); // No callback just gives json
        $data = $request->get('data');

        // Reject if non alphanumeric and _ chars
        $jsoncallback = (!preg_match('/[^a-z_0-9]/i', $dirty_jsoncallback ?? '') ? $dirty_jsoncallback : null);
        if ($jsoncallback !== $dirty_jsoncallback) {
            return new Response(json_encode(['error' => 'Invalid callback']), 400, ['Content-type' => 'text/javascript; charset=utf8']);
        }

        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Max-Age'       => '3628800',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
        ];

        // Types are whitelisted by methods on the Api class

        $res = null;
        $api = new Api();
        $allowed = [
            'send_chat',
            'new_chats',
            'chats',
            'char_search',
            'latest_chat_id',
            'latest_event',
            'latest_message',
        ];

        if (isset($type) && (in_array($type, $allowed) || method_exists($api, $type))) {
            // Customized parameters like ?term=&limit=
            if ($type == 'latest_message') {
                $result = $api->latestMessage();
            } elseif ($type == 'latest_event') {
                $result = $api->latestEvent();
            } elseif ($type == 'latest_chat_id') {
                $result = $api->latestChatId();
            } elseif ($type == 'send_chat') {
                $result = $api->sendChat($request->get('msg'));
            } elseif ($type == 'new_chats') {
                $chat_since = $request->get('since', null);
                $result = $api->newChats($chat_since);
            } elseif ($type == 'chats') {
                $chat_limit = $request->get('chat_limit', 20);
                $result = $api->chats($chat_limit);
            } elseif ($type == 'char_search') {
                $result = $api->charSearch($request->get('term'), $request->get('limit'));
            } else {
                $result = method_exists($api, $type) ? $api->$type($data) : null;
            }

            // Default case to create a jsonp response with the callback if there is a callback
            $res = (!$jsoncallback) ? json_encode($result) : "$jsoncallback(" . json_encode($result) . ")";
        } else { // Not whitelisted, so reject request
            $res = json_encode(['error' => 'Invalid api type']);
            $headers['Content-Type'] = 'text/javascript; charset=utf8';
            return new Response($res, 400, $headers);
        }

        $headers['Content-Type'] = 'text/javascript; charset=utf8';

        return new Response($res, 200, $headers);
    }
}
