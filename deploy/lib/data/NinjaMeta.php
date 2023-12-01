<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use model\Status;
use PDO;
use RuntimeException;

/**
 * Wrap additional data around the Player class
 * @todo Move clan and status operations here
 */
class NinjaMeta
{
    private $char;

    /**
     * Chainable constructor
     */
    public function __construct(Player $char)
    {
        $this->char = $char;
        return $this;
    }

    /**
     * Get the current ranking of a character
     */
    public function ranking()
    {
        return query_item(
            'SELECT rank_id FROM rankings WHERE player_id = :player_id limit 1',
            [':player_id' => $this->char->id()]
        ) ?? null;
    }

    /**
     * Simplified deactivating of the character, can be easily reverted via login
     */
    public function deactivate()
    {
        $this->char->active = 0;
        $this->char->save();
    }

    /**
     * Turns the ninja back on/active
     */
    public function reactivate()
    {
        $this->char->active = 1;
        $this->char->save();
    }
}
