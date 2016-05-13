<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Deity;
use NinjaWars\core\data\GameLog;

/**
 * Control the game ticks.
 */
class TickController{

    const LOG_CHANCE_DIVISOR = 10;

    private $logger;
    private $deity; 

    public function __construct(GameLog $logger, $deity){
        $this->logger = $logger;
        $this->deity = $deity;
    }

    /**
     * Smallest atomic tick
     */
    public function atomic(){
        $this->deity->rerank();
        $this->deity->increaseKi();
    }

    /**
     * Almost the smallest tick
     */
    public function tiny(){
        $deity = $this->deity;
        $this->deity->regenCharacters($deity::DEFAULT_REGEN);

        // Revive one page of characters at least
        $params = [
            'minor_revive_to'      => 20,
            'major_revive_percent' => 0,
        ];
        list($revived, $dead_count) = $this->deity->revivePlayers($params);

        // Only log on the short interval once in a while.
        if (DEBUG || rand(1, self::LOG_CHANCE_DIVISOR) === 1) {
            $log = "DEITY_TINY STARTING: ".date(DATE_RFC1036)."\n
            PCs revived: ".$revived." \n
            PCs who are/were dead: ".$dead_count." \n";
            $this->logger->log($log);
        }
    }

    /**
     * half-hour tick
     */
    public function minor(){
        $this->logger->log("DEITY_MINOR STARTING: ".date(DATE_RFC1036)."\n");

        $params = [
            'minor_revive_to'      => MINOR_REVIVE_THRESHOLD,
            'major_revive_percent' => 0,
        ];
        list($revived, $dead_count) = $this->deity->revivePlayers($params);

        $this->deity->computeTurns();
        $this->deity->computeActivePCs();
        $this->deity->fixBounties();

        $this->logger->log('Reviving: ['.$revived.'] / ['.$dead_count.'] 
            revived characters / dead characters.;');

        $this->logger->log("DEITY_MINOR ENDING: ".date(DATE_RFC1036)."\n");
    }

    /**
     * Major/hourly tick
     */
    public function major(){
        $this->logger->log("DEITY_MAJOR STARTING: ".date(DATE_RFC1036)."\n");

        $this->deity->computeTime();
        $this->deity->computeTurns();
        $deity = $this->deity;  // Doesn't work without temporary variable, unfortunately
        $this->deity->regenCharacters($deity::DEFAULT_REGEN);
        $this->deity->computeStatuses();

        $params = [
            'minor_revive_to'      => 0,
            'major_revive_percent' => MAJOR_REVIVE_PERCENT,
        ];
        list($revived, $dead_count) = $this->deity->revivePlayers($params);

        $this->logger->log('Reviving: ['.$revived.'] / ['.$dead_count.'] 
            revived characters / dead characters.;');

        $this->logger->log("DEITY_MAJOR ENDING: ".date(DATE_RFC1036)."\n");
    }

    /**
     * Rare Daily/Nightly tick
     */
    public function nightly(){
        $this->logger->log("DEITY_NIGHTLY STARTING: ---- ".date(DATE_RFC1036)." ----\n
            DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".\n");

        $unconfirmed_message = $this->deity->processUnconfirms();
        $this->deity->rearrangeStats();
        $this->deity->truncateMessages();
        $this->deity->pcsUpdate();

        $this->logger->log('DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed_message.").  30 is the current default maximum.\n
            DEITY_NIGHTLY ENDING: ---- ".date(DATE_RFC1036)." ---- \n");
    }
}