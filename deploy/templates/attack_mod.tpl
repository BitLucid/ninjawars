<section id='attack-outcome'>
	<h1>Battle Outcome</h1>

	<div class='padded-area'>
        {if $target}
		<div>
			<a href="/player?player_id={$target->id()|escape:'url'}">{include file="gravatar.tpl" gurl=$target->avatarUrl()}</a>
		</div>
		<hr>
        {/if}

		{if $attack_error}
		<div class='ninja-error centered'>{$attack_error}</div>
		{else}

			{if $stealthed_attack}
				<div>You reveal yourself with a surprise strike from the shadows!</div>
			{/if}

			{if $stealth_damage}
				<div>{$target->name()} has lost {$starting_target->health - $target->health} health.</div>
			{/if}

			{if $stealth_lost}
	            You have lost your stealth.
	        {/if}

			{if $blaze}
				Your soul blazes with fire!
			{/if}

			{if $deflect}
				You center your body and soul before battle!
			{/if}

			{if $evade}
				As you enter battle, you note your potential escape routes...
			{/if}

			{include file="combat-prebattle-stats.tpl" attacker=$starting_attacker target=$starting_target}

			{if $blaze}
				<div>Your attack is more powerful due to blazing!</div>
			{/if}

			{if $deflect}
				<div>Your wounds are reduced by deflecting the attack!</div>
			{/if}

			{if $evade && $target->health gt 0}
				<div>Realizing you are out matched, you escape with your life to fight another day!</div>
			{/if}

			{if $rounds}
				<div>Total Rounds: {$rounds}</div>
			{/if}

            {include file="combat-final-results.tpl" starting_attacker=$starting_attacker final_attacker=$attacker starting_target=$starting_target target=$target}

			{if $duel}
				<p>You spent an extra turn dueling.</p>
			{/if}

			{if $blaze}
				<div>You spent two extra turns to blaze with power.</div>
			{/if}

			{if $deflect}
				<div>You spent three extra turns in order to deflect your enemy's blows.</div>
			{/if}

			{if $evade}
				<div>You spent two extra turns preparing your escape routes.</div>
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

				!</div>

				{if $loot}
					<div>You have taken <span class='gold-count'>{$loot} gold</span> from {$target->name()}.</div>
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
				<div class='child ninja-error thick'>{$target->name()} has killed you!</div>
			</div>

                {if $loot}
				<div>{$target->name()} has taken {$loot} gold from you.</div>
                {/if}
		<div class='ninja-notice thick'>
			Go to the <a href="/shrine">Shrine</a> to return to the living.
		</div>
            {/if}
		{/if}{* End of no attack error section *}
	</div><!-- End of inset-area -->
	<nav class='attack-nav'>
		{if $target}
			{if $attack_again}
				<div><a href="/attack?attacked=1&amp;target={$target->id()|escape:'url'}" class='attack-again thick btn btn-primary'>Attack Again?</a></div>
			{/if}
				<div><a href='/player?player_id={$target->id()|escape:'url'}'><< Return to <span class='char-name'>{$target->name()|escape}'s Info</span></a></div>
		{/if}
		<a href='/enemies' class='return-to-location'>Return to the Fight</a>
	</nav>
</section>
