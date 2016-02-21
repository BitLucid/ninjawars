<article>
	<header>
		<h1>Quest: {$quest.title}</h1>
	</header>

	<h2>{$quest.title} from <a href='/player?player_id={$quest.player_id}'>{$quest.giver}</a></h2>
	Tags: {$quest.tags}
	Description: {$quest.description}
	Rewards: {$quest.rewards}
	Obstacles: {$quest.obstacles}
	Expiration: {$quest.expiration}
	Proof Required: {$quest.proof}
</article>

<nav class='glassbox'><a href='/quest/'>View All Quests</a></nav>

