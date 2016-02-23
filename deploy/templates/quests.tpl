<h1>Quests</h1>

<a id='create-a-quest' class='js-hook'>Create a quest</a>

<form id="quest-create" action="/quest/create" method="post">
    <input name='quest-name' required type='text' value='' placeholder='Quest name'>
    <input name='quest-tags' type='text' value='' placeholder='tags'>
    <textarea name='quest-description' required placeholder='What do you want to hire a ninja to do?'></textarea>
    <input name='quest-rewards' type='textarea' value='' placeholder='What extra rewards will you offer?'>
    <button type='submit'>Create a quest</button>
</form>

<section>
	<h2>Quests:</h2>
	<ol>
		{foreach from=$quests item=$a_quest}
			<li>Quest from <a target='main' href='/player?player_id={$user_id}'>Username Here</a>: Tagline description here <a target='main' href='/quest/view/{$a_quest.quest_id}'>View Quest</a></li>
		{/foreach}
	</ol>
</section>
