<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">
<script>
{literal}
  var link = document.querySelector("link[rel~='icon']");
if (!link) {
    link = document.createElement('link');
    link.rel = 'icon';
    document.head.appendChild(link);
}
link.href = '/images/ninjamaster/shuriken-favicon.png';
{/literal}
</script>

<style>
#admin-favicon {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 1000;
  background: #ff29ed;
  border-radius: 50%;
  padding: 1rem;
  box-shadow: 0 0 1rem #ff29ed;
}
#admin-favicon svg {
  height: 5rem;
  width: 5rem;
}
.admin-nav-float {
  position: absolute;
  z-index: 1000;
  left: 0.5rem;
  top: 0.5rem;
}
</style>
<div id='admin-favicon'>
  <span>
    <a href='/ninjamaster'>
      <img src='/images/ninjamaster/shuriken-favicon.svg' width='50' height='50' alt='dashboard-icon' />
    </a>
  </span>
</div>

{include file="ninjamaster.css.tpl"}

<span class='admin-nav-float'>
  <a href='/' class='btn btn-default' title='Return Home'><i class='fa fa-home'></i></a>
</span>
<div id='admin-actions'>

<h1>Admin Dashboard</h1>

{if $error}
	<div class='parent'>
		<div class='child error'>
			{$error|escape}
		</div>
	</div>
{/if}

<nav class='admin-nav parent'>
	<div class='child'>
		<ul class='nav nav-pills'>
			<li><a class='' href='/ninjamaster/#npc-list-stats'>Npc List</a></li>
			<li><a class='' href='/ninjamaster/#char-list'>Character List</a></li>
			<li><a class='' href='/ninjamaster/#clans'>Clans</a></li>
			<li><a class='' href='/ninjamaster/tools'>Validation Tools</a></li>
			<li><a class='' href='/ninjamaster/player_tags'>Character Tag List</a></li>
			<li><a class='' href='/ninjamaster/#item-list-area'>Item List</a></li>
			<li><a class='' href='/ninjamaster/#aws-services'>AWS Services</a></li>
			<li><a class='' href='/epics'>UI Epics</a></li>
		</ul>
	</div>
</nav>

<section class='centered glassbox special-info'>
	<form name='char-search' action='/ninjamaster' method='post'>
		View character @<input id='char-name' name='char_name' type='text' placeholder='character' value='{$char_name|escape}' required=required>
		<div><input type='Submit' value='Find'></div>
	</form>
</section>

{if $char_infos}
<!-- View the details of some ninja -->
{foreach from=$char_infos item='char_info'}
<section class='char-info-area glassbox'>
	<span class='char-actions float-right'>
		<a href='./' title='clear'><i class='fa fa-times-circle'></i></a>
		<a href='/player?player_id={$char_info.player_id|escape}' title='View public profile'><i class='fa fa-eye'></i></a>
	</span>
	<h2><a href='?view={$char_info.player_id|escape}'>{$char_info.uname|escape}</a></h2>
	<div id='char-info-scroll'>
		<table id='char-info-table'>
			<thead>
				{foreach from=$char_info key='name' item='stat'}<th class='char-info-header'>{$name|escape}</th>{/foreach}
			</thead>
			<tr>
			{foreach from=$char_info key='name' item='stat'}
				<td>{$stat|escape}</td>
			{/foreach}
			</tr>
		</table>
	</div>
	<div class='text-center'>
		<div class='highlight-box thick'>
		active {$char_info.days} days ago
		</div>
	</div>
	{if $char_info.first}
	<div class='char-profile'>Out-of-Character profile: {$first_message|escape}</div>
	<div class='char-description'>Char Description: {$first_description|escape}</div>
	{if $first_account}
	<section class='account-info inline-block half-width centered'>
		<div>
			<h3>Account Info</h3>
			<dl class='left-aligned'>
				<dt>Account Identity</dt><dd>{$first_account->identity()|escape}</dd>
				<dt>Active Email</dt><dd>{$first_account->getActiveEmail()|escape}</dd>
				<dt>Karma Total</dt><dd>{$first_account->getKarmaTotal()|escape}</dd>
				<dt>Last Login</dt><dd><time class='timeago' datetime='{$first_account->getLastLogin()|escape}'>{$first_account->getLastLogin()|escape}</time></dd>
				<dt>Last Login Failure</dt><dd><time class='timeago' datetime='{$first_account->getLastLoginFailure()|escape}'>{$first_account->getLastLoginFailure()|escape}</time></dd>
				<dt>Operational</dt><dd>{if $first_account->isOperational()}true{else}false{/if}</dd>
				<dt>Confirmed</dt><dd>{if $first_account->isConfirmed()}1{else}0{/if}</dd>
                <dt>Created</dt><dd><time class="created-time timeago" datetime="{$first_account->created_date|escape}" title="{$first_account->created_date|escape}">
				{$first_account->created_date|escape}</time></dd>
			</dl>
		</div>
		<details class='constrained' style='margin-bottom:1.25rem'>
			<summary>SEE ACTIONS TO TAKE</summary>
			<div>
				<div>
				{if $char_info.active === 1}
					<button class='btn btn-warning' id='start-deactivate'>
						Begin to Deactivate {$char_info.uname|escape}
					</button>
					<button type='button' class='btn btn-danger' style='display:none' id='deactivate-character' data-char-id="{$char_info.player_id}">
						Are you sure you want to deactivate {$char_info.uname|escape}
					</button>
					{/if}
				</div>
				<div>
					{if $char_info.active !== 1}
					<button type='button' class='btn btn-default' id='reactivate-character' data-char-id="{$char_info.player_id}">
						Reactivate {$char_info.uname|escape}
					</button>
					{/if}
				</div>
			</div>
		</details>
		{if $char_info.active !== 1}
		<div class='alert alert-info'>
			This character is inactive.
		</div>
		{/if}
	</section>
	{/if}
	{/if}
	<section class='char-inventory-area inline-block half-width'>
		<h3>Inventory for <strong class='char-name'>{$char_info.uname|escape}</strong></h3>
		<details class='constrained'>
			<summary>
				See item counts
			</summary>
			<div>
				<table class='char-inventory'>
					{foreach from=$char_inventory key='name' item='item'}
						<tr class='info'>
						<td>&#9734;</td>
						{foreach from=$item key='column' item='data'}
							<td class='headed'>{$column|escape}</td><td> {$data|escape}</td>
						{/foreach}
						</tr>
					{/foreach}
				</table>
			</div>
		</details>
	</section>
</section>
{/foreach}
{/if}


<section class='special-info'>
	<header>
		<h2 id='usage-usage'>Recent Activity</h2>
	</header>
	<div class='carded-area'>
		<div class='card card-50'>
			<div class='card-container'>
				<h5>New Players</h5>
				<div>Recent new players in 7 days: {$usage.new_count}</div>
				<ul>
					{foreach from=$usage.new item='nChar'}
						<li><a href='?view={$nChar.player_id|escape}'>{$nChar.uname|escape}</a> <time class='timeago' datetime='{$nChar.created_date|escape}'>{$nChar.created_date|escape}</time></li>
					{/foreach}
				</ul>
			</div>
		</div>
		<div class='card'>
			<div class='card-container'>
				<h5>Logins</h5>
				<div>Recent logins in 7 days: {$usage.recent_count}</div>
				<ul>
					{foreach from=$usage.recent item='nChar'}
						<li><a href='?view={$nChar.player_id|escape}'>{$nChar.uname|escape}</a> <time class='timeago' datetime='{$nChar.last_login|escape}'>{$nChar.last_login|escape}</time></li>
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
</section>

<section id='review-new-signups' class='special-info'>
	<header>
		<h3>Review New Signups</h3>
	</header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div id='review-new-signups'>
		<h5>New Signups</h5>
		<div>Recent new player accounts created: {$signups.new_count}</div>
		<div>
			<ul>
				{foreach from=$signups.new item='nsChar'}
					<li>
						<button type='button' class='btn btn-danger deactivate-character' data-char-id="{$nsChar.player_id}">
							Deactivate {$nsChar.uname|escape}
						</button>
						<a href='?view={$nsChar.player_id|escape}'>{$nsChar.uname|escape}</a> <time class='timeago' datetime='{$nsChar.created_date|escape}'>{$nsChar.created_date|escape}</time> 
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
</section>

<section id='char-list' class='special-info'>
	<header>
		<h3>Char List of High Rollers</h3>
	</header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div id='char-list-stats'>
		{foreach from=$stats item='stat' key='stat_name'}
		<h2>Most {$stat_name}:</h2>
		<div class='glassbox'>
		{foreach from=$stat item='char'}
			<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname|escape}</a> :: {$char.stat|escape}<br>
		{/foreach}
		</div>
		{/foreach}
	</div>
</section>

<div class='nm-clans-container'>

	{include file="ninjamaster.clans.tpl"}
</div>

{if $dupes}
<section id='duplicate-ips' class='glassbox special-info'>
	<header><h3>Duplicate Ips</h3></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div>
		{foreach from=$dupes item='dupe'}
		<a href='/ninjamaster/?view={$dupe.player_id|escape}' class='char-name'>{$dupe.uname|escape}</a> :: IP <strong class='ip'>{$dupe.last_ip|escape}</strong> :: days {$dupe.days|escape}<br>
		{/foreach}
	</div>
</section>
{/if}

<section class='special-info npc-list'>
	<header><h2 id='npc-list-stats'>Npc list raw info</h2></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div class='npc-raw-info'>
		<section class='npc-raw-info-list-area'>
			{foreach from=$npcs item='npc'}
			<div class='npc-box tiled'>
			<h2>{$npc->identity()|escape}</h2>
			<div class='npc-details'>
				<dl>
					<dt>Name</dt><dd>{$npc->name()|escape}</dd>
					<dt>Race</dt><dd>{$npc->race()|escape}</dd>
					<dt>Difficulty</dt><dd><span class='badge badge-info'>{$npc->difficulty()}</span></dd>
					<dt>Max Damage</dt><dd>{$npc->maxDamage()}</dd>
					<dt>Max Health</dt><dd>{$npc->getMaxHealth()}</dd>
				</dl>
			</div>
			<div class='npc-traits glassbox'>
				<span class='inline'>Traits:</span>
				{if !$npc->traits()}
					<span class='notice'>None</span>
				{/if}
				<ul>
					{foreach from=$npc->traits() item='trait'}
					<li><span class='badge badge-secondary'>{$trait|escape}</span></li>
					{/foreach}
				</ul>
			</div>
			<figure>
				<img {if $npc->image()}src='/images/characters/{$npc->image()|escape}'{/if} class='npc-icon' alt='no-image'>
				<figcaption title='{$npc->shortDesc()|escape}'>{$npc->shortDesc()|escape}&nbsp;</figcaption>
			</figure>
			</div>
			{/foreach}
		</section>
		<h3>Unfinished Raw Npcs</h3>
		<div class='callout'>
			<div class='notice'>
				<p>
					<em>These are npcs that have a difficulty less than one, and are thus unfinished.</em>
					Fighting them will be trivial, they don't have strength, speed, or health beyond the default 1,
					they don't have a representational image (not always required), etc.
				</p>
			</div>
		</div>
		<section class='npc-raw-info-list-area'>
			{foreach from=$trivial_npcs item='npc'}
			<div class='npc-box tiled'>
			<h2>{$npc->identity()|escape}</h2>
			<div class='npc-details'>
				<dl>
					<dt>Name</dt><dd>{$npc->name()|escape}</dd>
					<dt>Race</dt><dd>{$npc->race()|escape}</dd>
					<dt>Difficulty</dt><dd><span class='badge badge-info'>{$npc->difficulty()|escape}</span></dd>
					<dt>Max Damage</dt><dd><span class='damage'>{$npc->maxDamage()|escape}</span></dd>
					<dt>Max Health</dt><dd>{$npc->getMaxHealth()|escape}</dd>
				</dl>
			</div>
			<div class='npc-traits glassbox'>
				<span class='inline'>Traits:</span>
				{if !$npc->traits()}
					<span class='notice'>None</span>
				{/if}
				<ul>
					{foreach from=$npc->traits() item='trait'}
					<li><span class='badge badge-secondary'>{$trait|escape}</span></li>
					{/foreach}
				</ul>
			</div>
			<figure>
				<img src='/images/characters/{$npc->image()|escape}' class='npc-icon' alt='no-image'>
				<figcaption title='{$npc->shortDesc()|escape}'>{$npc->shortDesc()|escape}&nbsp;</figcaption>
			</figure>
			</div>
			{/foreach}
		</section>
	</div>
</section>

<section class='special-info'>
	<header><h2 id='item-list-area'>Item Raw Data</h2></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
{include file="ninjamaster.items.tpl"}
</section>

<section class='special-info'>
	<header><h2 id='aws-services'>AWS Services</h2></header>
	<nav class='glassbox nav nav-pills'>
			<li><a href='https://us-east-1.console.aws.amazon.com/ses/home?region=us-east-1#/reputation'>
				AWS Email Reputation Metrics
			</a></li>
			<li><a href='https://us-east-1.console.aws.amazon.com/ec2/home?region=us-east-1#Instances:instanceState=running'>
				Running Instances
			</a></li>
			<li><a href='https://us-east-1.console.aws.amazon.com/costmanagement/home?region=us-east-1#/home'>
				Billing
			</a></li>
      <li><a href='https://ads.google.com/aw/overview'>
				Adwords
			</a></li>
      <li><a href='https://analytics.google.com/analytics/web/?pli=1#/p289349786/reports/intelligenthome'>
				Analytics
			</a></li>
      <li><a href='https://www.google.com/recaptcha/admin/site/692084162/settings'>
				Recaptcha
			</a></li>
	</nav>
</section>


<footer class="admin-dashboard-footer bg-dark text-light p-4 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
	  	<hr class="bg-light">
        <ul class='nav nav-pills'>

		<li><a class='' href='/ninjamaster/#npc-list-stats'>Npc List</a></li>
		<li><a class='' href='/ninjamaster/#char-list'>Character List</a></li>
		<li><a class='' href='/ninjamaster/tools'>Validation Tools</a></li>
		<li><a class='' href='/ninjamaster/player_tags'>Character Tag List</a></li>
		<li><a class='' href='/ninjamaster/#item-list-area'>Item List</a></li>
		<li><a class='' href='/ninjamaster/#aws-services'>AWS Services</a></li>
		<li><a class='' href='/epics'>UI Epics</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>


<script type='module' src='/js/ninjamaster.js'></script>

</div><!-- End of #admin-actions -->
