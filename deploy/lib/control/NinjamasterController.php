<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."data/NpcFactory.php");
require_once(ROOT.'core/data/AccountFactory.php');
require_once(LIB_ROOT."data/Npc.php");
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_player.php"); // For player tags

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Player;
use \NpcFactory;
use \AccountFactory;


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

/**
 * The ninjamaster/admin info
 */
class NinjamasterController {
    const ALIVE = false;
    const PRIV  = true;
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
