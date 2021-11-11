<link rel="stylesheet" type="text/css" href="{cachebust file="/css/enemies.css"}" media="Screen" />
<h1>Staging</h1>

<style>
{literal}
.target-container{
    display:flex;
    justify-content: space-around;
    align-items: stretch;
    min-height: 20vh;
}
.target-container > *{
    height: 100%;
    flex:1;
}
.target-container .previous{
}
.target-container .next{
}
.target-container .target-preview{
    flex: 8;
}
.target-container .target-preview h2{
    width: 100%;
}
.target-container .actions{
    float:right;
    font-size:3rem;
    margin-right: 1rem;
}
.target-container .char-profile{
    display:flex;
    justify-content:space-evenly;
    align-items:stretch;
}
.target-container .char-profile > *{
    flex:1;
}
.target-container .char-profile .char-information {
    flex:4;
}
.target-container .char-profile .char-information charstats{
    display: flex;
    justify-content: space-evenly;
    text-align: left;
}
.target-container .spin-enemy {
    font-size: 13rem;
    padding: 0;
}
.target-container .spin-enemy a:hover{
    text-decoration: none;
}
.target-container .spin-enemy.up {
    text-align: right;
}
.target-container .space-evenly{
    display:flex;
    justify-content:space-evenly;
    align-items:stretch;
}
.action-area{
    display:flex;
    justify-content: space-around;
    align-items: center;
    flex: 1;
    background-color: #3d1818;
    min-height: 15vh;
    margin-bottom:5vh;
}
.enemies{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
}
.enemies > *{
    min-height:10vh;
    background:teal;
    border:thin solid black;
    display:flex;
    justify-content: center;
    align-items: center;
}
#add-enemy a{
    color:white;
    text-decoration: none;
}
{/literal}
</style>
<section class='target-container'>
<div class='previous'>
    <div class='spin-enemy down'>
        ❮
    </div>
</div>
<section class='target-preview'>
    <div>
        <actions class='actions'>
            <button type='button' class='btn btn-primary'>
                <i class='fas fa-bars'></i>
            </button>
        </actions>
        <h2 class='char-target-name'>&nbsp;</h2>
    </div>
    <div class='char-profile'>
        <figure><img class='char-avatar' alt=''/></figure>
        <div class='char-information'>
            <charstats class='skeleton'>
                <span class='char-level'>Level</span>
                <span class='char-class'>Class</span>
                <span class='char-difficulty'>Difficulty</span>
            </charstats>
            <div>
                <span class='health-bar-container'>
                    <!-- This is for generating a health status bar on various pages -->
                    <span class='char-health-indicator'>
                        <span class='char-health-border'>
                            <span class='character-health-bar'></span>
                        </span>
                        <span class='char-health-number'>
                            <span class='dead' style='display:none'>
                                <i class="far fa-heart" aria-hidden="true"></i> <span class='dead-notice'>Dead</span>
                            </span>
                            <span class='alive'>
                                <i class="fas fa-heart" aria-hidden="true"></i> 
                                <span class='char-numeric-health health-number'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            </span>
                        </span>
                    </span>
                </span>
                <div>
                    <span class='char-status status-b badge skeleton'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </div>
            </div>
        </div>
    </div>
</section>
<div class='next'>
    <div class='spin-enemy up'>
        ❯
    </div>
</div>

</section>

<div class='action-area'>
    <button class='btn btn-primary'>ATTACK</button>
</div>

<div class='enemies'>
<div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div id='add-enemy'><a href='#ninja-enemy'><i class='fa fa-plus'></i></a></div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>
</div>

<div id="ninja-enemy" class='solo-box hidden'>
  <form id="enemy-add" action="/enemies/search" method="get" name="enemy_add">
    <div class='input-group'>
      <input id='enemy-match' required=required type="text" maxlength="50" name="enemy_match" class="form-control textField" placeholder='Search by ninja name' value='{if isset($enemy_match)}{$enemy_match}{/if}'>
      <span class='input-group-btn'>
        <input type="submit" value="Find Enemies" class="btn btn-default formButton">
      </span>
    </div>
  </form>
</div>

{if !empty($found_enemies) && count($found_enemies) gt 0}
	{include file="enemy-matches.tpl" enemies=$found_enemies}
{elseif isset($enemy_match) && $enemy_match}
	<div class='hidden'>
Your search returned no ninja. Maybe you should make an enemy of someone who recently attacked you.
		{include file="enemy-matches.tpl" enemies=$recent_attackers}
	</div>
{/if}

<script type='module' src='/js/fight.js'></script>

<nav>
  <a href="/map" class="return-to-location block">Return to the Map</a>
</nav>