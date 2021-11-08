<h1>Staging</h1>

<style>
{literal}
.target-container{
    display:flex;
    justify-content: space-around;
    align-items: stretch;
}
.target-container > *{
    height: 100%;
    min-height: 30vh;
    flex:1;
}
.target-container .previous{
    background-color: #f0f0f0;

}
.target-container .next{
    background-color: #f0f0f0;
}
.target-container .preview{
    flex: 8;
    background-color: #b0a89d;
    display:flex;
    justify-content: center;
}
.action-area{
    display:flex;
    justify-content: space-around;
    align-items: center;
    flex: 1;
    background-color: #3d1818;
    min-height: 20vh;
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
<script>
{literal}
    $(()=>{
        $('#add-enemy, #add-enemy a').click((e)=>{
            e.preventDefault();
            e.stopPropagation();
            $('#ninja-enemy').removeClass('hidden');
    })
{/literal}
</script>
<section class='target-container'>
<div class='previous'>
    &lt;
</div>
<div class='preview'>
    Some ninja
</div>
<div class='next'>
    &gt;
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

<nav>
  <a href="/map" class="return-to-location block">Return to the Map</a>
</nav>