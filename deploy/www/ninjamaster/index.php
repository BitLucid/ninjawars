<?php
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."data/NpcFactory.php");
require_once(ROOT.'core/data/AccountFactory.php');
require_once(LIB_ROOT."data/Npc.php");

class AdminViews{

	public static function high_rollers(){
		// Select first few max kills from players.
		// Max turns.
		// Max gold.
		// Max kills
		// etc.
		$res = array();
		$res['gold'] = query_array('select player_id, uname, gold from players order by gold desc limit 10');
		$res['turns'] = query_array('select player_id, uname, turns from players order by turns desc limit 10');
		$res['kills'] = query_array('select player_id, uname, kills from players order by kills desc limit 10');
		$res['health'] = query_array('select player_id, uname, health from players order by health desc limit 10');
		$res['ki'] = query_array('select player_id, uname, ki from players order by ki desc limit 10');
		return $res;
	}

	public static function duped_ips(){
		$host= gethostname();
		$server_ip = gethostbyname($host);
		// Get name, id, and ip from players, grouped by ip matches
		return query('select uname, player_id, days, last_ip from players left join account_players on player_id = _player_id
			left join accounts on _account_id = account_id where uname is not null 
			and last_ip in 
			(SELECT last_ip FROM accounts 
				WHERE (operational = true and confirmed = 1) 
					and (last_ip != \'\' and last_ip != \'127.0.0.1\' and last_ip != :server_ip) 
				GROUP  BY last_ip HAVING count(*) > 1 ORDER BY count(*) DESC limit 30)
			 order by last_ip, days ASC limit 300',
			 [':server_ip'=>$server_ip]);
	}


	// Reformat the character info sets.
	public static function split_char_infos($ids){
		if(is_numeric($ids)){
			$ids = [$ids]; // Wrap it in an array.
		} else { // Get the info for multiple ninjas
			$res = array();
			$ids = explode(',', $ids);
		}
		$first = true;
		foreach($ids as $id){
			$res[$id] = char_info($id, $admin_info=true);
			$res[$id]['first'] = $first;
			unset($res[$id]['messages']); // Exclude the messages for length reasons.
			unset($res[$id]['description']); // Ditto
			$first = false;
		}
		return $res;
	}

	public static function char_inventory($char_id){
		return inventory_counts($char_id);
	}
}

// Redirect for any non-admins.
$char_id = self_char_id();
$self = null;
if(positive_int($char_id)){
	$self = new Player($char_id);
}
if($self instanceof Player && $self->isAdmin()){
	// Admin possibilities start here.

	$view_char = null;

	$dupes = AdminViews::duped_ips();
	$stats = AdminViews::high_rollers();

	$npcs = NpcFactory::allNonTrivialNpcs();
	$trivial_npcs = NpcFactory::allTrivialNpcs();

	$char_name = in('char_name');

	if(is_string($char_name) && trim($char_name)){
		$view_char = get_char_id($char_name);
	}

	// If a request is made to view a character's info, show it.
	$view_char = $view_char? $view_char : in('view');
	$char_infos = $char_inventory = $first_message = null;
	$first_char = null;
	$first_account = null;
	$first_description = null;
	if($view_char){
		$ids = explode(',', $view_char);
		$first_char_id = reset($ids);
		$first_char = new Player($first_char_id);
		$first_account = AccountFactory::findByChar($first_char);
		$char_infos = AdminViews::split_char_infos($view_char);
		$char_inventory = AdminViews::char_inventory($view_char);
		$first_message = $first_char->message();
		$first_description = $first_char->description();
	}


	display_page(
		'ninjamaster.tpl'	// *** Main Template ***
		, 'Admin Actions' // *** Page Title ***
		, ['stats'=>$stats, 'first_char'=>$first_char, 'first_description'=>$first_description, 'first_message'=>$first_message,
			'first_account'=>$first_account, 'char_infos'=>$char_infos, 
			'dupes'=>$dupes, 'char_inventory'=>$char_inventory, 'char_name'=>$char_name, 'npcs'=>$npcs, 
			'trivial_npcs'=>$trivial_npcs] // *** Page Variables ***
	);


} else {
	// Redirect to the root site.
	redirect('/');
}




