<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(LIB_ROOT."control/Skill.php");
require_once(LIB_ROOT."control/lib_player.php");

use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\control\AttackLegal;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\ClanFactory;
use \Player;

class PlayerController {
    const PRIV  = false;
    const ALIVE = false;

    public function index() {
        $target        = $player = first_value(in('ninja'), in('player'), in('find'), in('target'));
        $target_id     = first_value(in('target_id'), in('player_id'), get_char_id($target)); // Find target_id if possible.

        $target_player_obj = Player::find($target_id);
        $viewed_name_for_title = null;
        if ($target_player_obj !== null) {
            $viewed_name_for_title = $target_player_obj->name();
        }

        if ($target_player_obj === null) {
            $template = 'no-player.tpl';
            $parts    = array();
        } else {
            $player_info = $target_player_obj->as_array(); // Pull the info out of the object.

            if (!$player_info) {
                $template = 'no-player.tpl';
                $parts    = array();
            } else {
                $viewing_player_obj = Player::find(self_char_id());

                $char_info = char_info($viewing_player_obj->id());

                $self = (self_char_id() && self_char_id() == $player_info['player_id']); // Record whether this is a self-viewing.

                if ($viewing_player_obj !== null && $viewing_player_obj->vo) {
                    $char_id  = $viewing_player_obj->id();
                    $username = $viewing_player_obj->name();
                }

                $player      = $target = $player_info['uname']; // reset the target and target_id vars.
                $target_id   = $player_info['player_id'];

                $target_class_theme = $target_player_obj->getClassTheme();

                // Get the player's kills for this date.
                $kills_today = query_item('select sum(killpoints) from levelling_log where _player_id = :player_id and killsdate = CURRENT_DATE and killpoints > 0', array(':player_id'=>$target_id));

                $viewers_clan       = ($viewing_player_obj !== null ? ClanFactory::clanOfMember($viewing_player_obj) : null);

                // Attack Legal section
                $params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
                $attack_error = 'You must become a ninja first.';
                $attack_allowed = false;
                if(null !== $viewing_player_obj){
                    $AttackLegal     = new AttackLegal($viewing_player_obj, $target_player_obj, $params);
                    $attack_allowed  = $AttackLegal->check(false);
                    $attack_error    = $AttackLegal->getError();
                }

                $sel_rank_spot = "SELECT rank_id FROM rankings WHERE player_id = :char_id limit 1";
                $rank_spot = query_item($sel_rank_spot, array(':char_id'=>$player_info['player_id']));

                // Display the player info.
                $status_list          = get_status_list($player);
                $level_category       = level_category($player_info['level']);
                $gurl = $gravatar_url = generate_gravatar_url($target_player_obj);

                if ($viewing_player_obj !== null && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
                    // Attack or Duel

                    $skillDAO = new SkillDAO();

                    $is_admin = false;
                    if($viewing_player_obj){
                        $is_admin = $viewing_player_obj->isAdmin();
                    }

                    if(!$is_admin){
                        $combat_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'combat', $viewing_player_obj->vo->level);
                        $targeted_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'targeted', $viewing_player_obj->vo->level);
                    } else {
                        $combat_skills = $skillDAO->all('combat');
                        $targeted_skills = $skillDAO->all('targeted');
                    }

                    // Pull the items and some necessary data about them.
                    $items = inventory_counts($char_id);

                    $valid_items = rco($items);// row count

                }	// End of the there-was-no-attack-error section

                $set_bounty_section     = '';
                $communication_section  = '';
                $player_clan_section    = '';

                $clan = ClanFactory::clanOfMember($player_info['player_id']);
                $same_clan = false;

                $player_info = format_health_percent($player_info);

                // Player clan and clan members

                if ($clan) {
                    $viewer_clan  = $viewing_player_obj ? ClanFactory::clanOfMember($viewing_player_obj) : null;
                    $clan_id      = $clan->getID();
                    $clan_name    = $clan->getName();

                    if ($viewer_clan) {
                        $same_clan = ($clan->getID() == $viewer_clan->getID());
                        $display_clan_options = $viewing_player_obj && !$self && $same_clan && $viewing_player_obj->isClanLeader();
                    } else {
                        $same_clan = $display_clan_options = false;
                    }
                }

                // Send the info to the template.

                $template = 'player.tpl';
                $parts = get_certain_vars(get_defined_vars(), array('char_info', 'viewing_player_obj', 'target_player_obj', 'combat_skills',
                    'targeted_skills', 'player_info', 'self', 'rank_spot', 'kills_today', 'level_category',
                    'gravatar_url', 'status_list', 'clan', 'items'));
            }
        }

        return [
            'template' => $template,
            'title'    => 'Ninja'.($viewed_name_for_title? ": $viewed_name_for_title" : ' Profile'),
            'parts'    => $parts,
            'options'  => [
                'quickstat' => 'player',
            ],
        ];
    }

    /**
     * Wrapper to redirect item use via html form to proper pretty url
     * like a final url of /item/use/shuriken/tchalvak
     * from a starting url of http://nw.local/player/use_item/?item=shuriken&target=tchalvak
     */
    public function use_item(){
        $target = in('target_id');
        $item_in = in('item');
        $give = in('give');
        $method = $give? 'give' : 'use';
        $url = 'item/'.$method.'/'.$item_in.'/'.$target;
        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }

}
