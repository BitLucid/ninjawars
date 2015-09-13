<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT.'control/lib_clan.php');

$confirm     = in('confirm');
$username    = get_username();
$user_id     = get_user_id();
$clan        = get_clan_by_player_id($user_id);
$clan_joiner = in('clan_joiner');
$clan_joiner_name = get_username($clan_joiner);
$agree       = in('agree');
$random      = rand(1001, 9990);
$ninja = new Player($clan_joiner);

if ($clan && $clan_joiner) {
	$clan_id = $clan->getID();
	$clan_name = $clan->getName();

	if(!$ninja->name()){
		$error = 'No such ninja to bring into the clan.';
	} else {
		if ($agree) {
		    $joiner_clan = get_clan_by_player_id($clan_joiner);
		    $joiner_current_clan = ($joiner_clan instanceof Clan ? $clan->getID() : null);

		    $joiner_info = char_info($clan_joiner);
		    $joiner_confirmation_no = ($joiner_info ? $joiner_info['verification_number'] : null);

			if (empty($joiner_current_clan) && $joiner_confirmation_no && $confirm == $joiner_confirmation_no && $agree > 0) {
				$clan_id = $clan->getID();

				// Add the ninja to the clan, sourced by the current char.
				$clan->addMember($ninja, new Player($user_id));
			}
		}
	}
}	// End of else (when clan_name is available).

display_page(
	'clan_confirm.tpl'
	, 'Accept a New Clan Member'
	, get_certain_vars(get_defined_vars(), array('clan'))
	, array(
		'quickstat' => false
	)
);
}
?>
