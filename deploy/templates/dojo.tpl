{literal}
<style>
#scroll{
	margin:0 auto 1em;
	text-align:center;
}
.left-scroll-bookend{
	display:inline-block;
	background:url(/images/scroll_accent_left.png) no-repeat left;
	height:100px;
	padding-left:57px;
	margin:0 auto;
}
.right-scroll-bookend{
	vertical-align:middle;
	background:url(/images/scroll_accent_right.png) no-repeat right;
	height:100px;
	min-width:50%;
	padding-right:57px;
	display:inline-block;
	position:relative;
}
#scroll #scroll-title{
	height:93px;
	display:inline-block;
	padding: 35px .7em 35px;
	font-size: 1.3em;
	background:#ffe1ad;
}
table{
	width:90%;
	margin-left:5%;
	margin-right:5%;
	margin-bottom:2em;
}
table .char-title td{
	font-style:1.5em;
}
.black-robed-monk{
	font-weight:bold;color:gray;
}
.white-robed-monk{
	font-weight:bold;color:#F8F9CF;
}
.training-requirements tbody tr:nth-child(odd) {
   background-color: rgba(100, 100, 100, 0.5);
}
.training-requirements{
  width:80%;
}
.training-requirements caption{
   text-align:center;padding:.2em;font-size:1.3em;color:chocolate;
}
</style>
{/literal}

<h1>Dojo</h1>
<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

<div class="description">
  <p>
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
  </p>
  <p>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </p>
</div>

{if $error}
<div class='parent'>
    <p class='ninja-error'>{$error}</p>
</div>
{/if}

{foreach from=$pageParts item="part"}
	{include file="dojo.$part.tpl"}
{/foreach}
