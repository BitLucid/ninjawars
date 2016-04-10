<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\PlayerDAO;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;

/**
 * Handle updates for changing details and profile details
 */
class StatsController extends AbstractController {
	const ALIVE = false;
	const PRIV  = true;

	/**
	 * 	Should match the limit in limitStatChars.js - ajv: No, limitStatChars.js should be dynamically generated with this number from a common location -
	 */
	const PROFILE_MAX_LENGTH = 500;

    /**
     * Change account details
     */
	public function changeDetails() {
		$char = Player::find(SessionFactory::getSession()->get('player_id'));

		$description	= post('description', $char->description);
		$goals			= post('goals', $char->goals);
		$instincts		= post('instincts', $char->instincts);
		$beliefs		= post('beliefs', $char->beliefs);
		$traits			= post('traits', $char->traits);

		// Check that the text features don't differ
		$char->description = $description;
		$char->goals       = $goals;
		$char->instincts   = $instincts;
		$char->beliefs     = $beliefs;
		$char->traits      = $traits;

		$char = $char->save();

		return new RedirectResponse('/stats?changed=1');
	}

    /**
     * Update profile
     */
	public function updateProfile() {
		$char               = Player::find(SessionFactory::getSession()->get('player_id'));
		$new_profile		= trim(in('newprofile', null, null)); // Unfiltered input.
		$profile_changed	= false;
		$error				= '';

		if (!empty($new_profile)) {
			DatabaseConnection::getInstance();
			$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET messages = :profile WHERE player_id = :char_id');
			$statement->bindValue(':profile', $new_profile);
			$statement->bindValue(':char_id', $char->id());
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
		return new RedirectResponse('/stats'.$raw_query_str);
	}

    /**
     * Display the default stats page
     */
    public function index() {
        $char = Player::find(SessionFactory::getSession()->get('player_id'));

        $parts = [
            'char'               => $char,
            'clan'               => Clan::findByMember($char),
            'status_list'        => Player::getStatusList(),
            'rank_display'       => $this->getRank($char->id()),
            'profile_max_length' => self::PROFILE_MAX_LENGTH,
            'error'              => in('error'),
            'successMessage'     => '',
            'profile_changed'    => (bool) in('profile_changed'),
            'changed'            => (bool) in('changed'),
        ];

        return $this->render($parts);
    }

    private function render($parts) {
        return [
            'template'	=> 'stats.tpl',
            'title'		=> 'Ninja Stats',
            'parts'		=> $parts,
            'options'	=> [
                'quickstat' => 'player',
            ],
        ];
    }

    /**
     * Get the rank integer for a certain character.
     */
    private function getRank($p_char_id) {
        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE player_id = :player");
        $statement->bindValue(':player', $p_char_id);
        $statement->execute();

        $rank = $statement->fetchColumn();

        return ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
    }
}
