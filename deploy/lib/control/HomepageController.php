<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT.'control/lib_player_list.php'); // Used for member_counts()

use NinjaWars\core\data\Message;
use \Player as Player;


/**
 * display the standard homepage, and maybe eventually the splash page
 */
class HomepageController {
	const PRIV      = false;
	const ALIVE     = false;
	private $logged_in = false;

	/**
	 *
	 */
	public function __construct(){
		$this->logged_in = (bool) self_char_id();
	}

	/**
	 * Parse whether to display the splash page or the logged-in homepage.
	 */
	public function index(){
		if($this->logged_in){
			return $this->game();
		} else {
			return $this->splash();
		}
	}

	/**
	 * The standard homepage
	 */
	private function game(){
		// Initialize page display vars.
		$unread_message_count = 0;

		// Get the actual values of the vars.
		$player_info = self_info();
		$ninja = new Player(self_char_id());

		$unread_message_count = Message::where([
	            'send_to' => $ninja->id(),
	            'unread'  => 1,
	        ])->count();

		$member_counts = \member_counts();

		// Create the settings to pass to the page.
		$options = array('is_index'=>true);

		// Assign these vars to the template.
		$parts = array(
			'main_src'           => 'main.php'
			, 'body_classes'     => 'main-body'
			, 'version'          => 'NW Version 1.7.5 2010.12.05'
			, 'ninja'			 => $ninja
			, 'player_info'      => $player_info
			, 'unread_message_count' => $unread_message_count
			, 'members'          => $member_counts['active']
			, 'membersTotal'     => $member_counts['total']
		);

		return [
			'template'=>'index.tpl', 
			'title'=>'Live by the Shuriken', 
			'parts'=>$parts, 
			'options'=>$options,
			];
	}

	/**
	 * The main starting splash homepage (for logged-out user)
	 */
	private function splash(){
		$title       = 'Live by the Shuriken';
		$unread_message_count = 0;

		$options = array('is_index'=>true);

		$member_counts = member_counts();

		// Assign these vars to the template.
		$parts = array(
			'main_src'           => 'main.php'
			, 'body_classes'     => 'main-body splash'
			, 'version'          => 'NW Version 1.8.0 2014.06.30'
			, 'members'          => $member_counts['active']
			, 'membersTotal'     => $member_counts['total']
		);

		$parts['body_classes'] = 'main-body splash';

		return [
			'template'=>'splash.tpl',
			'title'=>$title,
			'parts'=>$parts,
			'options'=>$options,
			];

		//display_page('splash.tpl', $title, $parts, $options);
	}
	
}
