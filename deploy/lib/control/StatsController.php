<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\Filter;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     *
     * @param Container
     */
	public function changeDetails(Container $p_dependencies) {
        $request = RequestWrapper::$request;
		$char = $p_dependencies['current_player'];

		$description = $request->get('description', $char->description);
		$goals       = $request->get('goals', $char->goals);
		$instincts   = $request->get('instincts', $char->instincts);
		$beliefs     = $request->get('beliefs', $char->beliefs);
		$traits      = $request->get('traits', $char->traits);

		// Check that the text features don't differ
		$char->description = $description;
		$char->goals       = $goals;
		$char->instincts   = $instincts;
		$char->beliefs     = $beliefs;
		$char->traits      = $traits;

		$char->save();

		return new RedirectResponse('/stats?changed=1');
	}

    /**
     * Update profile
     *
     * @param Container
     */
	public function updateProfile(Container $p_dependencies) {
		$char            = $p_dependencies['current_player'];
		$new_profile     = trim(RequestWrapper::getPostOrGet('newprofile', null));
		$profile_changed = false;
		$error           = '';

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
     *
     * @param Container
     */
    public function index(Container $p_dependencies) {
        $request = RequestWrapper::$request;
        $char = $p_dependencies['current_player'];

        $parts = [
            'char'               => $char,
            'clan'               => Clan::findByMember($char),
            'status_list'        => Player::getStatusList(),
            'rank_display'       => $this->getRank($char->id()),
            'profile_max_length' => self::PROFILE_MAX_LENGTH,
            'error'              => $request->get('error'),
            'successMessage'     => '',
            'profile_changed'    => (bool) $request->get('profile_changed'),
            'changed'            => (bool) $request->get('changed'),
        ];

        return $this->render($parts);
    }

    private function render($parts) {
        return new StreamedViewResponse('Ninja Stats', 'stats.tpl', $parts, ['quickstat' => 'player']);
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
