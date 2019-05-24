{*
<script>
var enemy = 'enemy to array @json_encode'
</script>
*}

<section class="attack-next">
	<form action="/attack" method="POST" name="attack-next">
		{* Display the ninja *}
		<div>
			<div class='avatar'>Avatar</span>

			<h2>{$enemy->name()|escape}<h1>
			{include file="health_bar.tpl" health=$enemy->health level=$enemy->level}
		</div>
		{* Display attack with additional settings *}
		<button type="submit" class='btn btn-default'>Duel <em class='char-name'>{$enemy->name()|escape}</em></button>
		{* Item-based attack options *}
		<p>My Name: {$char->name()|escape}
	</form>
</section>