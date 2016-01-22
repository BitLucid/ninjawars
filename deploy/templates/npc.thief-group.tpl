<img src='{cachebust file="/images/scenes/KunitsunaTrainingWithTengu.jpg"}' alt='' style='width:1000px'>
<p>A group of tengu thieves is waiting for you. </p><p class='speech'>You'll pay for attacking our brethren.</p>

{if $victory}
	{if $attack gt 120}
<p>You overpowered the swine, but the blow to the head they gave you before they ran made you lose some of your memories!</p>
	{/if}
<p>The group of theives does {$attack} damage to you, but you rout them in the end!</p>
<p>You have gained {$gold} gold.</p>
<p>You have found a Fire Scroll on the body of one of the thieves!</p>
<a href="/npc/attack/thief/">Attack Another Thief</a>
<br>
{else}
<p>The group of theives does {$attack} damage to you!</p>
<p>The group of thieves have avenged their brotherhood and beaten you to a bloody pulp.</p>
{/if}
