<style type='text/css'>
{literal}
#tabs{
	background-color:rgb(30, 30, 30);
	margin: .2em 0 .7em;
	font-size:1.5em;
	font-weight:bold;
	text-align:center;
}
.current{
	background-color:black;
	color:rgb(230, 230, 230);
}
#tabs .current a, #tabs .current a:hover{
	/* Make the current tab look less like a link, since they're already on that tab/page */
	color:white;
	font-weight:normal;
	border-right:3px gray inset;
	border-left:3px gray inset;
	border-top:3px gray inset;
}


#tabs ul li {
	list-style-type:none;
	display:inline-block;
	margin-left:2em;
}
#tabs ul li a{
	padding:.3em .7em;
	color:rgb(30, 40, 240);
	display:inline-block;
}

#tabs ul li.first{
	border-left:none;
}

{/literal}
</style>

<div id='tabs'>

	<ul>
		<li class='{if $current == 'status'}current{/if} first'>
			<a href='events.php'>Status</a>
		</li>
		<li class='{if $current == 'messages'}current{/if}'>
			<a href='messages.php'>Messages</a>
		</li>
		{if $has_clan}
		<li class='{if $current == 'clan'}current{/if}'>
			<a href='messages.php?type=1'>Clan-Chat</a>
		</li>
		{/if}
	</ul>

</div>
