
<!-- message tabs css section in the main css file -->

<nav class='message-tabs' id='tabs'>

	<ul>
		<li class='{if $current == 'status'}current{/if} first'>
			<a href='/events'>Status</a>
		</li>
		<li class='{if $current == 'messages'}current{/if}'>
			<a href='/messages'>Messages</a>
		</li>
		{if $has_clan}
		<li class='{if $current == 'clan'}current{/if}'>
			<a href='/messages/clan'>Clan-Chat</a>
		</li>
		{/if}
	</ul>

</nav>
