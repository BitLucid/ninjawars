<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Player;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The controller for effects of a work request and the default index display
 * of the page and initial form
 */
class WorkController {
    const ALIVE = true;
    const PRIV  = false;

    const WORK_MULTIPLIER             = 30;
    const DEFAULT_RECOMMENDED_TO_WORK = 10;

    /**
     * Take in a url parameter of work and try to convert it to gold
     */
    public function requestWork() {
        $worked                 = positive_int(in('worked')); // No negative work.
        $char_id                = self_char_id();
        $char                   = Player::find($char_id);
        if(!($char instanceof Player)){
            return new RedirectResponse('/work');
        }
        $work_multiplier        = self::WORK_MULTIPLIER;
        $earned_gold            = null;
        $not_enough_energy      = null;
        $recommended_to_work    = $worked;
        $is_logged_in           = is_logged_in();
        $turns                  = $char->turns;
        $gold                   = $char->gold;

        if ($worked > $turns) {
            $not_enough_energy = true;
        } else {
            $earned_gold  = $worked * $work_multiplier; // calc amount worked
            $char->set_gold($gold+$earned_gold);
            $char->set_turns($turns-$worked);
            $char->save();
        }

        $gold_display = number_format($char->gold);

        $parts = [
            'recommended_to_work'    => $recommended_to_work,
            'work_multiplier'        => $work_multiplier,
            'is_logged_in'           => $is_logged_in,
            'gold_display'           => $gold_display,
            'worked'                 => $worked,
            'earned_gold'            => number_format($earned_gold),
            'not_enough_energy'      => $not_enough_energy,
        ];

        return $this->render($parts);
    }

    /**
     * Get the last turns worked by a pc, and pass it to display the default
     * page with form
     */
    public function index() {
        // Initialize variables to pass to the template.
        $work_multiplier        = self::WORK_MULTIPLIER;
        $worked                 = null;
        $earned_gold               = null;
        $not_enough_energy      = null;
        $is_logged_in           = is_logged_in();
        $char                   = new Player(self_char_id());

        // Fill out some of the variables.
        $recommended_to_work = self::DEFAULT_RECOMMENDED_TO_WORK;
        $gold_display = number_format($char->gold);

        $parts = [
            'recommended_to_work'    => $recommended_to_work,
            'work_multiplier'        => $work_multiplier,
            'is_logged_in'           => $is_logged_in,
            'gold_display'           => $gold_display,
            'worked'                 => $worked,
            'earned_gold'            => number_format($earned_gold),
            'not_enough_energy'      => $not_enough_energy,
        ];

        return $this->render($parts);
    }

    private function render($parts) {
        return [
            'template'  => 'work.tpl',
            'title'     => 'Working in the Village',
            'parts'     => $parts,
            'options'   => [
                'quickstat' => 'player',
            ],
        ];
    }
}
