<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_chat.php"); // Require all the chat helper and rendering functions.

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* The controller for effects of a village request and the default index display of the page
**/
class ChatController {

	const ALIVE                  = false;
	const PRIV                   = false;

	const DEFAULT_LIMIT  = 200;
	const FIELD_SIZE  = 40;

	/**
	* Take in a chat and record it to the database.
	*
	**/
	public function receive(){
		$char_id       = self_char_id();
		$message       = in('message', null, 'no filter'); // Essentially no filtering.
		$error = null;

		if (!empty($message)){
			if($char_id) {
				send_chat($char_id, $message);
			} else {
				$error = 'You must be logged in to chat.';
			}
		}

		return new RedirectResponse('/village/'.($error? '?error='.rawurlencode($error) : ''));
	}

	/**
	* Pull & display the chats and a chat send if logged in
	**/
	public function index(){
		// Initialize variables to pass to the template.
		$field_size    = self::FIELD_SIZE;
		$target        = $_SERVER['PHP_SELF'];
		$all_chats_count = $this->getChatCount();

		$view_all      = in('view_all');
		$error 		   = in('error');
		$chatlength    = in('chatlength', self::DEFAULT_LIMIT, 'toInt');
		$chatlength    = min(3000, max(30, $chatlength)); // Min 30, max 3000

		// Output section.
		$chats = $this->getChats(($view_all? null : $chatlength)); // Limit by chatlength unless a request to view all came in.
		$more_chats_to_see = (rco($chats)<$all_chats_count? true : null);

		$parts = [
			'field_size' => $field_size,
			'target' 	 => $target,
			'chats'  	 => $chats,
			'error'      => $error,
			'more_chats_to_see' => $more_chats_to_see,
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
        $bindings = array();
        if ($limit) {
            $bindings[':limit'] = $chatlength;
        }

        $chats = query_resultset("SELECT sender_id, uname, message, date, age(now(), date) AS ago FROM chat
            JOIN players ON chat.sender_id = player_id ORDER BY chat_id DESC ".$limit, $bindings);

        return $chats;
    }

    /**
     * Total number of chats available.
     */
    private function getChatCount() {
        return query_item("SELECT count(*) FROM chat");
    }

}
