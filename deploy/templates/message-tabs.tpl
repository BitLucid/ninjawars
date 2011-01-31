<style type='text/css'>
{literal}
#tabs{
	background-color:rgb(30, 30, 30);
	margin: .2em 0;
	font-size:1.5em;
	font-weight:bold;
	text-align:center;
}
.current{
	background-color:rgb(2, 2, 10);
	background-color:rgba(2, 2, 10, .5);
	color:rgb(230, 230, 230);
}

#tabs ul li {
	list-style-type:none;
	display:inline-block;
	margin-left:2em;
}
#tabs ul li a{
	padding:.5em;
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
		<li class='{if $current == 'status'}current{/if} first'>{if $current != 'status'}<a href='events.php'>{/if}Status{if $current != 'status'}</a>{/if}</li>
		<li class='{if $current == 'messages'}current{/if}'>{if $current != 'messages'}<a href='messages.php'>{/if}Messages{if $current != 'messages'}</a>{/if}</li>
	</ul>

</div>
