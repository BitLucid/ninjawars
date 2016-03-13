<?php
function smarty_modifier_to_playername($account) {
    if ($account instanceof \model\orm\Accounts) {
        return $account->getPlayerss()->getFirst()->getUname();
    }
}
