{**
 * This is the main template for combat. It holds the error case and includes
 * the main template when there is no error
 *}
<style>
.attack-outcome .padded-area {
	text-align: center;
	margin: 0.3rem 1rem;
}
.attack-outcome .combat-main table {
	margin: 0 auto;
}
</style>

<section id='attack-outcome' class='attack-outcome'>
	<h1>Battle Outcome</h1>

    <nav class='player-ranking-linkback'>
      {if $rank_spot}
	  <a href='/list?searched={'#'|cat:$rank_spot|escape:'url'|escape}&amp;hide=none' title='&lsaquo;Return to rank {$rank_spot}' >
        <i class="fas fa-chevron-circle-left"></i> Ninja List
      </a>
	  {else}
		<a href='/list?hide=dead' title='&lsaquo;Return to ninja list' >
			<i class="fas fa-chevron-circle-left"></i> Ninja List
		</a>
	  {/if}
    </nav>

	<div class='padded-area'>
        {if $target}
		<div class='avatar-area'>
			<a href="/player?player_id={$target->id()|escape:'url'}">{include file="gravatar.tpl" gurl=$target->avatarUrl()}</a>
		</div>
        {/if}

		{if $error}
		<div class='ninja-error centered'>
			{$error}
		</div>
		{else}
        {include file="combat.main.tpl"}
		{/if}
	</div><!-- End of inset-area -->
	<nav class='attack-nav'>
		{if $target->id()}
			{if $target->health gt 0 && $attacker->health gt 0}
				<a href="/attack?attacked=1&amp;target={$target->id()|escape:'url'}" class='attack-again thick btn btn-primary'>Attack Again?</a>
			{/if}
				<a class='return-to-location' href='/player?player_id={$target->id()|escape:'url'}'>
					<span class='fa fa-eye'></span> Look at <span class='char-name'>{$target->name()|escape}</span>
				</a>
		{/if}
		<a href='/enemies' class='return-to-location'>Return to the Fight</a>
	</nav>
</section>
