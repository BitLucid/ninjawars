<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Player;
use NinjaWars\core\Filter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;

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
     *
     * @param Container
     * @return StreamedViewResponse
     */
    public function requestWork(Container $p_dependencies) {
        $earned = 0;
        $worked = Filter::toNonNegativeInt(RequestWrapper::getPostOrGet('worked')); // No negative work.
        $char   = $p_dependencies['current_player'];

        if (!$char) {
            return new RedirectResponse('/work');
        }

        $sufficient_turns = ($worked <= $char->turns);

        if ($sufficient_turns) {
            $earned = $worked * self::WORK_MULTIPLIER; // calc amount worked
            $char->setGold($char->gold + $earned);
            $char->setTurns($char->turns - $worked);
            $char->save();
        }

        $parts = [
            'recommended_to_work' => $worked,
            'worked'              => $worked,
            'work_multiplier'     => self::WORK_MULTIPLIER,
            'authenticated'       => $p_dependencies['session']->get('authenticated', false),
            'gold_display'        => number_format($char->gold),
            'earned_gold'         => number_format($earned),
            'not_enough_energy'   => !$sufficient_turns,
        ];

        return $this->render($parts);
    }

    /**
     * Get the last turns worked by a pc, and pass it to display the default page with form
     *
     * @param Container
     * @return StreamedViewResponse
     */
    public function index(Container $p_dependencies) {
        $char = $p_dependencies['current_player'];

        if (!$char) {
            $char = new Player();
        }

        $parts = [
            'recommended_to_work' => self::DEFAULT_RECOMMENDED_TO_WORK,
            'work_multiplier'     => self::WORK_MULTIPLIER,
            'authenticated'       => $p_dependencies['session']->get('authenticated', false),
            'gold_display'        => number_format($char->gold),
            'earned_gold'         => number_format(null),
            'worked'              => null,
            'not_enough_energy'   => null,
        ];

        return $this->render($parts);
    }

    /**
     * @return StreamedViewResponse
     */
    private function render($parts) {
        return new StreamedViewResponse('Working in the Village', 'work.tpl', $parts, ['quickstat' => 'player']);
    }
}
