<h1>Quest: {$quest.title}</h1>

{$quest.title} via {$quest.giver}
{$quest.tags}
Description: {$quest.description}
Rewards: {$quest.rewards}

<!--  quest_id, title, player_id, tags, description, rewards, obstacles, expiration, proof -->

<form action='quest_submit'>
</form>
Quests:  
<ol>
<li>Quest via <a target='main' href='player.php?user_id={$quest.player_id}'>{$quest.giver}</a>: {$quest.description} <a target='main' href='quests.php?quest_id={$quest.quest_id}'>View Quest</a></li>
</ol>

