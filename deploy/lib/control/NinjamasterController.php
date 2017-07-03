<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\AdminViews;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use \InvalidArgumentException;
use NinjaWars\core\control\ShopController;

/**
 * The ninjamaster/admin info
 */
class NinjamasterController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    protected $self = null;

    public function __construct() {
        $this->self = Player::findPlayable($this->getAccountId());
    }

    /**
     * Check user authentication as an admin before continuing.
     */
    private function checkAuth(){
        if (!$this->self || !$this->self->isAdmin()) {
            return new RedirectResponse(WEB_ROOT);
        }
    }

    /**
     * Display the main admin area
     *
     * Includes player viewing, account duplicates checking, npc balacing
     *
     * @return Response
     */
    public function index() {
        $request = RequestWrapper::$request;
        $this->checkAuth();

        $error            = null;
        $char_infos        = null;
        $char_inventory    = null;
        $first_message     = null;
        $first_char       = null;
        $first_account     = null;
        $first_description = null;
        $dupes            = AdminViews::dupedIps();
        $stats            = AdminViews::highRollers();
        $npcs             = NpcFactory::allNonTrivialNpcs();
        $trivial_npcs      = NpcFactory::allTrivialNpcs();

        $items = $this->items();

        $char_ids  = preg_split("/[,\s]+/", $request->get('view'));
        $char_name = trim($request->get('char_name'));

        if ($char_name) { // View a target non-self character
            $first_char = Player::findByName($char_name);
            if(null !== $first_char && null !== $first_char->id()){
                $char_ids  = [$first_char->id()];
            }
        }

        if (is_array($char_ids)) {
            // Get a different first character if an array is specified
            $first_char        = ($first_char ? $first_char : Player::find(reset($char_ids)));
            if($first_char){
                assert($first_char instanceof Player);
                $first_account     = Account::findByChar($first_char);
                $char_inventory    = AdminViews::charInventory($first_char);
                $first_message     = $first_char->messages;
                $first_description = $first_char->description;
            }
            // All the rest multi-character table view
            try{
                $char_infos        = AdminViews::charInfos($char_ids);
            } catch(InvalidArgumentException $e){
                $error = $e->getMessage();
            }
        }

        $parts = [
            'error'             => $error,
            'stats'             => $stats,
            'first_char'        => $first_char,
            'first_description' => $first_description,
            'first_message'     => $first_message,
            'first_account'     => $first_account,
            'char_infos'        => $char_infos,
            'dupes'             => $dupes,
            'char_inventory'    => $char_inventory,
            'char_name'         => $char_name,
            'npcs'              => $npcs,
            'items'             => $items,
            'trivial_npcs'      => $trivial_npcs,
        ];

        return new StreamedViewResponse('Admin Actions', 'ninjamaster.tpl', $parts);
    }

    /**
     * Pull the items for administrative review
     */
    public function items(){
        $this->checkAuth();
        $item_costs = ShopController::itemForSaleCosts(true); // Show administrative entries.
        return $item_costs;
    }

    /**
     * Display the tools page
     *
     * @return Response
     */
    public function tools() {
        $this->checkAuth();
        return new StreamedViewResponse('Admin Tools', 'page.tools.tpl', [], [ 'private' => false ]);
    }

    /**
     * Display a list of characters ranked by score/difficulty.
     *
     * @return Response
     */
    public function player_tags() {
        $this->checkAuth();
        return new StreamedViewResponse('Player Character Tags', 'player-tags.tpl', [ 'player_size' => $this->playerSize() ], [ 'quickstat' => false ]);
    }

    /**
     * @return Array
     */
    private function playerSize() {
        $res = [];
        DatabaseConnection::getInstance();
        $sel = "SELECT (level-3-round(days/5)) AS sum, player_id, uname FROM players WHERE active = 1 AND health > 0 ORDER BY sum DESC";
        $statement = DatabaseConnection::$pdo->query($sel);

        $player_info = $statement->fetch();

        $max = $player_info['sum'];

        do {
            // make percentage of highest, multiply by 10 and round to give a 1-10 size
            $res[$player_info['uname']] = [
                'player_id' => $player_info['player_id'],
                'size'      => $this->calculatePlayerSize($player_info['sum'], $max),
            ];
        } while ($player_info = $statement->fetch());

        return $res;
    }

    /**
     * @param int $p_rank
     * @param int $p_max
     * @return int
     */
    private function calculatePlayerSize($p_rank, $p_max) {
        return floor(( (($p_rank-1 < 1 ? 0 : $p_rank-1)) / $p_max)*10)+1;
    }
}
