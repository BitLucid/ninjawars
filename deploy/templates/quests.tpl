<h1>Quests</h1>

<a id='create-a-quest'>Create a quest</a><!-- Clicking shows the hidden quest creation form. -->
<form action='quest_submit'>
</form>

<form id="quest-submit" action="quests.php" method="post">
    <input name='quest-name' type='text' value='quest name'>
    <input name='quest-tags' type='text' value='tags'>
    <input name='quest-description' type='textarea' value=''>
    <textarea name='quest-description'></textarea>
    <input name='quest-rewards' type='textarea' value=''>
    <button type='submit'>Create a quest</button>
</form>

Quests:  
<ol>
{foreach from=$quests item=$a_quest}
<li>Quest from <a target='main' href='player.php?user_id={$user_id}'>Username Here</a>: Tagline description here <a target='main' href='quests.php?quest_id={$quest_id}'>View Quest</a></li>
{/foreach}
</ol>

