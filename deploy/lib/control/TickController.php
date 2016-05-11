<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Deity;
use NinjaWars\core\data\GameLog;

/**
 * Control the game ticks.
 */
class TickController{

    /**
     * Smallest atomic tick
     */
    public static function atomic(){
        Deity::rerank();
        Deity::increaseKi();
    }


    /**
     * Almost the smallest tick
     */
    public static function tiny(){
        Deity::regenCharacters(Deity::DEFAULT_REGEN);

        // Revive one page of characters at least
        $params = [
            'minor_revive_to'      => 20,
            'major_revive_percent' => 0,
        ];
        list($revived, $dead_count) = Deity::revivePlayers($params);

        // Only log on the short interval once in a while.
        if (DEBUG || rand(1, DEITY_LOG_CHANCE_DIVISOR) === 1) {
            $log = "DEITY_TINY STARTING: ".date(DATE_RFC1036)."\n
            PCs revived: ".$revived." \n
            PCs who are/were dead: ".$dead_count." \n";
            GameLog::log($log);
        }
    }

    /**
     * half-hour tick
     */
    public static function minor(){
        GameLog::log("DEITY_MINOR STARTING: ".date(DATE_RFC1036)."\n");

        $params = [
            'minor_revive_to'      => MINOR_REVIVE_THRESHOLD,
            'major_revive_percent' => MAJOR_REVIVE_PERCENT,
        ];
        list($revived, $dead_count) = Deity::revivePlayers($params);

        Deity::computeTurns();
        Deity::computeActivePCs();
        Deity::fixBounties();

        GameLog::log("DEITY_MINOR ENDING: ".date(DATE_RFC1036)."\n");
    }

    /**
     * Major/hourly tick
     */
    public static function major(){
        GameLog::log("DEITY_MAJOR STARTING: ".date(DATE_RFC1036)."\n");

        Deity::computeTime();
        Deity::computeTurns();
        Deity::regenCharacters(Deity::DEFAULT_REGEN);
        Deity::computeStatuses();

        GameLog::log("DEITY_MAJOR ENDING: ".date(DATE_RFC1036)."\n");
    }

    /**
     * Rare Daily/Nightly tick
     */
    public static function nightly(){
        GameLog::log("DEITY_NIGHTLY STARTING: ---- ".date(DATE_RFC1036)." ----\n
            DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".\n");
        $unconfirmed_message = Deity::processUnconfirms();
        Deity::calculateStats();
        Deity::truncateMessages();
        Deity::pcsUpdate();

        GameLog::log('DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed_message.").  30 is the current default maximum.\n
            DEITY_NIGHTLY ENDING: ---- ".date(DATE_RFC1036)." ---- \n");
    }
}