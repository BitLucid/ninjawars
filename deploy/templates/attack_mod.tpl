{**
 * This is the main template for combat. It holds the error case and includes
 * the main template when there is no error
 *}
<section id='attack-outcome'>
	<h1>Battle Outcome</h1>

	<div class='padded-area'>
        {if $target}
		<div>
			<a href="/player?player_id={$target->id()|escape:'url'}">{include file="gravatar.tpl" gurl=$target->avatarUrl()}</a>
		</div>
		<hr>
        {/if}

		{if $error}
		<div class='ninja-error centered'>{$error}</div>
		{else}
        {include file="combat.main.tpl"}
		{/if}
	</div><!-- End of inset-area -->
	<nav class='attack-nav'>
		{if $target}
			{if $target->health gt 0 && $attacker->health gt 0}
				<div><a href="/attack?attacked=1&amp;target={$target->id()|escape:'url'}" class='attack-again thick btn btn-primary'>Attack Again?</a></div>
			{/if}
				<div><a href='/player?player_id={$target->id()|escape:'url'}'><< Return to <span class='char-name'>{$target->name()|escape}'s Info</span></a></div>
		{/if}
		<a href='/enemies' class='return-to-location'>Return to the Fight</a>
	</nav>
</section>
