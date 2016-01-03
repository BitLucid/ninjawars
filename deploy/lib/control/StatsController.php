<?php
namespace NinjaWars\core\control;

use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\DatabaseConnection;
use \Player;
use \PlayerDAO;

require_once(LIB_ROOT.'control/lib_player.php'); // Player info display pieces.
require_once(LIB_ROOT.'control/lib_status.php'); // Status alterations.

/**
 * Handle updates for changing details and profile details
 */
class StatsController {

	const ALIVE                  = false;
	const PRIV                   = true;

	/**
	 * 	Should match the limit in limitStatChars.js - ajv: No, limitStatChars.js should be dynamically generated with this number from a common location -
	 */
	const PROFILE_MAX_LENGTH = 500;

	/**
	* Change account details
	*/
	public function changeDetails() {
		$char_id	= self_char_id();
		$char		= new Player($char_id);

		$description	= post('description', $char->description());
		$goals			= post('goals', $char->goals());
		$instincts		= post('instincts', $char->instincts());
		$beliefs		= post('beliefs', $char->beliefs());
		$traits			= post('traits', $char->traits());

		assert((bool)$description);
		assert((bool)$goals);

		// Check that the text features don't differ
		$char->set_description($description);
		$char->set_goals($goals);
		$char->set_instincts($instincts);
		$char->set_beliefs($beliefs);
		$char->set_traits($traits);

		$changed = PlayerDAO::saveDetails($char);

		return new RedirectResponse('/stats.php?changed='.(int)$changed);
	}

	/**
	* Update profile
	*/
	public function updateProfile()
	{
		$char_id			= self_char_id();
		$new_profile		= trim(in('newprofile', null, null)); // Unfiltered input.
		$profile_changed	= false;
		$error				= '';

		if (!empty($new_profile)) {
			DatabaseConnection::getInstance();
			$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET messages = :profile WHERE player_id = :char_id');
			$statement->bindValue(':profile', $new_profile);
			$statement->bindValue(':char_id', $char_id);
			$statement->execute();
			$profile_changed = true;
		} else {
			$error = 'Cannot enter a blank profile.';
		}

		$query_str = [];
		if ($profile_changed) {
			$query_str['profile_changed'] = 1;
		} else {
			$query_str['error'] = $error;
		}

		$raw_query_str = count($query_str) ? '?'.http_build_query($query_str, null, '&') : null;
		return new RedirectResponse('/stats.php'.$raw_query_str);
	}

	/**
	* Display the default stats page
	**/
	public function index() {
		return $this->render([]);
	}

	private function render($parts) {
		// default parts
		$char_id		= self_char_id();
		$char			= new Player($char_id);
		$player			= self_info();
		$player_clan	= get_clan_by_player_id($char_id);
		// $player['created_date']=$player['created_date']? date("c", strtotime($player['created_date'])) : null;
		$class_theme	= class_theme($char->class_identity());
		$level_category	= level_category($player['level']);

		$parts = array_merge([
			'player'		=> $player,
			'player_clan'	=> $player_clan,
			'clan_id'		=> $player_clan ? $player_clan->getID() : false,
			'clan_name'		=> $player_clan ? $player_clan->getName() : false,

			'status_list'		=> get_status_list(),
			'profile_editable'	=> $player['messages'],
			'rank_display'		=> get_rank($char_id),

			'traits'		=> $char->traits(),
			'beliefs'		=> $char->beliefs(),
			'instincts'		=> $char->instincts(),
			'goals'			=> $char->goals(),
			'description'	=> $char->description(),

			'username'				=> self_name(),
			'level_category'		=> $level_category,
			'class_theme'			=> $class_theme,
			'gravatar_url'			=> generate_gravatar_url($player['player_id']),
			'profile_max_length'	=> self::PROFILE_MAX_LENGTH,

			'error'				=> in('error'),
			'successMessage'	=> '',
			'profile_changed'	=> (bool) in('profile_changed'),
			'changed'			=> (bool) in('changed'),
		], $parts);

		return [
			'template'	=> 'stats.tpl',
			'title'		=> 'Ninja Stats',
			'parts'		=> $parts,
			'options'	=> [
				'quickstat' => 'player',
			],
		];
	}
}
