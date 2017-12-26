<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\control\AttackLegal;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

class PlayerController extends AbstractController {
    const PRIV  = false;
    const ALIVE = false;

    public function index() {
        $request   = RequestWrapper::$request;
        $target    = $request->get('player');
        $target_id = $request->get('player_id');

        if ($target_id) {
            $target_player_obj = Player::find($target_id);
        } else {
            $target_player_obj = Player::findByName($target);
        }

        if ($target_player_obj === null) {
            $template              = 'no-player.tpl';
            $viewed_name_for_title = null;
            $parts                 = array();
        } else {
            $attack_error          = 'You must become a ninja first.';
            $clan                  = Clan::findByMember($target_player_obj);
            $combat_skills         = null;
            $display_clan_options  = false;
            $items                 = null;
            $same_clan             = false;
            $self                  = false;
            $targeted_skills       = null;
            $template              = 'player.tpl';
            $viewed_name_for_title = $target_player_obj->name();
            $viewing_player_obj    = Player::find(SessionFactory::getSession()->get('player_id'));
            $i_am_dead = null;

            $kills_today = query_item(
                'SELECT sum(killpoints) FROM levelling_log WHERE _player_id = :player_id AND killsdate = CURRENT_DATE AND killpoints > 0',
                [':player_id'=>$target_player_obj->id()]
            );

            $rank_spot = query_item(
                'SELECT rank_id FROM rankings WHERE player_id = :player_id limit 1',
                [':player_id'=>$target_player_obj->id()]
            );

            if ($viewing_player_obj !== null) {
                $viewers_clan   = Clan::findByMember($viewing_player_obj);
                $self           = ($viewing_player_obj->id() === $target_player_obj->id());
                $params         = ['required_turns'=>0, 'ignores_stealth'=>true];
                $AttackLegal    = new AttackLegal($viewing_player_obj, $target_player_obj, $params);
                $AttackLegal->check(false);
                $i_am_dead = $AttackLegal->iAmDead();
                $attack_error   = $AttackLegal->getError();

                if (!$attack_error && !$self) { // They're not dead or otherwise unattackable.
                    // Pull the items and some necessary data about them.
                    $inventory = new Inventory($viewing_player_obj);
                    $items     = $inventory->counts();

                    $skillDAO = new SkillDAO();

                    if (!$viewing_player_obj->isAdmin()) {
                        $combat_skills   = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->_class_id, 'combat', $viewing_player_obj->level);
                        $targeted_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->_class_id, 'targeted', $viewing_player_obj->level);
                    } else {
                        $combat_skills   = $skillDAO->all('combat');
                        $targeted_skills = $skillDAO->all('targeted');
                    }
                }

                if ($clan && $viewers_clan) {
                    $same_clan            = ($clan->id == $viewers_clan->id);
                    $display_clan_options = (!$self && $same_clan && $viewing_player_obj->isClanLeader());
                }
            }

            $parts = [
                'viewing_player_obj'   => $viewing_player_obj,
                'target_player_obj'    => $target_player_obj,
                'combat_skills'        => $combat_skills,
                'targeted_skills'      => $targeted_skills,
                'self'                 => $self,
                'rank_spot'            => $rank_spot,
                'kills_today'          => $kills_today,
                'status_list'          => Player::getStatusList($target_player_obj->id()),
                'clan'                 => $clan,
                'items'                => $items,
                'account'              => Account::findByChar($target_player_obj),
                'same_clan'            => $same_clan,
                'display_clan_options' => $display_clan_options,
                'i_am_dead'            => $i_am_dead,
                'attack_error'         => $attack_error,
            ];
        }

        $parts['authenticated'] = SessionFactory::getSession()->get('authenticated', false);

        $title = 'Ninja'.($viewed_name_for_title ? ": $viewed_name_for_title" : ' Profile');

        return new StreamedViewResponse($title, $template, $parts, [ 'quickstat' => 'player', ]);
    }

    /**
     * Wrapper to redirect item use via html form to proper pretty url
     * like a final url of /item/use/shuriken/tchalvak
     * from a starting url of http://nw.local/player/use_item/?item=shuriken&target=tchalvak
     */
    public function use_item() {
        $request = RequestWrapper::$request;
        $target = $request->get('target_id');
        $item_in = $request->get('item');
        $give = $request->get('give');
        $method = $give? 'give' : 'use';
        $url = 'item/'.rawurlencode($method).'/'.rawurlencode($item_in).'/'.rawurlencode($target);
        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }

    /**
     * Wrapper to redirect skill use into pretty urls
     * like a final url of /skill/use/firebolt/tchalvak
     * from a starting url of http://nw.local/player/use_skill/?act=firebolt&target=tchalvak
     */
    public function use_skill() {
        $request = RequestWrapper::$request;
        $target = $request->get('target');
        $act = $request->get('act');
        $url = 'skill/use/'.rawurlencode($act).'/'.rawurlencode($target);
        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }
}
