<link href="/css/npcs.css" rel="stylesheet" type="text/css" />
<div class='encounter-overall'>
	<figure>
		<img class='npc-image' src='{cachebust file="/images/scenes/KunitsunaTrainingWithTengu.jpg"}' 
		alt='' style='max-width:100%'>
	</figure>
	<section class='encounter-container'>
		<div class='encounter-section'>
			<p>A group of tengu thieves is waiting for you. </p>
			<p class='speech'>You'll pay for attacking our brethren.</p>

			{if $victory}
				<p><em>The group of theives does <span class='damage danger-text'>{$attack} damage</span> to you, but you rout them in the end!</em></p>
				{if $powerful_attack}
					<p>You outclassed the thieves, but a parting blow to your head from one of their clubs cast a mysterious shadow over your memories!</p>
				{/if}
			<div class='reward-container'>
				<p class='reward-text'>You have gained <span class='gold'>{$gold} gold</span>.</p>
				<p class='reward-text'>You have found <span class='obtained-item'>Phosphor Powder</span> on the body of one of the thieves!</p>
				<div class='chest-icon'></div>
			</div>
			<nav><a href="/npc/attack/thief/">Attack Another Thief</a></nav>
			<br>
			{else}
			<p>The group of theives does {$attack} damage to you!</p>
			<p><em>The group of thieves have avenged their brotherhood and beaten you to a bloody pulp.</em></p>
			{/if}
		</div>
	</section>
</div>
