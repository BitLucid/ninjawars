<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles user actions in the dojo
 *
 * @note IMPORTANT MAINTENANCE NOTES
 * To disable class change code: set $classChangeAllowed to boolean false
 */
class DojoController extends AbstractController {
    const ALIVE                = false;
    const PRIV                 = false;
    const DIM_MAK_COST         = 70; // Cost of acquiring DimMak In turns
    const CLASS_CHANGE_COST    = 50; // Cost of class change in turns

    /**
     * Default dojo action
     *
     * @param Container
     * @return Response
     */
    public function index(Container $p_dependencies) {
        if ($p_dependencies['session']->get('authenticated', false)) {
            return $this->render([], $p_dependencies['current_player']);
        } else {
            return $this->render();
        }
    }

    /**
     * Action to request the Dim Mak form AND execute the purchase
     *
     * @todo split form request (GET) and purchase (POST) into separate funcs
     * @param Container
     * @return Response
     */
    public function buyDimMak(Container $p_dependencies) {
        if ($p_dependencies['session']->get('authenticated', false)) {
            $player = $p_dependencies['current_player'];
            $showMonks = false;
            $parts = [];

            if (RequestWrapper::$request && RequestWrapper::$request->isMethod('POST')) {
                $error = $this->dimMakReqs($player, self::DIM_MAK_COST);

                if (!$error) {
                    $player->changeTurns((-1)*self::DIM_MAK_COST);
                    $inventory = new Inventory($player);
                    $inventory->add('dimmak', 1);
                    $parts['pageParts'] = ['success-dim-mak'];
                    $showMonks = true;
                    $player->save();
                } else {
                    $parts['error'] = $error;
                }
            } else {
                $parts['pageParts'] = ['form-dim-mak'];
                $parts['dim_mak_cost'] = self::DIM_MAK_COST;
            }

            return $this->render($parts, $player, $showMonks);
        } else {
            return $this->accessDenied();
        }
    }

    /**
     * Get the class and identity data
     *
     */
    private function getClasses(){
        $classes_raw = query_array('select 
                class_id, 
                identity, 
                class_name, 
                class_note, 
                class_tier, 
                class_desc, 
                class_icon, 
                theme 
                from class where class_active = true');
        $classes = [];
        foreach($classes_raw as $class){
            $classes[$class['identity']] = $class;
        }
        unset($class);
        return $classes;
    }

    /**
     * Action to request class change form AND execute class change
     *
     * @todo split form request and execute into separate funcs
     * @param Container
     * @return Response
     */
    public function changeClass(Container $p_dependencies) {
        if ($p_dependencies['session']->get('authenticated', false)) {
            $player            = $p_dependencies['current_player'];
            $classes           = $this->getClasses();
            $requested_identity = RequestWrapper::getPostOrGet('requested_identity');
            $currentClass      = $player->identity;
            $showMonks         = false;
            $parts             = [];

            if ($requested_identity && isset($classes[$requested_identity])) {
                $error = $this->classChangeReqs($player, self::CLASS_CHANGE_COST);
                if ($currentClass != $requested_identity && !$error) {
                    $error = $this->changePlayerClass($player, $requested_identity, self::CLASS_CHANGE_COST);
                }
                $currentClass = $player->identity;
                if (!$error) {
                    $parts['pageParts'] = ['success-class-change'];
                    $showMonks = true;
                } else {
                    $parts['error'] = $error;
                }
            } else {
                $parts['pageParts'] = ['form-class-change'];
            }
            unset($classes[$currentClass]);
            $parts['classOptions'] = $classes;
            return $this->render($parts, $player, $showMonks);
        } else {
            return $this->accessDenied();
        }
    }

    /**
     * Returns an error if there's an obstacle to changing classes.
     *
     * @param Player $p_player
     * @param int $p_requiredTurns
     * @return string
     */
    private function classChangeReqs($p_player, $p_requiredTurns) {
        $error = '';

        if ($p_player->turns < $p_requiredTurns) {
            // Check the turns, return the error if it's too below.
            $error = "You don't have enough turns to change your class.";
        }

        return $error;
    }

    /**
     * Subtract the cost in turns and change the class
     *
     * @return string
     */
    private function changePlayerClass($p_player, $p_class, $cost) {
        $error = $p_player->setClass($p_class);

        if (!$error) {
            $p_player->changeTurns((-1)*$cost);
            $p_player->save();
        }

        return $error;
    }

    /**
     * Returns an error if the requirements for getting a dim mak aren't met.
     *
     * @param Player $p_player
     * @param int $p_requiredTurns
     * @return string
     */
    private function dimMakReqs(Player $p_player, $p_requiredTurns) {
        $error = '';

        if ($p_player->turns < $p_requiredTurns) {
            $error = "You don't have enough turns to get a Dim Mak.";
        }

        return $error;
    }

    /**
     * Multiple actions currently check logged in status and deny access
     *
     * @todo remove this by abstracting login checks throughout this controller
     * @return Response
     */
    private function accessDenied() {
        return $this->render();
    }

    /**
     * Create the Response to render
     *
     * @param Array $p_parts Array that gets bound to view
     * @param Player $p_player The player requesting the action
     * @param boolean $p_renderMonks Flag to render links to actions
     * @return Response
     */
    private function render($p_parts = [], $p_player = null, $p_renderMonks = true) {
        $p_parts['max_level']         = MAX_PLAYER_LEVEL; // For non-logged in loop through stats.
        $p_parts['max_hp']            = Player::maxHealthByLevel(MAX_PLAYER_LEVEL+1);
        $p_parts['class_change_cost'] = self::CLASS_CHANGE_COST;
        $p_parts['player']            = $p_player;

        if (!isset($p_parts['pageParts'])) {
            $p_parts['pageParts'] = [];
        }

        if (!$p_player) {
            array_unshift($p_parts['pageParts'], 'access-denied');
        } else {
            if ($p_renderMonks) {
                if (empty($this->dimMakReqs($p_player, self::DIM_MAK_COST))) {
                    $p_parts['pageParts'][] = 'reminder-dim-mak';
                }

                if (empty($this->classChangeReqs($p_player, self::CLASS_CHANGE_COST))) {
                    $p_parts['pageParts'][] = 'reminder-class-change';
                }
            }

            $p_parts['pageParts'][] = 'reminder-class';
            $p_parts['pageParts'][] = 'reminder-level';

            if ($p_player->level < MAX_PLAYER_LEVEL) {
                $p_parts['required_kills'] = $p_player->killsRequiredForNextLevel();
                $p_parts['pageParts'][] = 'reminder-next-level';
            }
        }

        $p_parts['pageParts'][] = 'scroll';

        if (!isset($p_parts['error'])) {
            $p_parts['error'] = null;
        }

        return new StreamedViewResponse('Dojo', 'dojo.tpl', $p_parts, ['quickstat'=>'player']);
    }
}
