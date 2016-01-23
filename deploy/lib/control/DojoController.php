<?php
namespace NinjaWars\core\control;

use \Player;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles user actions in the dojo
 *
 * @note IMPORTANT MAINTENANCE NOTES
 * To disable class change code: set $classChangeAllowed to boolean false
 * To change order of class change cycling: Update $class_array, key = starting
 * class, value = next class in cycle
 */
class DojoController {
    const ALIVE                = false;
    const PRIV                 = false;
    const DIM_MAK_COST         = 70; // Cost of acquiring DimMak In turns
    const DIM_MAK_STRENGTH_MIN = 50; // Must have enough strength to get DimMak
    const CLASS_CHANGE_COST    = 50; // Cost of class change in turns

    /**
     * Default dojo action
     *
     * @return ViewSpec
     */
    public function index() {
        if (is_logged_in()) {
            return $this->render([], new Player(self_char_id()));
        } else {
            return $this->render();
        }
    }

    /**
     *
     * @return ViewSpec
     */
    public function buyDimMak() {
        if (is_logged_in()) {
            $player = new Player(self_char_id());
            $showMonks = false;
            $parts = [];


            if (Request::createFromGlobals()->isMethod('POST')) {
                $error = $this->dim_mak_reqs($player, self::DIM_MAK_COST, self::DIM_MAK_STRENGTH_MIN);

                if (!$error) {
                    $player->changeTurns((-1)*self::DIM_MAK_COST);
                    add_item($player->id(), 'dimmak', 1);
                    $parts['pageParts'] = ['success-dim-mak'];
                    $showMonks = true;
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
     *
     * @return ViewSpec
     */
    public function changeClass() {
        if (is_logged_in()) {
            $player            = new Player(self_char_id());
            $classes           = $this->classes_info();
            $requestedIdentity = in('requested_identity');
            $currentClass      = $player->class_identity();
            $showMonks         = false;
            $parts             = [];

            if (isset($classes[$requestedIdentity])) {
                $error = $this->class_change_reqs($player, self::CLASS_CHANGE_COST);

                if ($currentClass != $requestedIdentity && !$error) {
                    $error = $this->changePlayerClass($player, $requestedIdentity);
                }

                $currentClass = $player->class_identity();
                $currentClassName = $player->class_display_name();

                if (!$error) {
                    $pageParts = [
                        'success-class-change',
                    ];

                    $showMonks = true;
                } else {
                    $parts['error'] = $error;
                }
            } else {
                $pageParts = [
                    'form-class-change',
                ];
            }

            unset($classes[$currentClass]);

            $parts['pageParts']    = $pageParts;
            $parts['classOptions'] = $classes;

            return $this->render($parts, $player, $showMonks);
        } else {
            return $this->accessDenied();
        }
    }

    /**
     * Returns an error if there's an obstacle to changing classes.
     *
     * @return string|null
     */
    private function class_change_reqs($char_obj, $turn_req) {
        $error = null;

        if ($char_obj->turns() < $turn_req) {
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
    private function changePlayerClass($p_player, $p_class) {
        $class_change_error = set_class($p_player->id(), $p_class);

        if (!$class_change_error) {
            $p_player->changeTurns((-1)*self::CLASS_CHANGE_COST);
        }

        return $class_change_error;
    }

    /**
     * Returns an error if the requirements for getting a dim mak aren't met.
     *
     * @return string|null
     */
    private function dim_mak_reqs($char_obj, $turn_req, $str_req) {
        $error = null;

        if ($char_obj->turns() < $turn_req) {
            $error = "You don't have enough turns to get a Dim Mak.";
        }

        if ($char_obj->strength() < $str_req) {
            $error = "You don't have enough strength to get a Dim Mak.";
        }

        return $error;
    }

    /**
     * Pull the information about the classes.
     *
     * @return array
     */
    private function classes_info() {
        $classes = query('select class_id, identity, class_name, class_note, class_tier, class_desc, class_icon, theme from class where class_active = true');
        return array_identity_associate($classes, 'identity');
    }

    /**
     */
    private function accessDenied() {
        $this->render();
    }

    /**
     * Render
     *
     * @return ViewSpec
     */
    private function render($p_parts = [], $p_player = null, $p_renderMonks = true) {
        $p_parts['max_level']         = maximum_level(); // For non-logged in loop through stats.
        $p_parts['max_hp']            = max_health_by_level(maximum_level()+1);
        $p_parts['class_change_cost'] = self::CLASS_CHANGE_COST;
        $p_parts['player']            = $p_player;
        $p_parts['classes']           = $this->classes_info();

        if (!isset($p_parts['pageParts'])) {
            $p_parts['pageParts'] = [];
        }

        if (!$p_player) {
            array_unshift($p_parts['pageParts'], 'access-denied');
        } else {
            if ($p_renderMonks) {
                if (!$this->dim_mak_reqs($p_player, self::DIM_MAK_COST, self::DIM_MAK_STRENGTH_MIN)) {
                    $p_parts['pageParts'][] = 'reminder-dim-mak';
                }

                if (!$this->class_change_reqs($p_player, self::CLASS_CHANGE_COST)) {
                    $p_parts['pageParts'][] = 'reminder-class-change';
                }
            }

            $p_parts['pageParts'][] = 'reminder-class';
            $p_parts['pageParts'][] = 'reminder-level';

            if ($p_player->level() < maximum_level()) {
                $p_parts['required_kills'] = required_kills_to_level($p_player->level());
                $p_parts['pageParts'][] = 'reminder-next-level';
            }
        }

        $p_parts['pageParts'][] = 'scroll';

        if (!isset($p_parts['error'])) {
            $p_parts['error'] = null;
        }

        return [
            'template' => 'dojo.tpl',
            'title'    => 'Dojo',
            'parts'    => $p_parts,
            'options'  => ['quickstat'=>'player'],
        ];
    }
}
