<img src='images/scenes/KunitsunaTrainingWithTengu.jpg' alt='' style='width:1000px'>
<p>A group of tengu thieves is waiting for you. They seem to be angered by your attacks on their brethren.</p>
{if $victory}
	{if $group_attack gt 120}
<p>You overpowered the swine, but the blow to the head they gave you before they ran made you lose some of your memories!</p>
	{/if}
<p>The group of theives does {$group_attack} damage to you, but you rout them in the end!</p>
<p>You have gained {$group_gold} gold.</p>
<p>You have found a Fire Scroll on the body of one of the thieves!</p>
{else}
<p>The group of theives does {$group_attack} damage to you!</p>
<p>The group of thieves have avenged their brotherhood and beaten you to a bloody pulp.</p>
{/if}
