<h1>Live by the Sword!</h1>

<script type='text/javascript'>
{literal}
$(document).ready(function () {

    $('#show-faqs').click(function(){
            $('#faqs').toggle();
            $('#show-faqs').toggle();
            return false;
        });
        
    {/literal}
    {if $show_faqs}
    $('#show-faqs').toggle();
    {else}
    $('#faqs').toggle();
    {/if}
    {literal}

});
{/literal}
</script>

{include file='progression.tpl' user_id=$user_id}

{if !$show_faqs}
  <div id='show-faqs' style='margin: .3em auto;text-align:center;font-size:1.1em;font-style:italic;border-top:1px solid blue;border-bottom:1px solid blue;'>
  <a target='main' href="tutorial.php?show_faqs=true" id='show-faqs-link' style='display:inline-block;width:100%;height:100%'>
    Show More Info
  </a>
  </div>
{/if}


<div id='faqs'>

    <p><span class="brownHeading">How do I level up ?</span><br>
    By <a href='list_all_players.php'>killing other Ninja</a>.  Once you have enough kill points, you may increase your level by visiting the <a href="dojo.php">Dojo</a> in the village, which will make you do more damage and make your abilities stronger.</p><hr>

    <p><span class="brownHeading">How do I attack another ninja?</span><br>
    You can attack another ninja by selecting <a href="enemies.php">combat</a> from the main page menu then putting a ninja's name into the search, or viewing the <a href="list_all_players.php">list of ninjas</a> on the "player list", then click their name and attack them from their profile page.</p>
    <hr>

    <p><span class="brownHeading">I need turns, where can I get them?</span><br>
    You can <a href='inventory.php'>use speed scrolls</a> once you buy some from the <a href='shop.php'>shop</a> with gold, or wait for the half-hour and you will receive 2 turns per half-hour automatically (3 if you have the <a href='skills.php'>"speed" skill</a>).</p>
    <hr>

    <p><span class="brownHeading">I need gold, where can I get it?</span><br>
    You can get a percentage of gold from <a href='attack_player.php'>NPCs</a>, <a href='enemies.php'>killing other ninja</a>, or <a href="work.php">Working</a> in fields near the <a href='attack_player.php'>Village</a>, which will let you trade your time/turns for gold. Also, the <a href="doshin_office.php">Doshin Office</a> keeps a list of ninjas with bounties on their heads. Killing those ninja will get you the bounty as a reward.</p>
    <hr>

    <p><span class="brownHeading">How do I attack an NPC?</span><br>
    Choose the <a href="attack_player.php">Village</a> link from the main page, then click an NPC's link.  Most NPCs only give items and gold, not kill points, with the exception of the Samurai, who is very difficult to kill.</p>
    <hr>

    <p><span class="brownHeading">How do I use items?</span><br>
    There are two ways.  Either go to your own <a href="inventory.php">Inventory</a>, where you can use stealth scrolls and speed scrolls on you-rself, or go to another ninja's page from the ninja list and then click on the item to use it on them.</p>
    <hr>

    <p><span class="brownHeading">How do I use my skills?</span><br>
    Different Ninjas have different <a href="skills.php">skills</a> based on your ninja color (red, white, blue, black, and gray).  Either click the <a href='skills.php'>Skills</a> link from the menu for any skills that you can use on yourself, or find an enemy ninja's profile page and click a skill to use it on them.</p>
    <hr>

    <p><span class="brownHeading">How can I communicate with other players?</span><br>
    You can message players from their profile, send a message to all your clan members from the <a href='clan.php'>Clan</a> link if you are part of a clan, or post public chats to all players on the <a href='village.php'>full chat</a> board.  To check for messages sent directly to you, click the <a href='messages.php'>Messages</a> link on the main page.</p>
    <hr>

    <p><span class="brownHeading">How do I join a clan?</span><br>
    Find a clan you want to join or a clanleader you want to follow and then click on the clan section, then Join a clan, and find the name of the clan or clanleader.  When you click on the clan leader's name to join their clan, a message will be sent to them to make sure that they want to let you in.</p>


<p class='notice'>
	For various other info, see the <a target='_blank' class='extLink' href="http://ninjawars.pbworks.com"> Wiki</a>
</p>

</div><!-- End of faqs div -->
