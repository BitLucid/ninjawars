<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Player;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\extensions\SessionFactory;

/**
 * The controller for effects of a work request and the default index display
 * of the page and initial form
 */
class WorkController extends AbstractController {
    const ALIVE = true;
    const PRIV  = false;

    const WORK_MULTIPLIER             = 30;
    const DEFAULT_RECOMMENDED_TO_WORK = 10;

    /**
     * Take in a url parameter of work and try to convert it to gold
     */
    public function requestWork() {
        $earned = 0;
        $worked = Filter::toNonNegativeInt(in('worked')); // No negative work.
        $char   = Player::find(SessionFactory::getSession()->get('player_id'));

        if (!($char instanceof Player)){
            return new RedirectResponse('/work');
        }

        $sufficient_turns = ($worked <= $char->turns);

        if ($sufficient_turns) {
            $earned = $worked * self::WORK_MULTIPLIER; // calc amount worked
            $char->set_gold($char->gold + $earned);
            $char->set_turns($char->turns - $worked);
            $char->save();
        }

        $parts = [
            'recommended_to_work' => $worked,
            'worked'              => $worked,
            'work_multiplier'     => self::WORK_MULTIPLIER,
            'authenticated'       => SessionFactory::getSession()->get('authenticated', false),
            'gold_display'        => number_format($char->gold),
            'earned_gold'         => number_format($earned),
            'not_enough_energy'   => !$sufficient_turns,
        ];

        return $this->render($parts);
    }

    /**
     * Get the last turns worked by a pc, and pass it to display the default
     * page with form
     */
    public function index() {
        $char = Player::find(SessionFactory::getSession()->get('player_id'));

        if (!$char) {
            $char = new Player();
        }

        $parts = [
            'recommended_to_work' => self::DEFAULT_RECOMMENDED_TO_WORK,
            'work_multiplier'     => self::WORK_MULTIPLIER,
            'authenticated'       => SessionFactory::getSession()->get('authenticated', false),
            'gold_display'        => number_format($char->gold),
            'earned_gold'         => number_format(null),
            'worked'              => null,
            'not_enough_energy'   => null,
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
