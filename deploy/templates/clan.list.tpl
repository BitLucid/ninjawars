<section id='clan-tags'>
	<h4 id='clan-tags-title'>Clans in the Area</h4>
	<figure>
		<img src='/images/scenes/clanhalls.jpg' 
			alt='Scene of different clanhalls' 
			class='rounded mx-auto d-block'/>
	</figure>
	<ul>
{foreach from=$clans key=clan_id item=clan}
		<li class='clan-tag size{$clan.score}'>
			<a href='/clan/view?clan_id={$clan_id|escape:'url'}'>{$clan.name|escape}</a>
		</li>
{/foreach}
	</ul>
</section>
