<?php
function smarty_modifier_to_playerid($account) {
    if ($account instanceof \model\orm\Accounts) {
        return $account->getPlayerss()->getFirst()->getPlayerId();
    }
}
