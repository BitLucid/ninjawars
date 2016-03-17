<section id='attack-outcome'>
	<h1>Battle Outcome</h1>

	<div class='padded-area'>
		<div>
			<a href="/player?player_id={$target_id|escape:'url'}">{include file="gravatar.tpl" gurl=$target_player->avatarUrl()}</a>
		</div>

		<hr>

		{if $attack_error}
		<div class='ninja-error centered'>{$attack_error}</div>
		{else}

			{if $stealthed_attack}
				<div>You reveal yourself with a a surprise strike from the shadows!</div>
			{/if}

			{if $stealth_damage}
				<div>{$target} has lost {$stealthAttackDamage} health.</div>
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

			{if $pre_battle_stats}
				{include file="combat-prebattle-stats.tpl" attacker_name=$pbs_attacker_name attacker_str=$pbs_attacker_str attacker_hp=$pbs_attacker_hp target_name=$pbs_target_name target_str=$pbs_target_str target_hp=$pbs_target_hp}
			{/if}

			{if $blaze}
				<div>Your attack is more powerful due to blazing!</div>
			{/if}

			{if $deflect}
				<div>Your wounds are reduced by deflecting the attack!</div>
			{/if}

			{if $evade && $total_attacker_damage < $target_health}
				<div>Realizing you are out matched, you escape with your life to fight another day!</div>
			{/if}


			{if $rounds}
				<div>Total Rounds: {$rounds}</div>
			{/if}

			{if $combat_final_results}
				{include file="combat-final-results.tpl"}
			{/if}

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




			{if $killed_target}
				<div>{$attacker} has killed {$target}!</div>
				<div class='ninja-notice'>
					{$target} is dead, you have proven your might
				{if $killpoints == 2}
					twice over
				{elseif $killpoints > 2}
					{$killpoints} times over
				{/if}

				!</div>

				{if !$simultaneousKill && $loot}
					<div>You have taken <span class='gold-count'>{$loot} gold</span> from {$target}.</div>
				{/if}

				{if $wrath_regain}
					<div class='wrath'>Your victory fuels your wrath, allowing you to retain some of your health.</div>
				{/if}

			{/if}

			{if $rewarded_ki}
				<div>Your ki lifeforce has increased.</div>
			{/if}

			{if $bounty_result}
				{$bounty_result}
			{/if}


		{/if}{* End of no attack error section *}

		{if $target_ending_health}

			{include file="defender_health.tpl" health=$target_ending_health level=$target_player->level target_name=$target_name}

		{/if}
		{if $attacker_died}
			<div class='ninja-error thick'>{$target} has killed you!</div>

			{if !$simultaneousKill && $loot}
				<div>{$target} has taken {$loot} gold from you.</div>
			{/if}
		<div class='ninja-notice thick'>
			Go to the <a href="/shrine">Shrine</a> to return to the living.
		</div>
		{/if}
	</div><!-- End of inset-area -->
	<nav class='attack-nav'>
		{if $target}
			{if $attack_again}
				<div><a href="/attack?attacked=1&amp;target={$target|escape:'url'}" class='attack-again thick btn btn-primary'>Attack Again?</a></div>
			{/if}
				<div><a href='/player?player={$target|escape:'url'}'><< Return to <span class='char-name'>{$target|escape}'s Info</span></a></div>
		{/if}
		<a href='/enemies' class='return-to-location'>Return to the Fight</a>
	</nav>
</section>
