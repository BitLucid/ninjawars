<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Api;
use NinjaWars\core\data\Enemies;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Message;
use Symfony\Component\HttpFoundation\Response;
use \PDO;

/**
 * This is a class that provides a jsonP get api via passing in a callback
 * It is not a REST api
 */
class ApiController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

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
        $jsoncallback = (!preg_match('/[^a-z_0-9]/i', $dirty_jsoncallback) ? $dirty_jsoncallback : null);

        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Max-Age'       => '3628800',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
        ];

        // Types are whitelisted by methods on the Api class

        $res = null;
        $api = new Api();

        if (isset($type)) {
            // Customized parameters like ?term=&limit=
            if ($type == 'send_chat') {
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

            $res = (!$jsoncallback) ? json_encode($result) : "$jsoncallback(" . json_encode($result) . ")";
        }

        $headers['Content-Type'] = 'text/javascript; charset=utf8';

        return new Response($res, 200, $headers);
    }

}
