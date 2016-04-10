<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\control\AttackLegal;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\extensions\SessionFactory;

class PlayerController extends AbstractController {
    const PRIV  = false;
    const ALIVE = false;

    public function index() {
        $target    = $player = first_value(in('ninja'), in('player'), in('find'), in('target'));
        $target_id = first_value(in('target_id'), in('player_id'));

        $account               = null;
        $attack_error          = null;
        $char_info             = null;
        $clan                  = null;
        $combat_skills         = null;
        $display_clan_options  = null;
        $gravatar_url          = null;
        $items                 = null;
        $kills_today           = null;
        $player_info           = null;
        $rank_spot             = null;
        $same_clan             = false;
        $self                  = null;
        $status_list           = null;
        $targeted_skills       = null;
        $viewed_name_for_title = null;
        $viewing_player_obj    = null;

        if ($target_id) {
            $target_player_obj = Player::find($target_id);
        } else {
            $target_player_obj = Player::findByName($target);
        }

        if ($target_player_obj !== null) {
            $viewed_name_for_title = $target_player_obj->name();
        }

        if ($target_player_obj === null) {
            $template = 'no-player.tpl';
            $parts    = array();
        } else {
            $account = Account::findByChar($target_player_obj);
            $player_info = $target_player_obj->as_array(); // Pull the info out of the object.

            if (!$player_info) {
                $template = 'no-player.tpl';
                $parts    = array();
            } else {
                $viewing_player_obj = Player::find(SessionFactory::getSession()->get('player_id'));

                // Record whether this is a self-viewing.
                $self = ($viewing_player_obj && $viewing_player_obj->id() === $target_player_obj->id());

                if ($viewing_player_obj !== null) {
                    $char_info = $viewing_player_obj->dataWithClan();
                    $char_id   = $viewing_player_obj->id();
                    $username  = $viewing_player_obj->name();
                } else {
                    $char_info = [];
                }

                $player    = $target = $player_info['uname']; // reset the target and target_id vars.
                $target_id = $player_info['player_id'];

                // Get the player's kills for this date.
                $kills_today = query_item('select sum(killpoints) from levelling_log where _player_id = :player_id and killsdate = CURRENT_DATE and killpoints > 0', array(':player_id'=>$target_id));

                $viewers_clan       = ($viewing_player_obj !== null ? ClanFactory::clanOfMember($viewing_player_obj) : null);

                // Attack Legal section
                $params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
                $attack_error = 'You must become a ninja first.';
                $attack_allowed = false;
                if (null !== $viewing_player_obj) {
                    $AttackLegal     = new AttackLegal($viewing_player_obj, $target_player_obj, $params);
                    $attack_allowed  = $AttackLegal->check(false);
                    $attack_error    = $AttackLegal->getError();
                }

                $sel_rank_spot = "SELECT rank_id FROM rankings WHERE player_id = :char_id limit 1";
                $rank_spot = query_item($sel_rank_spot, array(':char_id'=>$player_info['player_id']));

                // Display the player info.
                $status_list          = Player::getStatusList($player);
                $gurl = $gravatar_url = $target_player_obj->avatarUrl();

                if ($viewing_player_obj !== null && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
                    // Attack or Duel

                    $skillDAO = new SkillDAO();

                    $is_admin = false;
                    if($viewing_player_obj){
                        $is_admin = $viewing_player_obj->isAdmin();
                    }

                    if(!$is_admin){
                        $combat_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->_class_id, 'combat', $viewing_player_obj->level);
                        $targeted_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->_class_id, 'targeted', $viewing_player_obj->level);
                    } else {
                        $combat_skills = $skillDAO->all('combat');
                        $targeted_skills = $skillDAO->all('targeted');
                    }

                    // Pull the items and some necessary data about them.
                    $inventory = new Inventory($viewing_player_obj);
                    $items = $inventory->counts();

                }	// End of the there-was-no-attack-error section

                $set_bounty_section    = '';
                $communication_section = '';
                $player_clan_section   = '';

                $clan = ClanFactory::clanOfMember($player_info['player_id']);

                // Player clan and clan members

                if ($clan) {
                    $viewer_clan  = ($viewing_player_obj ? ClanFactory::clanOfMember($viewing_player_obj) : null);
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
                $combat_skills = ($combat_skills ? $combat_skills->fetchAll() : null);

                $parts = [
                    'char_info'            => $char_info,
                    'viewing_player_obj'   => $viewing_player_obj,
                    'target_player_obj'    => $target_player_obj,
                    'combat_skills'        => $combat_skills,
                    'targeted_skills'      => $targeted_skills,
                    'player_info'          => $player_info,
                    'self'                 => $self,
                    'rank_spot'            => $rank_spot,
                    'kills_today'          => $kills_today,
                    'gravatar_url'         => $gravatar_url,
                    'status_list'          => $status_list,
                    'clan'                 => $clan,
                    'items'                => $items,
                    'account'              => $account,
                    'same_clan'            => $same_clan,
                    'display_clan_options' => $display_clan_options,
                    'attack_error'         => $attack_error,
                ];
            }
        }

        $parts['authenticated'] = SessionFactory::getSession()->get('authenticated', false);

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
        $url = 'item/'.rawurlencode($method).'/'.rawurlencode($item_in).'/'.rawurlencode($target);
        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }

    /**
     * Wrapper to redirect skill use into pretty urls
     * like a final url of /skill/go/firebolt/tchalvak
     * from a starting url of http://nw.local/player/use_skill/?act=firebolt&target=tchalvak
     */
    public function use_skill(){
        $target = in('target');
        $act = in('act');
        $url = 'skill/use/'.rawurlencode($act).'/'.rawurlencode($target);
        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }

}
