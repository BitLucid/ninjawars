<style type='text/css'>
#faqs {
  margin: .5em auto 1em;
  padding: .2em;
  width: 90%;
}
#faqs p{
  border: 1px solid #7BA9AD;
  border-bottom-left-radius: 10px 10px;
  border-bottom-right-radius: 10px 10px;
  border-top-left-radius: 10px 10px;
  border-top-right-radius: 10px 10px;
  padding: 1em;
}
#faqs .notice{
  font-style:italic;
}
#faqs .brownHeading{
  font-variant:small-caps;
}
#progression {
  text-align:center;
  margin: 3em 0 0.5em 0;
  font-size:1.7em;
  font-family:"Trebuchet MS",Arial,Helvetica,sans-serif;
  /*font-family: Impact, sans-serif;*/
  /*font-variant: small-caps;*/
}
#progression a {
  font-family:"Trebuchet MS",Arial,Helvetica,sans-serif;
}
#later-progression a{
  margin-top:0;margin-bottom:0;
}
#progression .down-arrow {
  height:35px;margin-top:0.5em;
}
#progression p {
  margin: 0;
}
#progression p:first-child {
  font-size:larger;
}
#join-link{
    font-size:3.5rem;
}
.accent-sandwiched{
  display:inline-block;margin: .4em auto .3em;text-align:center;font-size:1.1em;font-style:italic;
  padding-left:2em;padding-right:2em;
  border-top:1px solid rgba(0, 129, 165, 0.4); border-bottom:1px solid rgba(0, 129, 165, 0.4);
}
.accent-sandwiched a{
  display:inline-block;width:100%;height:100%
}
/* Don't display the h1 when housed within the iframe */
#main-page-headings{
  display:none;
}
.solo-page #main-page-headings{
  display:inherit;
}
/** Text to accent as links later **/
a.dull-link{
  color:ghostwhite;
}
a.dull-link#join-link{
  font-size:3.5rem;
}
</style>

<div id='main-page-headings'>

  <h1>The Ninja Game at Ninjawars.net</h1>
  <h2>Live by the Shuriken!</h2>
</div>

<div id='progression'>
  <div class='not-user js-target'>
  	<p><a class='dull-link' target='main' href='/signup' id='join-link'>Become a Ninja!&shy;</a></p>
  	<img class='down-arrow' src='{cachebust file="/images/Down_Arrow_Icon.png"}' alt='then'>
  </div>

<div id='later-progression'>
	<p>Explore the <a class='dull-link' target='main' href='/map'>map</a> and <a class='dull-link' target='main' href='/enemies#npcs'>attack monsters</a>, gather loot</p>
	<img class='down-arrow' src='{cachebust file="/images/Down_Arrow_Icon.png"}' alt='then'>
	<p>Kill other <a class='dull-link' target='main' href='/list'>Ninja</a>, get stronger at the <a class='dull-link' target='main' href='/dojo'>Dojo</a></p>
	<img class='down-arrow' src='{cachebust file="/images/Down_Arrow_Icon.png"}' alt='then'>
	<p>Join a <a class='dull-link' target='main' href='/clan'>Clan</a>, wage war on other ninja clans</p>
	<img class='down-arrow' src='{cachebust file="/images/Down_Arrow_Icon.png"}' alt='then'>
	<p>Live by the Sword, and <a class='dull-link' target='main' href='/shrine'>avoid death</a> if you can!</p>
	</div>
</div>




<div class='centered'>
  <div id='show-faqs' class='accent-sandwiched'>
    <a target='main' href="/intro?show_faqs=1" id='show-faqs-link'>Show More Info</a>
  </div>
</div>


<div id='faqs'>
<div id='scrollable-viewport'>

  <p>
    <span class="brownHeading">How do I level up ?</span><br>
    By <a href='/list'>killing other Ninja</a>.
    Once you have enough kill points, you'll be levelled up automatically.  You can visit the <a href="/dojo">Dojo</a> to get a listing of what will change, and what kills you need.
  </p>

  <p>
    <span class="brownHeading">How do I attack another ninja?</span><br>
    You can attack another ninja by selecting <a href="/enemies">fight</a> from the main page clicking a ninja's name, or viewing the <a href="/list">list of ninjas</a> on the "player list", then click their name and attack them from their profile page.
  </p>

  <p>
    <span class="brownHeading">I need turns, where can I get them?</span><br>
    You can <a href='/inventory'>use amanita mushrooms</a> once you buy some from the <a href='/shop'>shop</a> with gold, or wait for the half-hour and you will receive a few turns automatically (more if you have the <a href='/skill'>"speed" skill</a>).
  </p>

  <p>
    <span class="brownHeading">I need gold, where can I get it?</span><br>
    You can get a gold from <a href='/enemies'>NPCs</a>, <a href='/enemies'>killing other ninja</a> for a fraction of their gold, or <a href="/work">Working</a> in fields on the <a href='/map'>Map</a>, which will let you trade your time/turns for gold. Also, the <a href="/doshin">Doshin Office</a> keeps a list of ninjas who sometimes have bounties on their heads. Killing those ninja will get you the bounty as a reward.
  </p>

  <p>
    <span class="brownHeading">How do I attack an NPC?</span><br>
    Choose the <a href="/enemies">Fight</a> link from the main page, then click an NPC's link.  Most NPCs only give items and gold, not kill points, with the exception of the Samurai, who is very difficult to kill, easily a match for any ninja.
  </p>

  <p>
    <span class="brownHeading">How do I use items?</span><br>
    There are two ways.  Either go to your own <a href="/inventory">Inventory</a>, where you can use stealth scrolls and speed scrolls on you-rself, or go to another ninja's page from the ninja list and then click on the item to use it against your target.
  </p>

  <p>
    <span class="brownHeading">How do I use my skills?</span><br>
    Different Ninjas have different <a href="/skill">skills</a> based on ninja class.  Either click the <a href='/skill'>Skills</a> link from the menu for any skills that you can use on yourself, or find an enemy ninja's profile page and click a skill to use it on them.
  </p>

  <p>
    <span class="brownHeading">How can I talk with other players?</span><br>
    You can message players from their profile, send a message to all your clan members from the <a href='/clan'>Clan</a> link if you are part of a clan, or post public chats to all players on the <a href='/village'>full chat</a> board.  To check for messages sent directly to you, click the <a href='/messages'>Messages</a> link on the main page.
  </p>

  <p>
    <span class="brownHeading">How do I join a clan?</span><br>
    View a clan and make a join request when viewing their clan page. If the leader is active, they'll get a message and respond to let you in or not.  If not, you're free to try requesting with any number of clans, or you can make your own once you reach about level 20.
  </p>

  <p class='notice'>
    For various other info, see the <a target='_blank' class='extLink' href="http://ninjawars.pbworks.com">Wiki</a>
  </p>
 </div>
</div><!-- End of faqs div -->

<script src='/js/intro.js'></script>
