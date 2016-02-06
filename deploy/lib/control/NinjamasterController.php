<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."data/NpcFactory.php");
require_once(LIB_ROOT."data/AdminViews.php");
require_once(ROOT.'core/data/AccountFactory.php');
require_once(LIB_ROOT."data/Npc.php");
require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_player.php"); // For player tags

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use \NpcFactory;
use \AccountFactory;
use \NinjaWars\core\data\AdminViews;



/**
 * The ninjamaster/admin info
 */
class NinjamasterController {
    const ALIVE = false;
    const PRIV  = false;
    protected $char_id = null;

    public function __construct(){
        $this->char_id = self_char_id();
        $this->self = Player::find($this->char_id);
    }

    /**
     * If the player isn't logged in, or isn't admin,
     * return a redirect
     */
    public function requireAdmin($player){
        if($player === null || !$player instanceof Player || !$player->isAdmin()){
            // Redirect to the root site.
            return new RedirectResponse(WEB_ROOT);
        } else {
            return true;
        }

    }

    /**
     * Display the main admin area for player viewing,
     * account duplicates checking,
     * npc balacing,
     */
    public function index(){

        $result = $this->requireAdmin($this->self);
        if($result instanceof RedirectResponse){
            return $result;
        }

        $view_char = null;
        // View a target non-self character
        $char_name = in('char_name');
        if(is_string($char_name) && trim($char_name)){
            $view_char = get_char_id($char_name);
        }
        // If a request is made to view a character's info, show it.
        $view_char = first_value($view_char, in('view'));

        $dupes = AdminViews::duped_ips();
        $stats = AdminViews::high_rollers();

        $npcs = NpcFactory::allNonTrivialNpcs();
        $trivial_npcs = NpcFactory::allTrivialNpcs();

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

        $parts = [
            'stats'=>$stats, 'first_char'=>$first_char, 
            'first_description'=>$first_description, 'first_message'=>$first_message,
            'first_account'=>$first_account, 'char_infos'=>$char_infos, 
            'dupes'=>$dupes, 'char_inventory'=>$char_inventory, 
            'char_name'=>$char_name, 'npcs'=>$npcs, 
            'trivial_npcs'=>$trivial_npcs
            ];

        return [
            'title'=>'Admin Actions',
            'template'=>'ninjamaster.tpl',
            'parts'=>$parts,
            'options'=>null
            ];
    }

    /**
     * Display the tools page
     */
    public function tools(){
        $result = $this->requireAdmin($this->self);
        if($result instanceof RedirectResponse){
            return $result;
        }
        return [
            'title'=>'Admin Tools',
            'template'=>'page.tools.tpl',
            'parts'=>[],
            'options'=>['private'=>false]
            ];
    }

    /**
     * Display a list of characters ranked by score/difficulty.
     */
    public function player_tags(){
        $result = $this->requireAdmin($this->self);
        if($result instanceof RedirectResponse){
            return $result;
        }
        $player_size = player_size();
        return [
            'title'=>'Player Character Tags',
            'template'=>'player-tags.tpl',
            'parts'=>['player_size' => $player_size],
            'options'=>['quickstat'=>false]
            ];
    }

}
