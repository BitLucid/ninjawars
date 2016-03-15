<?php
/**
 * Turn an account into its first player's id
 * @return int|null
 */
function smarty_modifier_to_playerid($account) {
    if ($account instanceof \model\orm\Accounts) {
        return $account->getPlayerss()->getFirst()->getPlayerId();
    } else {
        return null;
    }
}
