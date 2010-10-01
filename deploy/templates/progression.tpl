<style type='text/css'>
{literal}

/* Main Landing Page */

#progression {
	text-align:center;
	margin: .2em 0 .5em 0;
	font-size:1.7em;
	font-family:"Trebuchet MS",Arial,Helvetica,sans-serif;
	/*font-family: Impact, sans-serif;*/
	/*font-variant: small-caps;*/
}
#progression a {
	font-family:"Trebuchet MS",Arial,Helvetica,sans-serif;
}
#later-progression a{
    color:whitesmoke;
}
#progression .down-arrow {
	height:35px;
}
#progression p {
	margin: 0;
}
#progression p:first-child {
	font-size:larger;
}
#join-link{
    color:#66CCFF;
    font-size:1.5em;
    text-shadow:#c00 2px 2px 2px;
}
#join-link:hover{
    color:brown;
    font-size:1.6em;
}
{/literal}
</style>

<div id='progression'>
{if !$user_id}
	<p><a target='main' href='{$templatelite.const.WEB_ROOT}signup.php' id='join-link'>Become a Ninja!&shy;</a></p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
{/if}
    <div id='later-progression' style='margin-top:0;margin-bottom:0'>
    {literal}
    <script>        
        $().ready(function(){
        
            // Fade the link colors in gradually, one at a time.
            $('#later-progression a')
                .each(function(secs, element){
                    setTimeout(function (){
                        $(element).css({'color':'steelBlue'});
                    }, 1000*(secs+1)*5);
                });
            $('#join-link').each(function(index, element){
                setTimeout(function (){
                    $(element).css({'color':'steelBlue', 'font-size':'1.5em'});
                }, 1000*26);
            });
        });
    </script>
    {/literal}
	<p>Rob townsfolk in the <a target='main' href='{$templatelite.const.WEB_ROOT}attack_player.php'>Village</a>, gather loot</p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Kill other <a target='main' href='{$templatelite.const.WEB_ROOT}list_all_players.php'>Ninja</a>, get stronger at the <a target='main' href='{$templatelite.const.WEB_ROOT}dojo.php'>Dojo</a></p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Join a <a target='main' href='{$templatelite.const.WEB_ROOT}clan.php'>Clan</a>, wage war on other ninja clans</p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Live by the Sword, and <a target='main' href='{$templatelite.const.WEB_ROOT}shrine.php'>avoid death</a> if you can!</p>
	</div>
</div>
