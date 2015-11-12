<?php
namespace app\Controller;

require_once(LIB_ROOT."control/lib_chat.php"); // Require all the chat helper and rendering functions.

/**
* The controller for effects of a village request and the default index display of the page
**/
class VillageController {

	const ALIVE                  = false;
	const PRIV                   = false;

	const DEFAULT_LIMIT  = 200;
	const FIELD_SIZE  = 40;

	/**
	* Take in a chat and record it to the database.
	*
	**/
	public function postnow(){
		$user_id       = self_char_id();
		$message       = in('message', null, 'no filter'); // Essentially no filtering.

		if (!empty($message)) {
			send_chat($user_id, $message);
		}

		redirect('/village.php');
	}

	/**
	* Get the last turns worked by a pc, and pass it to display the default page with form
	**/
	public function index(){
		// Initialize variables to pass to the template.
		$field_size    = self::FIELD_SIZE;
		$target        = $_SERVER['PHP_SELF'];
		$message_count = get_chat_count();

		$view_all      = in('view_all');
		$chatlength    = in('chatlength', self::DEFAULT_LIMIT, 'toInt');
		$chatlength    = min(3000, max(30, $chatlength)); // Min 30, max 3000

		// Output section.
		$chats = get_chats(($view_all? null : $chatlength)); // Limit by chatlength unless a request to view all came in.
		$chats = $chats->fetchAll();
		$more_chats_to_see = (count($chats)<$message_count? true : null);

		$parts = [
			'field_size' => $field_size,
			'target' 	 => $target,
			'chats'  	 => $chats,
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
}
