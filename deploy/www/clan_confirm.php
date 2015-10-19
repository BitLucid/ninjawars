<?php
require_once(CORE.'data/ClanFactory.php');

$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT.'control/lib_clan.php');

$confirm     = (int) in('confirm');
$ninja = new Player(self_char_id());
$ninja_id     = $ninja->id();

$clan        = ClanFactory::clanOfMember($ninja_id);
$clan_name = null;
$clan_id = null;
$potential_clan_joiner = in('clan_joiner');
$joining_ninja = new Player($potential_clan_joiner);
$join_requester_name = $joining_ninja->name();
$agree       = in('agree');
$random      = rand(1001, 9990);
$ninja_added = null;
$join_requester_id = null;
$error = null;

if($clan){
	$clan_id = $clan->getID();
	$clan_name = $clan->getName();

	if(!$joining_ninja->name()){
		$error = 'No such ninja to bring into the clan.';
	} else {
		$join_requester_id = $joining_ninja->id();
		if ($agree && $agree > 0) {
			// Allow joining as long as the verification number is correct.
			if($confirm && $confirm === $joining_ninja->getVerificationNumber()){
				// Add the ninja to the clan, sourced by the current char.
				$result = $clan->addMember($joining_ninja, $ninja); // This will randomize the verification number as well.
				if($result !== true){
					$error = is_string($result)? $result : 'Unable to add member, please try again later.';
				} else{
					$ninja_added = true;
				}
			} else {
				$error = 'That request was old or invalid, please try inviting that ninja again.';
			}
		}
	}
}

display_page(
	'clan_confirm.tpl'
	, 'Accept a New Clan Member'
	, get_certain_vars(get_defined_vars(), array('clan'))
	, array(
		'quickstat' => false
	)
);


}

