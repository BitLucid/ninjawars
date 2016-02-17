<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\ClanFactory;
use \Player as Player;

/**
 * Handle the listing of events
 */
class EventsController {
    const ALIVE          = false;
    const PRIV           = true;

    public function __construct(){
    }

    /**
     * Display the combat/action events and mark them as read when displayed.
     */
    public function index(){
    	$char = new Player(self_char_id());
		$events = get_events($char->id(), 300);

		// Check for clan to use it in the nav tabs.
		$has_clan  = (bool)ClanFactory::clanOfMember($char);

		read_events($char->id()); // mark events as viewed.

		$template = 'events.tpl';
		$title = 'Events';
		$parts = ['events'=>$events, 'has_clan'=>$has_clan, 'char'=>$char];
		$options = ['quickstat' => 'player'];
		return [
			'title'=>$title,
			'template'=>$template,
			'parts'=>$parts,
			'options'=>$options
			];
    }

}
