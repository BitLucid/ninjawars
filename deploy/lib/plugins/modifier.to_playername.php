<?php
/**
 * Turn an ORM Account into it's first player name
 * @return string|null
 */
function smarty_modifier_to_playername($account) {
    if ($account instanceof \model\orm\Accounts) {
        return $account->getPlayerss()->getFirst()->getUname();
    } else {
        return null;
    }
}
