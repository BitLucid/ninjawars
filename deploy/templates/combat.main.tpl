{**
 * This template renders the result of a successful combat request
 *}
{if $stealthed_attack}
<div>You reveal yourself with a surprise strike from the shadows!</div>
{/if}

{if $stealth_damage}
<div>{$target->name()} has lost {$starting_target->health - $target->health} health.</div>
{/if}

{if $stealth_lost}
You have lost your stealth.
{/if}

{if $options.blaze}
Your soul blazes with fire!
{/if}

{if $options.deflect}
You center your body and soul before battle!
{/if}

{if $options.evade}
As you enter battle, you note your potential escape routes...
{/if}

{include file="combat-prebattle-stats.tpl" attacker=$starting_attacker target=$starting_target}

{if $options.blaze}
<div>Your attack is more powerful due to blazing!</div>
{/if}

{if $options.deflect}
<div>Your wounds are reduced by deflecting the attack!</div>
{/if}

{if $options.evade && $target->health gt 0}
<div>Realizing you are out matched, you escape with your life to fight another day!</div>
{/if}

<div>Total Rounds: {$rounds}</div>

{include file="combat-final-results.tpl" 
    starting_attacker=$starting_attacker 
    final_attacker=$attacker 
    starting_target=$starting_target 
    target=$target 
    low_health=($attacker->health lt $attacker->getMaxHealth() / 5)}

{if $options.duel}
<p>You spent an extra turn dueling.</p>
{/if}

{if $options.blaze}
<div>You spent {getTurnCost skillName="blaze"} turns to blaze with power.</div>
{/if}

{if $options.deflect}
<div>You spent {getTurnCost skillName="deflect"} more turn in order to deflect your enemy's blows.</div>
{/if}

{if $options.evade}
<div>You spent {getTurnCost skillName="deflect"} more turn preparing your escape routes.</div>
{/if}

{if $target->health lt 1}
<div>{$attacker->name()} has killed {$target->name()}!</div>
<div class='ninja-notice'>
    {$target->name()} is dead, you have proven your might
    {if $killpoints eq 2}
    twice over
    {elseif $killpoints gt 2}
    {$killpoints} times over
    {/if}
    !
</div>

    {if $loot}
<div>You have taken <span class='gold-count'>石{$loot|number_format:0|escape} gold</span> from {$target->name()}.</div>
    {/if}

    {if $wrath}
<div class='wrath'>Your victory fuels your wrath, allowing you to retain some of your health.</div>
    {/if}
{/if}

{if $rewarded_ki}
<div>Your ki lifeforce has increased.</div>
{/if}

{if $bounty_result}
{$bounty_result}
{/if}

{if $target->health gt 0}
{include file="defender_health.tpl" health=$target->health level=$target->level target_name=$target->name()}
{/if}

{if $attacker->health lt 1}
<div class='parent died'>
    <div class='child ninja-error thick'>
        {$target->name()} has killed you!
    </div>
</div>

    {if $loot}
<div class='parent'>
    <div class='child ninja-notice'>
        {$target->name()} has <strong>taken</strong> <span class='gold-count'>{$loot|number_format:0|escape} gold</span> from you!
    </div>
</div>
    {/if}
<div class='ninja-notice thick'>
    Go to the <a href="/shrine">Shrine</a> to return to the living.
</div>
{/if}
