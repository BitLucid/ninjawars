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

<h1>Dashboard</h1>

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
			<li><a class='' href='/ninjamaster/#clan-list'>Clans</a></li>
			<li><a class='' href='/ninjamaster/tools'>Validation Tools</a></li>
			<li><a class='' href='/ninjamaster/player_tags'>Character Tag List</a></li>
			<li><a class='' href='/ninjamaster/#item-list-area'>Item List</a></li>
			<li><a class='' href='/ninjamaster/#api-epics'>Api Epics</a></li>
			<li><a class='' href='/ninjamaster/#services'>Infrastructure Services</a></li>
			<li><a class='' href='/epics'>UI Epics</a></li>
		</ul>
	</div>
</nav>

<div class='hero'>
	<h2>Welcome!</h2>
	<!-- make this area display with whitespace intact -->
	<div class='intro'>
		<pre class='spaced'>{* 
		*}<p>You have the power to view internal out-of-character settings, and intimate details of other players, npcs, items, etc.
			</p>
			<span class='notice'>Do:</span> Use this power wisely, and for the good of the game.
			<span class='notice'>Do not:</span> Share this information with other players, as it might 
			make the game easier and less fun for them.</span>
		</pre>
	</div>
</div>

<section class='special-info'>
	<header>
	<h2 id='usage-usage'>Game Health Checks</h2>
	</header>
	<article>
		<pre class='hi-pri-warnings spaced'>
			Alerts and warnings about the game's health based on live statistics.

			Operational Health:

			{if $usage.new_count eq 0}<span class="alert alert-danger"><i class="fa-solid fa-tornado"></i> Danger: Signups appear low, check signup page health, at {$usage.new_count|escape} in the last week.</span>{else}<i class="fa-solid fa-sun"></i> Signup system seems to be functioning normally.{/if}



			{if $usage.recent_count lt 5}<span class="alert alert-danger"><i class="fa-solid fa-tornado"></i> Danger: Recent logins in the last week are at a low level of {$usage.recent_count}, check the login system!</span>{else}<i class="fa-solid fa-sun"></i> Login system seems to be functioning normally.{/if}




			Social Engagement Health:


			{if $usage.new_count lt 10}<span class="alert alert-warning"><i class="fa-solid fa-cloud-sun-rain"></i> Warning: Recruiting of new players is low, signups are at {$usage.new_count|escape} in the last week.</span>{else}<i class="fa-solid fa-sun"></i> Recruitment of new players seems normal at {$usage.new_count|escape} in the last period.{/if}



			{if $usage.recent_count lt 10}<span class="alert alert-warning"><i class="fa-solid fa-cloud-sun-rain"></i> Warning: Current engagement levels seem to be low, recent logins were: {$usage.recent_count}</span>{else}<i class="fa-solid fa-sun"></i> Player engagement levels at least appear to be within normal bounds.{/if}
			


			{if $usage.last_hour_attacks_count lt 5}<span class="alert alert-info"> Info: Combat in the last hour seems low, recent attacks were: {$usage.last_hour_attacks_count}</span>{else}<i class="fa-solid fa-sun"></i> Combat occurrances appear to be within normal activity bounds.{/if}


		</pre>
		{* To add: Too high of login attempts, too high of activity rates for signups? *}
	</article>
</section>

<section class='special-info'>
	<header>
	<h2 id='usage-usage'>Infrastructure Health</h2>
	</header>
	<article>
		<pre class='infrastructural-checks spaced'>
			These checks are manual, but important to click through to every month.
			High Priority Checks:

			★ Email Sendability Reputation: <a href='https://us-east-1.console.aws.amazon.com/ses/home?region=us-east-1#/reputation'>Emailability Health</a>
			★ Cost Anomalies: <a href='https://us-east-1.console.aws.amazon.com/cost-management/home?region=us-east-1#/anomaly-detection/overview'>Cost Anomalies</a>
			★ AWS infrastructure Alarms: <a href='https://us-east-1.console.aws.amazon.com/cloudwatch/home?region=us-east-1#alarmsV2:'>AWS Infrastructure Alarms</a>

			These checks can be checked once every 3 months, and there are sometimes other alert mechanisms.
			Medium Priority Checks:
			• DNS Health checks: <a href='https://us-east-1.console.aws.amazon.com/route53/healthchecks/home?region=us-east-1#/'>DNS Health Checks</a>
			• AWS Account Health Notices: <a href='https://health.aws.amazon.com/health/home#/account/dashboard/open-issues'>Account Health</a>
			• Servers health: <a href='https://us-east-1.console.aws.amazon.com/ec2/home?region=us-east-1#Instances:'>Server Instances Health</a>
			• Cost Management Health: <a href='https://us-east-1.console.aws.amazon.com/costmanagement/home?region=us-east-1#/home'>Cost Management Health</a>
			• Load Balancer Health: <a href='https://us-east-1.console.aws.amazon.com/ec2/home?region=us-east-1#LoadBalancers:'>Load Balancers Health</a>
			• Databases Health: <a href='https://us-east-1.console.aws.amazon.com/rds/home?region=us-east-1#databases:'>Databases Health</a>

			Check these just as desired.
			Low Priority Checks:
			• Adwords Health: <a href='https://ads.google.com/aw/overview?ocid=8472107'>Adwords Health</a>
			• Adsense Health: <a href='https://www.google.com/adsense/new/u/0/pub-9488510237149880/home'>Adsense Health</a>
		</pre>
		{* To add: Too high of login attempts, too high of activity rates for signups? *}
	</article>
</section>

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
				<dt>Account Id</dt><dd>{$first_account->id()|escape}</dd>
				<dt>Active Email</dt><dd>{$first_account->getActiveEmail()|escape}</dd>
	<dt>Type</dt><dd>{$first_account->getType()|escape}{if $first_account->getType() eq 1} Moderator{elseif $first_account->getType() eq 2} Admin{else} - Standard Account{/if}</dd>
				<dt>Karma Total</dt><dd>{$first_account->getKarmaTotal()|escape}</dd>
				<dt><i class="fa-solid fa-person-running"></i> Last Login</dt><dd><time class='timeago' datetime='{$first_account->getLastLogin()|escape}'>{$first_account->getLastLogin()|escape}</time></dd>
				<dt><i class="fa-solid fa-door-closed"></i> Last Login Failure</dt><dd><time class='timeago' datetime='{$first_account->getLastLoginFailure()|escape}'>{$first_account->getLastLoginFailure()|escape}</time></dd>
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
<div>Recent new players in 7 days: <span class='{if $usage.new_count eq 0}warning notice{/if}'>{$usage.new_count}</span></div>
				<div>Signup spam-rejection trigger rate: <abbr title='In other words, this is the fraction of signups that will be rejected by recaptcha if it catches shenanigans'><span class='notice warning'>1/{RECAPTCHA_DIVISOR}</span></div>
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
				<div>Recent logins in 7 days: <span class='{if $usage.recent_count lt 5}warning notice{/if}'>{$usage.recent_count}</div>
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
		<p>
			Recent new players in 7 days: <span class='{if $signups.new_count lt 5}warning notice{/if}{if $signups.new_count gt 50}warning notice{/if}'>{$signups.new_count}</span>
		</p>
		<div>Recent new player accounts created: {$signups.new_count}</div>
		<div><em>(Showing the latest {count($signups.new)|escape} signups:)</em></div>
		<p class='alert alert-info'>Info: Watch out for spam accounts in this list with totally random non-human names.</p>
		<p class='alert alert-info'>Note: Generally Viper-xxxx are just temporary testing accounts, though.</p>
		<div>
			<ul>
				{foreach from=$signups.new item='nsChar'}
					<li>
						<button 
							type='button'
							class='btn {if isset($nsChar.last_login)}btn-danger{else}btn-info{/if} deactivate-character' 
							data-char-id="{$nsChar.player_id}" 
							data-char-last-login="{if $nsChar.last_login}1{else}0{/if}"
						>
							Deactivate {$nsChar.uname|escape}
						</button>
							<a href='?view={$nsChar.player_id|escape}'>view {$nsChar.uname|escape}</a> 
							<em>Created {if !isset($nsChar.created_date)}(unknown){/if} <time class='timeago' datetime='{$nsChar.created_date|escape}'>{$nsChar.created_date|escape}</time> </em>
							<em>Last login: {if isset($nsChar.last_login)}<time class='timeago' datetime='{$nsChar.last_login|escape}'>{$nsChar.last_login|escape}</time>{else}NEVER{/if}</em>
					</li>
				{/foreach}
			</ul>
			<div class='text-center'>
				(Limited to the latest {$signup_views_limit|escape} signups)
			</div>
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

<section class='special-info npc-list'>
	<header><h2 id='npc-list-stats'>Npc list raw info</h2></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div class='npc-raw-info'>
		<div class='callout'>
			<div class='notice'>
				<p> These are npcs that are currently active and attackable.</p>
			</div>
		</div>
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
	<div>
		<div class='callout'>
			<div class='notice'>
				<p>These are items that exist in the game, though not all are obtainable by any means.</p>
			</div>
		</div>
{include file="ninjamaster.items.tpl"}
	</div>
</section>

{if $dupes}
	<section id='duplicate-ips' class='glassbox special-info'>
		<header><h3>Duplicate Ips</h3></header>
		<div class='text-center'>
			<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
		</div>
		<div>
			<p class='alert alert-info'>These are players who have logged in from the same IP address, and thus MAY be the same person/multiaccounters.</p>
			<p>Generally we want to allow the players to report and find multiplayers as it comes up.</p>
			<p class='alert alert-warning'>Currently a bug causes the load balancer to give all logins the load balancer's ip, so this list should not be trusted.</p>
			{foreach from=$dupes item='dupe'}
			<a href='/ninjamaster/?view={$dupe.player_id|escape}' class='char-name'>{$dupe.uname|escape}</a> :: IP <strong class='ip'>{$dupe.last_ip|escape}</strong> :: days {$dupe.days|escape}<br>
			{/foreach}
		</div>
	</section>
	{/if}

<section class='special-info'>
	<header><h2 id='services'>Services</h2></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div>
		<div class='text-centered'>
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
		</div>
	</div>
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
		<li><a class='' href='/ninjamaster/#clan-list'>Clans</a></li>
		<li><a class='' href='/ninjamaster/#api-epics'>Api Epics</a></li>
		<li><a class='' href='/ninjamaster/#services'>Infrastructure Services</a></li>
		<li><a class='' href='/epics'>UI Epics</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>


<script type='module' src='/js/ninjamaster.js'></script>


</div><!-- End of #admin-actions -->
