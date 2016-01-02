<?php
namespace NinjaWars\core\control;

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
        // Initialize variables to pass to the template.
        $work_multiplier        = self::WORK_MULTIPLIER;
        $worked                 = intval(in('worked'));
        $new_gold               = null;
        $not_enough_energy      = null;
        $use_second_description = null;
        $recommended_to_work    = $worked;
        $is_logged_in           = is_logged_in();
        $char_id                = self_char_id();
        $turns                  = get_turns($char_id);

        set_setting('turns_worked', $worked);

        if ($worked > $turns) {
            $not_enough_energy = true;
        } else {
            $new_gold  = $worked * $work_multiplier; // calc amount worked

            add_gold($char_id, $new_gold);
            subtractTurns($char_id, $worked);

            $use_second_description = true;
        }

        $gold_display = number_format(get_gold($char_id));

        $parts = [
            'recommended_to_work'    => $recommended_to_work,
            'work_multiplier'        => $work_multiplier,
            'is_logged_in'           => $is_logged_in,
            'gold_display'           => $gold_display,
            'worked'                 => $worked,
            'new_gold'               => $new_gold,
            'not_enough_energy'      => $not_enough_energy,
            'use_second_description' => $use_second_description,
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
        $new_gold               = null;
        $not_enough_energy      = null;
        $use_second_description = null;
        $is_logged_in           = is_logged_in();
        $char_id                = self_char_id();

        // Fill out some of the variables.
        $last_worked = get_setting('turns_worked');
        $recommended_to_work = ($last_worked ? $last_worked : self::DEFAULT_RECOMMENDED_TO_WORK);
        $gold_display = number_format(get_gold($char_id));

        $parts = [
            'recommended_to_work'    => $recommended_to_work,
            'work_multiplier'        => $work_multiplier,
            'is_logged_in'           => $is_logged_in,
            'gold_display'           => $gold_display,
            'worked'                 => $worked,
            'new_gold'               => $new_gold,
            'not_enough_energy'      => $not_enough_energy,
            'use_second_description' => $use_second_description,
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
