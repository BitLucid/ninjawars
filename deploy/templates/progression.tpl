<div id='progression'>
{if !$user_id}
	<p><a id='join-link' target='main' href='{$templatelite.const.WEB_ROOT}signup.php'>Become a Ninja!</a></p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
{/if}
    <div id='later-progression' style='margin-top:0;margin-bottom:0'>
    {literal}
    <script>
        // Fade the link colors in gradually, one at a time.
        var delay = function delay(secs, element){
            setTimeout(function (){
                $(element).css({'color':'steelBlue'});
            }, 1000*(secs+1)*5);
        }
        
        $().ready(function(){
            $('#later-progression a').css({'color':'whitesmoke'}).each(delay);
            $('#join-link').css({'color':'blue'}).each(function(index, element){
                setTimeout(function (){
                    $(element).css({'color':'steelBlue', 'font-size':'1.1em'});
                }, 1000*26);
            });
        });
    </script>
    {/literal}
	<p>Rob townsfolk in the <a target='main' href='{$templatelite.const.WEB_ROOT}attack_player.php'>Village</a>, gather loot</p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Kill other <a target='main' href='{$templatelite.const.WEB_ROOT}list_all_players.php'>Ninja</a>, get stronger at the <a href='{$templatelite.const.WEB_ROOT}dojo.php'>Dojo</a></p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Join a <a target='main' href='{$templatelite.const.WEB_ROOT}clan.php'>Clan</a>, wage war on other ninja clans</p>
	<img class='down-arrow' src='images/Down_Arrow_Icon.png' alt='then'>
	<p>Live by the Sword, and <a target='main' href='{$templatelite.const.WEB_ROOT}shrine.php'>avoid death</a> if you can!</p>
	</div>
</div>
